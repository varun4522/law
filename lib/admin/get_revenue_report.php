<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$period = $_GET['period'] ?? 'month';
$date_cond = match($period) { 'week' => 'cs.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)', 'year' => 'YEAR(cs.created_at)=YEAR(CURDATE())', default => 'MONTH(cs.created_at)=MONTH(CURDATE()) AND YEAR(cs.created_at)=YEAR(CURDATE())' };
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount),0) as gross, COALESCE(SUM(amount*0.15),0) as commission, COUNT(*) as sessions FROM consultation_sessions WHERE status='completed' AND $date_cond");
    $summary = $stmt->fetch();
    $stmt = $pdo->query("SELECT DATE(session_date) as date, SUM(amount) as revenue, COUNT(*) as sessions FROM consultation_sessions WHERE status='completed' AND $date_cond GROUP BY DATE(session_date) ORDER BY date");
    sendSuccessResponse('Revenue', ['summary' => $summary, 'daily' => $stmt->fetchAll(), 'period' => $period]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
