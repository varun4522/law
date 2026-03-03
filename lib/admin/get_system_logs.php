<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$limit  = intval($_GET['limit']  ?? 100);
$offset = intval($_GET['offset'] ?? 0);
$type   = $_GET['type'] ?? 'all';
try {
    $pdo = getDBConnection();
    $where = $type !== 'all' ? 'WHERE log_type = ?' : 'WHERE 1=1';
    $params = $type !== 'all' ? [$type, $limit, $offset] : [$limit, $offset];
    $stmt = $pdo->prepare("SELECT sl.*, u.full_name FROM system_logs sl LEFT JOIN users u ON sl.user_id=u.id $where ORDER BY sl.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($params);
    sendSuccessResponse('Logs', ['logs' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
