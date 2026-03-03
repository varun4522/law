<?php
require_once '../db.php';
require_once 'payment_config.php';

header('Content-Type: application/json');

$user = requireAuth();

try {
    $payment_id = intval($_GET['payment_id'] ?? 0);
    
    if (!$payment_id) {
        throw new Exception('Payment ID is required');
    }
    
    // Get payment record
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            u.full_name,
            u.email
        FROM payments p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ? AND p.user_id = ?
    ");
    $stmt->execute([$payment_id, $user['id']]);
    $payment = $stmt->fetch();
    
    if (!$payment) {
        throw new Exception('Payment not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $payment
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
