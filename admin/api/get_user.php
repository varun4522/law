<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);
$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$userId) {
    sendErrorResponse('User ID is required', 400);
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, full_name, email, phone, role, status, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    sendErrorResponse('User not found', 404);
}

sendSuccessResponse(['user' => $user]);
