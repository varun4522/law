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

if (!isset($input['schedule']) || !is_array($input['schedule'])) {
    sendErrorResponse('Schedule array is required');
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Delete existing schedule
    $stmt = $conn->prepare("DELETE FROM expert_availability WHERE expert_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    
    // Insert new schedule
    $stmt = $conn->prepare("
        INSERT INTO expert_availability (expert_id, day_of_week, start_time, end_time, is_available)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $valid_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    foreach ($input['schedule'] as $slot) {
        if (!isset($slot['day']) || !isset($slot['start_time']) || !isset($slot['end_time'])) {
            $conn->rollback();
            sendErrorResponse('Each schedule slot must have day, start_time, and end_time');
        }
        
        if (!in_array($slot['day'], $valid_days)) {
            $conn->rollback();
            sendErrorResponse('Invalid day: ' . $slot['day']);
        }
        
        $is_available = isset($slot['is_available']) ? intval($slot['is_available']) : 1;
        
        $stmt->bind_param("isssi", 
            $user['id'], 
            $slot['day'], 
            $slot['start_time'], 
            $slot['end_time'], 
            $is_available
        );
        $stmt->execute();
    }
    
    $conn->commit();
    
    sendSuccessResponse('Availability schedule updated', [
        'slots_added' => count($input['schedule'])
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error updating schedule: ' . $e->getMessage());
}
