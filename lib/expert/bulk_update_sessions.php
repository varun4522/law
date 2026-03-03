<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['session_ids'], $input['action'])) { sendErrorResponse('session_ids and action required'); }
$allowed = ['confirm', 'reject', 'complete'];
if (!in_array($input['action'], $allowed)) { sendErrorResponse('Invalid action'); }
$status_map = ['confirm' => 'confirmed', 'reject' => 'rejected', 'complete' => 'completed'];
try {
    $pdo = getDBConnection();
    $ids = array_map('intval', $input['session_ids']);
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $params = array_merge([$status_map[$input['action']]], $ids, [$user['id']]);
    $stmt = $pdo->prepare("UPDATE consultation_sessions SET status=? WHERE id IN ($ph) AND expert_id=?");
    $stmt->execute($params);
    sendSuccessResponse('Updated', ['affected' => $stmt->rowCount()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
