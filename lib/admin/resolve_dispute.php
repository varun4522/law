<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['dispute_id'], $input['resolution'])) { sendErrorResponse('dispute_id and resolution required'); }
try {
    $pdo = getDBConnection();
    $pdo->prepare("UPDATE disputes SET status='resolved', resolution=?, resolved_by=?, resolved_at=NOW() WHERE id=?")
        ->execute([$input['resolution'], $user['id'], intval($input['dispute_id'])]);
    sendSuccessResponse('Dispute resolved');
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
