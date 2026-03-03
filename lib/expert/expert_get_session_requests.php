<?php
require_once '../db.php';

header('Content-Type: application/json');

// Check if user is expert or admin
$user = requireAuth();
if ($user['role'] !== 'expert' && $user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT 
            cs.*,
            u.name as client_name,
            u.email as client_email
        FROM consultation_sessions cs
        JOIN users u ON cs.client_id = u.id
        WHERE cs.expert_id = ?
        ORDER BY cs.session_date ASC
    ");
    
    $stmt->execute([$user['id']]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendSuccessResponse($sessions);
    
} catch (Exception $e) {
    sendErrorResponse('Error fetching sessions: ' . $e->getMessage());
}
