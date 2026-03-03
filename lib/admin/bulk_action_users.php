<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['user_ids'], $input['action'])) { sendErrorResponse('user_ids and action required'); }
$allowed = ['activate', 'deactivate', 'delete', 'verify'];
if (!in_array($input['action'], $allowed)) { sendErrorResponse('Invalid action'); }
try {
    $pdo = getDBConnection();
    $ids = array_map('intval', $input['user_ids']);
    $ph = implode(',', array_fill(0, count($ids), '?'));
    if ($input['action'] === 'delete') {
        $pdo->prepare("DELETE FROM users WHERE id IN ($ph) AND id != ?")->execute(array_merge($ids, [$user['id']]));
    } else {
        $col = match($input['action']) { 'activate' => 'is_active', 'deactivate' => 'is_active', 'verify' => 'is_verified' };
        $val = $input['action'] === 'deactivate' ? 0 : 1;
        $pdo->prepare("UPDATE users SET $col=? WHERE id IN ($ph)")->execute(array_merge([$val], $ids));
    }
    sendSuccessResponse('Bulk action done');
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
