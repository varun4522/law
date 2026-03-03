<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'sessions'; // sessions, revenue, recent
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

try {
    $conn = getDBConnection();
    
    $order_clause = "total_sessions DESC";
    if ($sort_by === 'revenue') {
        $order_clause = "total_revenue DESC";
    } elseif ($sort_by === 'recent') {
        $order_clause = "last_session_date DESC";
    }
    
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.phone,
            u.profile_image,
            COUNT(cs.id) as total_sessions,
            SUM(CASE WHEN cs.status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
            SUM(CASE WHEN cs.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_sessions,
            SUM(CASE WHEN cs.status = 'completed' THEN cs.amount ELSE 0 END) as total_revenue,
            AVG(CASE WHEN cs.rating IS NOT NULL THEN cs.rating ELSE NULL END) as avg_rating,
            MAX(cs.session_date) as last_session_date,
            MIN(cs.session_date) as first_session_date
        FROM users u
        INNER JOIN consultation_sessions cs ON u.id = cs.user_id
        WHERE cs.expert_id = ?
        GROUP BY u.id
        ORDER BY $order_clause
        LIMIT ?
    ");
    $stmt->bind_param("ii", $user['id'], $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    
    // Get overall stats
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT user_id) as total_unique_clients,
            COUNT(*) as total_sessions
        FROM consultation_sessions
        WHERE expert_id = ?
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    sendSuccessResponse('Client list retrieved', [
        'clients' => $clients,
        'count' => count($clients),
        'stats' => $stats,
        'sort_by' => $sort_by
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching clients: ' . $e->getMessage());
}
