<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

$period = isset($_GET['period']) ? $_GET['period'] : 'month';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

try {
    $conn = getDBConnection();
    
    if (!$date_from) {
        switch ($period) {
            case 'day':
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d');
                break;
            case 'week':
                $date_from = date('Y-m-d', strtotime('monday this week'));
                $date_to = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $date_from = date('Y-m-01');
                $date_to = date('Y-m-t');
                break;
            case 'year':
                $date_from = date('Y-01-01');
                $date_to = date('Y-12-31');
                break;
        }
    }
    
    // Total revenue and commissions
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as gross_revenue,
            SUM(CASE WHEN status = 'completed' THEN commission ELSE 0 END) as platform_commission,
            SUM(CASE WHEN status = 'completed' THEN (amount - commission) ELSE 0 END) as expert_payout,
            AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_session_value,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_sessions
        FROM consultation_sessions
        WHERE DATE(session_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $overview = $stmt->get_result()->fetch_assoc();
    
    // Revenue by expert
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            COUNT(cs.id) as sessions,
            SUM(cs.amount) as gross_revenue,
            SUM(cs.commission) as commission,
            SUM(cs.amount - cs.commission) as expert_earning
        FROM consultation_sessions cs
        INNER JOIN users u ON cs.expert_id = u.id
        WHERE cs.status = 'completed'
        AND DATE(cs.session_date) BETWEEN ? AND ?
        GROUP BY u.id
        ORDER BY commission DESC
        LIMIT 20
    ");
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $top_experts = [];
    while ($row = $result->fetch_assoc()) {
        $top_experts[] = $row;
    }
    
    // Daily revenue breakdown
    $stmt = $conn->prepare("
        SELECT 
            DATE(session_date) as date,
            COUNT(*) as sessions,
            SUM(amount) as revenue,
            SUM(commission) as commission
        FROM consultation_sessions
        WHERE status = 'completed'
        AND DATE(session_date) BETWEEN ? AND ?
        GROUP BY DATE(session_date)
        ORDER BY date
    ");
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $daily_revenue = [];
    while ($row = $result->fetch_assoc()) {
        $daily_revenue[] = $row;
    }
    
    // Wallet transactions summary
    $stmt = $conn->prepare("
        SELECT 
            transaction_type,
            COUNT(*) as count,
            SUM(amount) as total_amount
        FROM wallet_transactions
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY transaction_type
    ");
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallet_transactions = [];
    while ($row = $result->fetch_assoc()) {
        $wallet_transactions[] = $row;
    }
    
    // Revenue by session type
    $stmt = $conn->prepare("
        SELECT 
            session_type,
            COUNT(*) as count,
            SUM(amount) as revenue,
            AVG(amount) as avg_amount
        FROM consultation_sessions
        WHERE status = 'completed'
        AND DATE(session_date) BETWEEN ? AND ?
        GROUP BY session_type
    ");
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $revenue_by_type = [];
    while ($row = $result->fetch_assoc()) {
        $revenue_by_type[] = $row;
    }
    
    sendSuccessResponse('Revenue report generated', [
        'period' => $period,
        'date_from' => $date_from,
        'date_to' => $date_to,
        'overview' => $overview,
        'top_experts' => $top_experts,
        'daily_revenue' => $daily_revenue,
        'wallet_transactions' => $wallet_transactions,
        'revenue_by_type' => $revenue_by_type
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error generating report: ' . $e->getMessage());
}
