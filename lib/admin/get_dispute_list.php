<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$status = $_GET['status'] ?? 'open';
try {
    $pdo = getDBConnection();
    $where = $status !== 'all' ? "WHERE d.status = ?" : "WHERE 1=1";
    $params = $status !== 'all' ? [$status] : [];
    $stmt = $pdo->prepare("SELECT d.*, u.full_name as reporter_name, e.full_name as expert_name FROM disputes d LEFT JOIN users u ON d.user_id=u.id LEFT JOIN users e ON d.expert_id=e.id $where ORDER BY d.created_at DESC");
    $stmt->execute($params);
    sendSuccessResponse('Disputes', ['disputes' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
