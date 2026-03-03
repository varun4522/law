<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['password']) || empty($input['password'])) {
    sendErrorResponse('New password is required');
}

$password = $input['password'];

// Validate password strength
if (strlen($password) < 6) {
    sendErrorResponse('Password must be at least 6 characters long');
}

$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);
    
    sendSuccessResponse(null, 'Password changed successfully');
    
} catch (PDOException $e) {
    error_log("Change password error: " . $e->getMessage());
    sendErrorResponse('An error occurred while changing password', 500);
}
?>
