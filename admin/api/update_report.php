<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$reportId = isset($_POST['id']) ? intval($_POST['id']) : null;
$newStatus = isset($_POST['status']) ? trim($_POST['status']) : null;

if (!$reportId || !$newStatus) {
    sendErrorResponse('Report ID and status are required', 400);
}

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("
        UPDATE content_reports 
        SET status = ?, resolved_by = ?, resolved_at = NOW(), updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$newStatus, $adminUser['id'], $reportId]);

    logAdminAction($adminUser['id'], "Updated report ID: $reportId to status: $newStatus");
    
    sendSuccessResponse(null, 'Report updated successfully');
} catch (Exception $e) {
    sendErrorResponse('Error updating report: ' . $e->getMessage(), 500);
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
