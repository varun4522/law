<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$sessionId = isset($_POST['id']) ? intval($_POST['id']) : null;
$newStatus = isset($_POST['status']) ? trim($_POST['status']) : null;

if (!$sessionId || !$newStatus) {
    sendErrorResponse('Session ID and status are required', 400);
}

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("
        UPDATE consultation_sessions 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$newStatus, $sessionId]);

    logAdminAction($adminUser['id'], "Updated session ID: $sessionId to status: $newStatus");
    
    sendSuccessResponse(null, 'Session updated successfully');
} catch (Exception $e) {
    sendErrorResponse('Error updating session: ' . $e->getMessage(), 500);
}

function logAdminAction($adminId, $action) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$adminId, $action]);
    } catch (Exception $e) {
        // Log
    }
}
