<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if (!isset($_GET['expert_id'])) { sendErrorResponse('expert_id required'); }
$expert_id = intval($_GET['expert_id']);
$date = $_GET['date'] ?? date('Y-m-d');
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM expert_availability WHERE expert_id = ? AND available_date = ? AND is_booked = 0 ORDER BY start_time");
    $stmt->execute([$expert_id, $date]);
    $slots = $stmt->fetchAll();
    $stmt = $pdo->prepare("SELECT session_date, session_time FROM consultation_sessions WHERE expert_id = ? AND DATE(session_date) = ? AND status IN ('pending','confirmed')");
    $stmt->execute([$expert_id, $date]);
    $booked_sessions = $stmt->fetchAll();
    $stmt = $pdo->prepare("SELECT u.full_name, ep.specialization, ep.hourly_rate, ep.rating, ep.availability_status FROM users u LEFT JOIN expert_profiles ep ON u.id=ep.user_id WHERE u.id = ?");
    $stmt->execute([$expert_id]);
    $expert = $stmt->fetch();
    sendSuccessResponse('Availability', ['slots' => $slots, 'booked_times' => $booked_sessions, 'expert' => $expert, 'date' => $date]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
