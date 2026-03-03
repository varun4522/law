<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['full_name']) || empty(trim($input['full_name']))) {
    sendErrorResponse('Full name is required');
}

$fullName = trim($input['full_name']);
$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
    $stmt->execute([$fullName, $userId]);
    
    sendSuccessResponse(null, 'Profile updated successfully');
    
} catch (PDOException $e) {
    error_log("Update profile error: " . $e->getMessage());
    sendErrorResponse('An error occurred while updating profile', 500);
}
?>
