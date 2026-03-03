<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['slots']) || !is_array($input['slots'])) { sendErrorResponse('Slots array required'); }
try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    if (isset($input['clear_date'])) {
        $pdo->prepare("DELETE FROM expert_availability WHERE expert_id = ? AND available_date = ?")->execute([$user['id'], $input['clear_date']]);
    }
    $inserted = 0;
    foreach ($input['slots'] as $slot) {
        if (!isset($slot['date'], $slot['start_time'], $slot['end_time'])) continue;
        $pdo->prepare("INSERT IGNORE INTO expert_availability (expert_id, available_date, start_time, end_time, is_booked) VALUES (?,?,?,?,0)")
            ->execute([$user['id'], $slot['date'], $slot['start_time'], $slot['end_time']]);
        $inserted++;
    }
    $pdo->commit();
    sendSuccessResponse('Availability set', ['inserted' => $inserted]);
} catch (Exception $e) { $pdo->rollBack(); sendErrorResponse($e->getMessage()); }
