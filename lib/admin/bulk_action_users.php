<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_ids']) || !isset($input['action'])) {
    sendErrorResponse('User IDs array and action are required');
}

$user_ids = array_map('intval', $input['user_ids']);
$action = $input['action'];
$allowed_actions = ['activate', 'deactivate', 'delete', 'verify_expert', 'unverify_expert'];

if (!in_array($action, $allowed_actions)) {
    sendErrorResponse('Invalid action. Allowed: ' . implode(', ', $allowed_actions));
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    $success_count = 0;
    $failed = [];
    
    foreach ($user_ids as $user_id) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $target_user = $stmt->get_result()->fetch_assoc();
        
        if (!$target_user) {
            $failed[] = ['id' => $user_id, 'reason' => 'User not found'];
            continue;
        }
        
        // Don't allow actions on admins
        if ($target_user['role'] === 'admin' && $action === 'delete') {
            $failed[] = ['id' => $user_id, 'reason' => 'Cannot delete admin users'];
            continue;
        }
        
        switch ($action) {
            case 'activate':
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $success_count++;
                break;
                
            case 'deactivate':
                $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $success_count++;
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $success_count++;
                break;
                
            case 'verify_expert':
                if ($target_user['role'] !== 'expert') {
                    $failed[] = ['id' => $user_id, 'reason' => 'User is not an expert'];
                    continue 2;
                }
                $stmt = $conn->prepare("UPDATE expert_profiles SET verification_status = 'verified' WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $success_count++;
                break;
                
            case 'unverify_expert':
                if ($target_user['role'] !== 'expert') {
                    $failed[] = ['id' => $user_id, 'reason' => 'User is not an expert'];
                    continue 2;
                }
                $stmt = $conn->prepare("UPDATE expert_profiles SET verification_status = 'pending' WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $success_count++;
                break;
        }
    }
    
    $conn->commit();
    
    sendSuccessResponse('Bulk action completed', [
        'action' => $action,
        'total_requested' => count($user_ids),
        'success' => $success_count,
        'failed' => count($failed),
        'failed_details' => $failed
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Bulk action error: ' . $e->getMessage());
}
