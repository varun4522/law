<?php
require_once '../db.php';
require_once 'payment_config.php';

header('Content-Type: application/json');

$user = requireAuth();

try {
    // Get all payments for current user
    $stmt = $pdo->prepare("
        SELECT 
            id,
            amount,
            payment_type,
            payment_method,
            payment_reference,
            transaction_id,
            status,
            created_at,
            updated_at
        FROM payments
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$user['id']]);
    $payments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $payments
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
