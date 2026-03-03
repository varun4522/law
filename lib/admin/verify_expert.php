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

if (!isset($input['expert_id']) || !isset($input['status'])) {
    sendErrorResponse('Expert ID and status (verified/rejected) are required');
}

$expert_id = intval($input['expert_id']);
$status = $input['status'];
$notes = isset($input['notes']) ? trim($input['notes']) : '';

if (!in_array($status, ['verified', 'rejected'])) {
    sendErrorResponse('Status must be "verified" or "rejected"');
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Verify expert exists
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE id = ? AND role = 'expert'");
    $stmt->bind_param("i", $expert_id);
    $stmt->execute();
    $expert = $stmt->get_result()->fetch_assoc();
    
    if (!$expert) {
        $conn->rollback();
        sendErrorResponse('Expert not found');
    }
    
    // Update verification status
    $stmt = $conn->prepare("
        UPDATE expert_profiles 
        SET verification_status = ?, verification_notes = ?, verified_at = NOW(), verified_by = ?
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssii", $status, $notes, $user['id'], $expert_id);
    $stmt->execute();
    
    // Send notification
    if ($status === 'verified') {
        $notif_title = "Expert Verification Approved";
        $notif_message = "Congratulations! Your expert profile has been verified. You can now receive consultation requests.";
    } else {
        $notif_title = "Expert Verification Rejected";
        $notif_message = "Your expert profile verification was not approved. " . ($notes ? "Reason: $notes" : "Please contact support for more details.");
    }
    
    $notif_type = 'system';
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $expert_id, $notif_title, $notif_message, $notif_type);
    $stmt->execute();
    
    // Log action
    $log_action = $status === 'verified' ? 'expert_verified' : 'expert_rejected';
    $log_details = json_encode([
        'expert_id' => $expert_id,
        'status' => $status,
        'notes' => $notes,
        'verified_by' => $user['id']
    ]);
    
    $stmt = $conn->prepare("
        INSERT INTO system_logs (user_id, action, details, ip_address)
        VALUES (?, ?, ?, ?)
    ");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("isss", $user['id'], $log_action, $log_details, $ip);
    $stmt->execute();
    
    $conn->commit();
    
    sendSuccessResponse('Expert verification updated', [
        'expert_id' => $expert_id,
        'status' => $status
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error updating verification: ' . $e->getMessage());
}
