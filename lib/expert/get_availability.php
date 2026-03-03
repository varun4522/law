<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT * FROM expert_availability 
        WHERE expert_id = ?
        ORDER BY 
            FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            start_time
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedule = [];
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
    
    // Group by day
    $grouped_schedule = [];
    foreach ($schedule as $slot) {
        $day = $slot['day_of_week'];
        if (!isset($grouped_schedule[$day])) {
            $grouped_schedule[$day] = [];
        }
        $grouped_schedule[$day][] = $slot;
    }
    
    sendSuccessResponse('Availability schedule retrieved', [
        'schedule' => $schedule,
        'grouped_schedule' => $grouped_schedule,
        'total_slots' => count($schedule)
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching schedule: ' . $e->getMessage());
}
