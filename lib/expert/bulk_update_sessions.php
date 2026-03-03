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

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['session_ids']) || !is_array($input['session_ids']) || !isset($input['action'])) {
    sendErrorResponse('Session IDs array and action are required');
}

$session_ids = array_map('intval', $input['session_ids']);
$action = $input['action'];
$allowed_actions = ['confirm', 'complete', 'cancel'];

if (!in_array($action, $allowed_actions)) {
    sendErrorResponse('Invalid action. Allowed: ' . implode(', ', $allowed_actions));
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    $updated = 0;
    $failed = [];
    
    foreach ($session_ids as $session_id) {
        // Verify session belongs to expert
        $stmt = $conn->prepare("SELECT id, status FROM consultation_sessions WHERE id = ? AND expert_id = ?");
        $stmt->bind_param("ii", $session_id, $user['id']);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            $failed[] = ['id' => $session_id, 'reason' => 'Not found or access denied'];
            continue;
        }
        
        // Validate status transition
        $valid = false;
        if ($action === 'confirm' && $session['status'] === 'pending') {
            $valid = true;
        } elseif ($action === 'complete' && $session['status'] === 'confirmed') {
            $valid = true;
        } elseif ($action === 'cancel' && in_array($session['status'], ['pending', 'confirmed'])) {
            $valid = true;
        }
        
        if (!$valid) {
            $failed[] = ['id' => $session_id, 'reason' => "Cannot $action session with status: {$session['status']}"];
            continue;
        }
        
        // Map action to status
        $new_status = $action === 'confirm' ? 'confirmed' : ($action === 'complete' ? 'completed' : 'cancelled');
        
        $stmt = $conn->prepare("UPDATE consultation_sessions SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $session_id);
        $stmt->execute();
        
        $updated++;
    }
    
    $conn->commit();
    
    sendSuccessResponse('Bulk update completed', [
        'total_requested' => count($session_ids),
        'updated' => $updated,
        'failed' => count($failed),
        'failed_details' => $failed
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Bulk update error: ' . $e->getMessage());
}
