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

if (!isset($input['commission_percentage'])) {
    sendErrorResponse('Commission percentage is required');
}

$commission_percentage = floatval($input['commission_percentage']);

if ($commission_percentage < 0 || $commission_percentage > 100) {
    sendErrorResponse('Commission must be between 0 and 100');
}

try {
    $conn = getDBConnection();
    
    // Update or insert platform settings
    $stmt = $conn->prepare("
        INSERT INTO platform_settings (setting_key, setting_value)
        VALUES ('commission_rate', ?)
        ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
    ");
    $stmt->bind_param("ss", $commission_percentage, $commission_percentage);
    $stmt->execute();
    
    // Log action
    $log_action = 'commission_rate_updated';
    $log_details = json_encode([
        'new_rate' => $commission_percentage,
        'updated_by' => $user['id']
    ]);
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("
        INSERT INTO system_logs (user_id, action, details, ip_address)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user['id'], $log_action, $log_details, $ip);
    $stmt->execute();
    
    sendSuccessResponse('Commission rate updated', [
        'commission_percentage' => $commission_percentage
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error updating commission: ' . $e->getMessage());
}
