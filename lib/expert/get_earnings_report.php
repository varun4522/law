<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
$period = $_GET['period'] ?? 'month';
$date_filter = match($period) { 'week' => 'DATE(cs.created_at) >= DATE_SUB(CURDATE(),INTERVAL 7 DAY)', 'year' => 'YEAR(cs.created_at) = YEAR(CURDATE())', default => 'MONTH(cs.created_at) = MONTH(CURDATE()) AND YEAR(cs.created_at) = YEAR(CURDATE())' };
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(cs.amount),0) as total_revenue, COUNT(*) as total_sessions, COUNT(CASE WHEN cs.status='completed' THEN 1 END) as completed_sessions, COALESCE(AVG(cs.amount),0) as avg_session_value FROM consultation_sessions cs WHERE cs.expert_id = ? AND cs.status='completed' AND $date_filter");
    $stmt->execute([$user['id']]);
    $summary = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT DATE(cs.session_date) as date, SUM(cs.amount) as revenue, COUNT(*) as sessions FROM consultation_sessions cs WHERE cs.expert_id = ? AND cs.status='completed' AND $date_filter GROUP BY DATE(cs.session_date) ORDER BY date");
    $stmt->execute([$user['id']]);
    sendSuccessResponse('Earnings', ['summary' => $summary, 'daily_breakdown' => $stmt->fetchAll(), 'period' => $period]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
