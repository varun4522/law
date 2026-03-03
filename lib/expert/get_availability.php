<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
$from = $_GET['from'] ?? date('Y-m-d');
$to   = $_GET['to']   ?? date('Y-m-d', strtotime('+30 days'));
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM expert_availability WHERE expert_id = ? AND available_date BETWEEN ? AND ? ORDER BY available_date, start_time");
    $stmt->execute([$user['id'], $from, $to]);
    sendSuccessResponse('Availability', ['slots' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
