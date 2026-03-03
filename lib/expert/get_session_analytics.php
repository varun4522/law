<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) as confirmed, SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled, COALESCE(AVG(CASE WHEN status='completed' THEN amount END),0) as avg_amount FROM consultation_sessions WHERE expert_id = ?");
    $stmt->execute([$user['id']]);
    $stats = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT MONTHNAME(session_date) as month, COUNT(*) as sessions, SUM(CASE WHEN status='completed' THEN amount ELSE 0 END) as revenue FROM consultation_sessions WHERE expert_id = ? AND YEAR(session_date) = YEAR(CURDATE()) GROUP BY MONTH(session_date) ORDER BY MONTH(session_date)");
    $stmt->execute([$user['id']]);
    sendSuccessResponse('Analytics', ['stats' => $stats, 'monthly' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
