<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['report_id'], $input['action'])) { sendErrorResponse('report_id and action required'); }
try {
    $pdo = getDBConnection();
    $pdo->prepare("UPDATE content_reports SET status=?, moderated_by=?, moderated_at=NOW() WHERE id=?")
        ->execute([$input['action'], $user['id'], intval($input['report_id'])]);
    sendSuccessResponse('Content moderated');
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
