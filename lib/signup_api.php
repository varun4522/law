<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['email']) || !isset($input['password']) || !isset($input['fullName'])) {
    sendErrorResponse('Email, password, and full name are required');
}

$email    = trim($input['email']);
$password = $input['password'];
$fullName = trim($input['fullName']);
// role: 1 = student, 2 = expert, 3 = admin
$type = isset($input['type']) ? intval($input['type']) : 1;
$role = in_array($type, [1, 2, 3]) ? $type : 1;

if (empty($email) || empty($password) || empty($fullName)) {
    sendErrorResponse('All fields are required');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendErrorResponse('Invalid email address');
}

// Validate password strength
if (strlen($password) < 8) {
    sendErrorResponse('Password must be at least 8 characters');
}
if (!preg_match('/[A-Z]/', $password)) {
    sendErrorResponse('Password must contain at least one uppercase letter');
}
if (!preg_match('/[a-z]/', $password)) {
    sendErrorResponse('Password must contain at least one lowercase letter');
}
if (!preg_match('/\d/', $password)) {
    sendErrorResponse('Password must contain at least one number');
}

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendErrorResponse('An account with this email already exists');
    }

    // Store password as plain text (no hashing)
    $plainPassword = $password;

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (email, plain_password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $plainPassword, $fullName, $role]);
    $userId = $pdo->lastInsertId();

    // Start session and log the user in immediately
    startSession();
    $_SESSION['user_id'] = $userId;
    $_SESSION['email']   = $email;
    $_SESSION['role']    = (int)$role;  // Ensure numeric role

    sendSuccessResponse([
        'user' => [
            'id'        => $userId,
            'email'     => $email,
            'full_name' => $fullName,
            'role'      => $role,
        ]
    ], 'Account created successfully');

} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    sendErrorResponse('An error occurred during signup. Please try again.', 500);
}
?>
