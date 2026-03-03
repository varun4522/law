<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if (!isset($_GET['expert_id'])) {
    sendErrorResponse('Expert ID is required');
}

$expert_id = intval($_GET['expert_id']);
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    $conn = getDBConnection();
    
    // Get expert's availability schedule
    $stmt = $conn->prepare("
        SELECT * FROM expert_availability 
        WHERE expert_id = ? AND day_of_week = DAYNAME(?)
    ");
    $stmt->bind_param("is", $expert_id, $date);
    $stmt->execute();
    $availability = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get booked sessions for the date
    $stmt = $conn->prepare("
        SELECT session_date, duration 
        FROM consultation_sessions 
        WHERE expert_id = ? 
        AND DATE(session_date) = ? 
        AND status IN ('pending', 'confirmed')
    ");
    $stmt->bind_param("is", $expert_id, $date);
    $stmt->execute();
    $booked_sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Generate available time slots
    $slots = [];
    foreach ($availability as $avail) {
        $start_time = strtotime($date . ' ' . $avail['start_time']);
        $end_time = strtotime($date . ' ' . $avail['end_time']);
        $slot_duration = 60 * 60; // 1 hour slots
        
        for ($time = $start_time; $time < $end_time; $time += $slot_duration) {
            $slot_time = date('Y-m-d H:i:s', $time);
            $is_booked = false;
            
            // Check if slot is booked
            foreach ($booked_sessions as $booked) {
                $booked_start = strtotime($booked['session_date']);
                $booked_end = $booked_start + ($booked['duration'] * 60);
                
                if ($time >= $booked_start && $time < $booked_end) {
                    $is_booked = true;
                    break;
                }
            }
            
            $slots[] = [
                'time' => $slot_time,
                'display_time' => date('h:i A', $time),
                'is_available' => !$is_booked
            ];
        }
    }
    
    sendSuccessResponse('Availability retrieved', [
        'expert_id' => $expert_id,
        'date' => $date,
        'slots' => $slots,
        'total_slots' => count($slots),
        'available_slots' => count(array_filter($slots, function($s) { return $s['is_available']; }))
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching availability: ' . $e->getMessage());
}
