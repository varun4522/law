<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$status = $_GET['status'] ?? 'pending';
try {
    $pdo = getDBConnection();
    $where = $status !== 'all' ? 'WHERE cr.status = ?' : 'WHERE 1=1';
    $params = $status !== 'all' ? [$status] : [];
    $stmt = $pdo->prepare("SELECT cr.*, u.full_name as reporter_name FROM content_reports cr LEFT JOIN users u ON cr.reported_by=u.id $where ORDER BY cr.created_at DESC");
    $stmt->execute($params);
    sendSuccessResponse('Reports', ['reports' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
