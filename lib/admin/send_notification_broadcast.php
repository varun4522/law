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

if (!isset($input['user_ids']) || !isset($input['title']) || !isset($input['message'])) {
    sendErrorResponse('User IDs array, title, and message are required');
}

$user_ids = array_map('intval', $input['user_ids']);
$title = trim($input['title']);
$message = trim($input['message']);
$type = isset($input['type']) ? $input['type'] : 'system';
$link = isset($input['link']) ? trim($input['link']) : '';

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type, link)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $sent_count = 0;
    $failed = [];
    
    foreach ($user_ids as $user_id) {
        // Verify user exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        
        if (!$check_stmt->get_result()->fetch_assoc()) {
            $failed[] = ['id' => $user_id, 'reason' => 'User not found'];
            continue;
        }
        
        $stmt->bind_param("issss", $user_id, $title, $message, $type, $link);
        $stmt->execute();
        $sent_count++;
    }
    
    // Log broadcast
    $log_action = 'notification_broadcast';
    $log_details = json_encode([
        'title' => $title,
        'recipient_count' => count($user_ids),
        'sent_count' => $sent_count,
        'type' => $type,
        'sent_by' => $user['id']
    ]);
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $log_stmt = $conn->prepare("
        INSERT INTO system_logs (user_id, action, details, ip_address)
        VALUES (?, ?, ?, ?)
    ");
    $log_stmt->bind_param("isss", $user['id'], $log_action, $log_details, $ip);
    $log_stmt->execute();
    
    $conn->commit();
    
    sendSuccessResponse('Notifications sent', [
        'total_requested' => count($user_ids),
        'sent' => $sent_count,
        'failed' => count($failed),
        'failed_details' => $failed
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error sending notifications: ' . $e->getMessage());
}
