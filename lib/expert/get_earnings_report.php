<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

$period = isset($_GET['period']) ? $_GET['period'] : 'month'; // day, week, month, year, all
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

try {
    $conn = getDBConnection();
    
    // Calculate date range based on period
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
            case 'all':
                $date_from = '2000-01-01';
                $date_to = date('Y-m-d');
                break;
        }
    }
    
    // Get earnings summary
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_sessions,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as gross_earnings,
            SUM(CASE WHEN status = 'completed' THEN commission ELSE 0 END) as total_commission,
            SUM(CASE WHEN status = 'completed' THEN (amount - commission) ELSE 0 END) as net_earnings,
            AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_session_amount,
            AVG(CASE WHEN rating IS NOT NULL THEN rating ELSE NULL END) as avg_rating
        FROM consultation_sessions
        WHERE expert_id = ?
        AND DATE(session_date) BETWEEN ? AND ?
    ");
    $stmt->bind_param("iss", $user['id'], $date_from, $date_to);
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc();
    
    // Get daily breakdown
    $stmt = $conn->prepare("
        SELECT 
            DATE(session_date) as date,
            COUNT(*) as sessions,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as earnings,
            SUM(CASE WHEN status = 'completed' THEN commission ELSE 0 END) as commission,
            SUM(CASE WHEN status = 'completed' THEN (amount - commission) ELSE 0 END) as net
        FROM consultation_sessions
        WHERE expert_id = ?
        AND DATE(session_date) BETWEEN ? AND ?
        GROUP BY DATE(session_date)
        ORDER BY date DESC
    ");
    $stmt->bind_param("iss", $user['id'], $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $daily_breakdown = [];
    while ($row = $result->fetch_assoc()) {
        $daily_breakdown[] = $row;
    }
    
    // Get top clients
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.profile_image,
            COUNT(*) as total_sessions,
            SUM(cs.amount) as total_spent,
            AVG(cs.rating) as avg_rating
        FROM consultation_sessions cs
        INNER JOIN users u ON cs.user_id = u.id
        WHERE cs.expert_id = ?
        AND cs.status = 'completed'
        AND DATE(cs.session_date) BETWEEN ? AND ?
        GROUP BY u.id
        ORDER BY total_sessions DESC
        LIMIT 10
    ");
    $stmt->bind_param("iss", $user['id'], $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $top_clients = [];
    while ($row = $result->fetch_assoc()) {
        $top_clients[] = $row;
    }
    
    sendSuccessResponse('Earnings report generated', [
        'period' => $period,
        'date_from' => $date_from,
        'date_to' => $date_to,
        'summary' => $summary,
        'daily_breakdown' => $daily_breakdown,
        'top_clients' => $top_clients
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error generating report: ' . $e->getMessage());
}
