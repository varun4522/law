<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userId) {
    sendErrorResponse('User ID is required', 400);
}

$pdo = getDBConnection();

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        sendErrorResponse('User not found', 404);
    }

    // Delete expert profile if exists
    $stmt = $pdo->prepare("DELETE FROM expert_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    logAdminAction($adminUser['id'], "Deleted user ID: $userId");
    
    sendSuccessResponse(null, 'User deleted successfully');
} catch (Exception $e) {
    sendErrorResponse('Error deleting user: ' . $e->getMessage(), 500);
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
