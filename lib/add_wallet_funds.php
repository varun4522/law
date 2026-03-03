<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['amount']) || $input['amount'] <= 0) {
    sendErrorResponse('Valid amount is required');
}

$amount = floatval($input['amount']);
$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $pdo->beginTransaction();
    
    // Update wallet balance
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
    $stmt->execute([$amount, $userId]);
    
    // Add transaction record
    $stmt = $pdo->prepare("INSERT INTO wallet_transactions (user_id, transaction_type, amount, description, status) VALUES (?, 'credit', ?, 'Wallet recharge', 'completed')");
    $stmt->execute([$userId, $amount]);
    
    $pdo->commit();
    
    sendSuccessResponse(null, 'Funds added successfully');
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Add funds error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
