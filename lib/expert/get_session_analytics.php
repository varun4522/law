<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

try {
    $conn = getDBConnection();
    
    // Get overall performance metrics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            AVG(CASE WHEN rating IS NOT NULL THEN rating ELSE NULL END) as avg_rating,
            COUNT(DISTINCT user_id) as unique_clients,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_earnings
        FROM consultation_sessions
        WHERE expert_id = ?
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $overall = $stmt->get_result()->fetch_assoc();
    
    // Calculate completion rate
    $overall['completion_rate'] = $overall['total_sessions'] > 0 
        ? round(($overall['completed'] / $overall['total_sessions']) * 100, 2) 
        : 0;
    
    // Get monthly trend (last 12 months)
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(session_date, '%Y-%m') as month,
            COUNT(*) as sessions,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            AVG(CASE WHEN rating IS NOT NULL THEN rating ELSE NULL END) as avg_rating,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as earnings
        FROM consultation_sessions
        WHERE expert_id = ?
        AND session_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(session_date, '%Y-%m')
        ORDER BY month DESC
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $monthly_trend = [];
    while ($row = $result->fetch_assoc()) {
        $monthly_trend[] = $row;
    }
    
    // Get session type distribution
    $stmt = $conn->prepare("
        SELECT 
            session_type,
            COUNT(*) as count,
            AVG(amount) as avg_amount
        FROM consultation_sessions
        WHERE expert_id = ?
        AND status = 'completed'
        GROUP BY session_type
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $session_types = [];
    while ($row = $result->fetch_assoc()) {
        $session_types[] = $row;
    }
    
    // Get rating distribution
    $stmt = $conn->prepare("
        SELECT 
            rating,
            COUNT(*) as count
        FROM consultation_sessions
        WHERE expert_id = ?
        AND rating IS NOT NULL
        GROUP BY rating
        ORDER BY rating DESC
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rating_distribution = [];
    while ($row = $result->fetch_assoc()) {
        $rating_distribution[] = $row;
    }
    
    // Get busiest days
    $stmt = $conn->prepare("
        SELECT 
            DAYNAME(session_date) as day,
            COUNT(*) as sessions
        FROM consultation_sessions
        WHERE expert_id = ?
        GROUP BY DAYNAME(session_date)
        ORDER BY sessions DESC
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $busiest_days = [];
    while ($row = $result->fetch_assoc()) {
        $busiest_days[] = $row;
    }
    
    sendSuccessResponse('Analytics retrieved', [
        'overall' => $overall,
        'monthly_trend' => $monthly_trend,
        'session_types' => $session_types,
        'rating_distribution' => $rating_distribution,
        'busiest_days' => $busiest_days
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching analytics: ' . $e->getMessage());
}
