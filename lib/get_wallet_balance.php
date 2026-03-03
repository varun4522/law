<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    if (!$result) {
        sendErrorResponse('User not found', 404);
    }
    
    sendSuccessResponse(['balance' => $result['wallet_balance']]);
    
} catch (PDOException $e) {
    error_log("Get wallet balance error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
