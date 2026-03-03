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
    $stmt = $pdo->prepare("
        SELECT id, transaction_type, amount, description, reference_id, status, created_at
        FROM wallet_transactions
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 100
    ");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll();
    
    sendSuccessResponse($transactions);
    
} catch (PDOException $e) {
    error_log("Get transactions error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
