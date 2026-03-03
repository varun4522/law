<?php
require_once '../db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

try {
    if ($userRole === 'expert' || $userRole === 'admin') {
        // Get sessions where user is the expert
        $stmt = $pdo->prepare("
            SELECT cs.*, u.full_name as client_name, u.email as client_email
            FROM consultation_sessions cs
            INNER JOIN users u ON cs.user_id = u.id
            WHERE cs.expert_id = ?
            ORDER BY cs.session_date DESC
        ");
    } else {
        // Get sessions where user is the client
        $stmt = $pdo->prepare("
            SELECT cs.*, u.full_name as expert_name, u.email as expert_email, ep.specialization
            FROM consultation_sessions cs
            INNER JOIN users u ON cs.expert_id = u.id
            LEFT JOIN expert_profiles ep ON u.id = ep.user_id
            WHERE cs.user_id = ?
            ORDER BY cs.session_date DESC
        ");
    }
    
    $stmt->execute([$userId]);
    $sessions = $stmt->fetchAll();
    
    sendSuccessResponse($sessions);
    
} catch (PDOException $e) {
    error_log("Get sessions error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
