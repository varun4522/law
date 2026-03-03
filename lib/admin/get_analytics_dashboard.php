<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

try {
    $conn = getDBConnection();
    
    // User statistics
    $stmt = $conn->prepare("
        SELECT 
            role,
            COUNT(*) as count,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN YEARWEEK(created_at) = YEARWEEK(NOW()) THEN 1 ELSE 0 END) as this_week,
            SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 ELSE 0 END) as this_month
        FROM users
        GROUP BY role
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $user_stats = [];
    $total_users = 0;
    while ($row = $result->fetch_assoc()) {
        $user_stats[$row['role']] = $row;
        $total_users += $row['count'];
    }
    
    // Session statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN DATE(session_date) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN YEARWEEK(session_date) = YEARWEEK(NOW()) THEN 1 ELSE 0 END) as this_week,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status = 'completed' THEN commission ELSE 0 END) as total_commission
        FROM consultation_sessions
    ");
    $stmt->execute();
    $session_stats = $stmt->get_result()->fetch_assoc();
    
    // Revenue by month (last 12 months)
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(session_date, '%Y-%m') as month,
            SUM(amount) as revenue,
            SUM(commission) as commission,
            COUNT(*) as sessions
        FROM consultation_sessions
        WHERE status = 'completed'
        AND session_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(session_date, '%Y-%m')
        ORDER BY month DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $monthly_revenue = [];
    while ($row = $result->fetch_assoc()) {
        $monthly_revenue[] = $row;
    }
    
    // Forum statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_questions,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = 'answered' THEN 1 ELSE 0 END) as answered,
            SUM(views) as total_views
        FROM forum_questions
    ");
    $stmt->execute();
    $forum_stats = $stmt->get_result()->fetch_assoc();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total_answers FROM forum_answers");
    $stmt->execute();
    $forum_stats['total_answers'] = $stmt->get_result()->fetch_assoc()['total_answers'];
    
    // Wallet statistics
    $stmt = $conn->prepare("
        SELECT 
            SUM(wallet_balance) as total_wallet_balance
        FROM users
    ");
    $stmt->execute();
    $wallet_stats = $stmt->get_result()->fetch_assoc();
    
    $stmt = $conn->prepare("
        SELECT 
            transaction_type,
            COUNT(*) as count,
            SUM(amount) as total_amount
        FROM wallet_transactions
        WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())
        GROUP BY transaction_type
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallet_stats['transactions'] = [];
    while ($row = $result->fetch_assoc()) {
        $wallet_stats['transactions'][$row['transaction_type']] = $row;
    }
    
    // Top experts by revenue
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.profile_image,
            ep.specialization,
            COUNT(cs.id) as sessions,
            SUM(cs.amount) as revenue,
            ep.rating
        FROM users u
        INNER JOIN expert_profiles ep ON u.id = ep.user_id
        LEFT JOIN consultation_sessions cs ON u.id = cs.expert_id AND cs.status = 'completed'
        WHERE u.role = 'expert'
        GROUP BY u.id
        ORDER BY revenue DESC
        LIMIT 10
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $top_experts = [];
    while ($row = $result->fetch_assoc()) {
        $top_experts[] = $row;
    }
    
    // Recent activities
    $stmt = $conn->prepare("
        SELECT 
            'session' as type,
            cs.id as item_id,
            cs.created_at,
            u.full_name as user_name,
            e.full_name as expert_name,
            cs.status
        FROM consultation_sessions cs
        INNER JOIN users u ON cs.user_id = u.id
        INNER JOIN users e ON cs.expert_id = e.id
        ORDER BY cs.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    sendSuccessResponse('Admin dashboard analytics', [
        'total_users' => $total_users,
        'user_stats' => $user_stats,
        'session_stats' => $session_stats,
        'monthly_revenue' => $monthly_revenue,
        'forum_stats' => $forum_stats,
        'wallet_stats' => $wallet_stats,
        'top_experts' => $top_experts,
        'recent_activities' => $recent_activities,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error generating dashboard: ' . $e->getMessage());
}
