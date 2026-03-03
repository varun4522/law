<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if (!isset($_GET['session_id'])) {
    sendErrorResponse('Session ID is required');
}

$session_id = intval($_GET['session_id']);

try {
    $conn = getDBConnection();
    
    // Verify user has access to this session
    $stmt = $conn->prepare("
        SELECT id FROM consultation_sessions 
        WHERE id = ? AND (user_id = ? OR expert_id = ?)
    ");
    $stmt->bind_param("iii", $session_id, $user['id'], $user['id']);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        sendErrorResponse('Session not found or access denied');
    }
    
    // Get documents
    $stmt = $conn->prepare("
        SELECT 
            sd.*,
            u.full_name as uploaded_by_name,
            u.role as uploaded_by_role
        FROM session_documents sd
        INNER JOIN users u ON sd.uploaded_by = u.id
        WHERE sd.session_id = ?
        ORDER BY sd.created_at DESC
    ");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $documents = [];
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
    
    sendSuccessResponse('Documents retrieved', [
        'documents' => $documents,
        'count' => count($documents)
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching documents: ' . $e->getMessage());
}
