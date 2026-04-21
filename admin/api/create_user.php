<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = intval($_POST['role'] ?? ROLE_STUDENT);
$status = $_POST['status'] ?? 'active';
$password = $_POST['password'] ?? '';

if (empty($fullName) || empty($email)) {
    sendErrorResponse('Full name and email are required', 400);
}

// Check if email already exists
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    sendErrorResponse('Email already exists', 400);
}

// Generate password if not provided
if (empty($password)) {
    $password = bin2hex(random_bytes(4));
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, phone, role, status, password, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([$fullName, $email, $phone, $role, $status, $passwordHash]);
    
    $userId = $pdo->lastInsertId();
    
    // Log admin action
    logAdminAction($adminUser['id'], "Created user: $fullName (ID: $userId)");
    
    sendSuccessResponse(['user_id' => $userId], 'User created successfully');
} catch (Exception $e) {
    sendErrorResponse('Error creating user: ' . $e->getMessage(), 500);
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
        // Log table might not exist, ignore
    }
}
