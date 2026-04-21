<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userId) {
    sendErrorResponse('User ID is required', 400);
}

$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = intval($_POST['role'] ?? ROLE_STUDENT);
$status = $_POST['status'] ?? 'active';

if (empty($fullName)) {
    sendErrorResponse('Full name is required', 400);
}

$pdo = getDBConnection();

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$userId]);
if (!$stmt->fetch()) {
    sendErrorResponse('User not found', 404);
}

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, phone = ?, role = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$fullName, $phone, $role, $status, $userId]);
    
    logAdminAction($adminUser['id'], "Updated user ID: $userId");
    
    sendSuccessResponse(null, 'User updated successfully');
} catch (Exception $e) {
    sendErrorResponse('Error updating user: ' . $e->getMessage(), 500);
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
        // Log table might not exist
    }
}
