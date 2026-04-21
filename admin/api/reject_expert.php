<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$expertId = isset($_POST['id']) ? intval($_POST['id']) : null;
$reason = trim($_POST['reason'] ?? '');

if (!$expertId) {
    sendErrorResponse('Expert ID is required', 400);
}

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("
        UPDATE expert_profiles 
        SET verification_status = 'rejected', rejection_reason = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$reason, $expertId]);

    logAdminAction($adminUser['id'], "Rejected expert ID: $expertId - Reason: $reason");
    
    sendSuccessResponse(null, 'Expert rejected successfully');
} catch (Exception $e) {
    sendErrorResponse('Error rejecting expert: ' . $e->getMessage(), 500);
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
