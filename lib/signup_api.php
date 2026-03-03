<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['email', 'password', 'fullName', 'type'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || (is_string($input[$field]) && empty(trim($input[$field])))) {
        sendErrorResponse(ucfirst($field) . ' is required');
    }
}

$email    = trim($input['email']);
$password = $input['password'];
$fullName = trim($input['fullName']);
$type     = intval($input['type']);

// Map numeric type to role
$typeMap = [1 => 'user', 2 => 'expert', 3 => 'admin'];
if (!array_key_exists($type, $typeMap)) {
    sendErrorResponse('Invalid type. Use 1 (User), 2 (Expert), or 3 (Admin)');
}
$role = $typeMap[$type];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendErrorResponse('Invalid email format');
}

// Validate password strength
if (strlen($password) < 8) {
    sendErrorResponse('Password must be at least 8 characters long');
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

// Validate role (already resolved from type above)

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendErrorResponse('Email already exists');
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $hashedPassword, $fullName, $role]);
    
    $userId = $pdo->lastInsertId();
    
    // Create expert profile row if role is expert
    if ($role === 'expert') {
        $pdo->prepare("INSERT INTO expert_profiles (user_id, verification_status, availability_status) VALUES (?, 'pending', 'available')")
            ->execute([$userId]);
    }
    
    sendSuccessResponse([
        'user' => [
            'id' => $userId,
            'email' => $email,
            'full_name' => $fullName,
            'role' => $role
        ]
    ], 'Account created successfully');
    
} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    sendErrorResponse('An error occurred during signup', 500);
}
?>
