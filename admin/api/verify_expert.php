<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$expertId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$expertId) {
    sendErrorResponse('Expert ID is required', 400);
}

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("
        UPDATE expert_profiles 
        SET verification_status = 'verified', updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$expertId]);

    logAdminAction($adminUser['id'], "Verified expert ID: $expertId");
    
    sendSuccessResponse(null, 'Expert verified successfully');
} catch (Exception $e) {
    sendErrorResponse('Error verifying expert: ' . $e->getMessage(), 500);
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
