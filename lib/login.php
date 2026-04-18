<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email']) || !isset($input['password'])) {
    sendErrorResponse('Email and password are required');
}

$email = trim($input['email']);
$password = $input['password'];

if (empty($email) || empty($password)) {
    sendErrorResponse('Email and password cannot be empty');
}

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    // Find user by email
    $stmt = $pdo->prepare("SELECT id, email, plain_password, full_name, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Compare plain passwords (no hashing)
    if (!$user || $password !== $user['plain_password']) {
        sendErrorResponse('Invalid email or password', 401);
    }
    
    // Start session
    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    // Return user data (without password)
    sendSuccessResponse([
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ]
    ], 'Login successful');
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    sendErrorResponse('An error occurred during login', 500);
}
?>
