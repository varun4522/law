<?php
require_once '../db.php';

header('Content-Type: application/json');

// Check if user is admin
$user = requireAuth();
if ($user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

try {
    $conn = getDBConnection();
    
    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total experts
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'expert'");
    $totalExperts = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total sessions
    $stmt = $conn->query("SELECT COUNT(*) as count FROM consultation_sessions");
    $totalSessions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total revenue
    $stmt = $conn->query("SELECT SUM(amount) as total FROM wallet_transactions WHERE transaction_type = 'debit'");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Get pending sessions
    $stmt = $conn->query("SELECT COUNT(*) as count FROM consultation_sessions WHERE status = 'pending'");
    $pendingSessions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get forum questions
    $stmt = $conn->query("SELECT COUNT(*) as count FROM forum_questions");
    $forumQuestions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    sendSuccessResponse([
        'total_users' => $totalUsers,
        'total_experts' => $totalExperts,
        'total_sessions' => $totalSessions,
        'total_revenue' => $totalRevenue,
        'pending_sessions' => $pendingSessions,
        'forum_questions' => $forumQuestions
    ]);
    
} catch (Exception $e) {
    sendErrorResponse('Error fetching statistics: ' . $e->getMessage());
}
