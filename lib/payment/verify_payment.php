<?php
require_once '../db.php';
require_once 'payment_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$user = requireAuth();
$data = json_decode(file_get_contents('php://input'), true);

try {
    $payment_id = intval($data['payment_id'] ?? 0);
    $transaction_id = $data['transaction_id'] ?? '';
    $screenshot = $data['screenshot'] ?? ''; // Base64 encoded screenshot
    
    if (!$payment_id) {
        throw new Exception('Payment ID is required');
    }
    
    // Get payment record
    $stmt = $pdo->prepare("
        SELECT * FROM payments 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$payment_id, $user['id']]);
    $payment = $stmt->fetch();
    
    if (!$payment) {
        throw new Exception('Payment not found');
    }
    
    if ($payment['status'] === PAYMENT_VERIFIED) {
        throw new Exception('Payment already verified');
    }
    
    // Update payment with transaction details
    $stmt = $pdo->prepare("
        UPDATE payments 
        SET transaction_id = ?,
            status = ?,
            screenshot = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([
        $transaction_id,
        PAYMENT_COMPLETED, // Admin will verify later
        $screenshot,
        $payment_id
    ]);
    
    // If it's a wallet recharge, add funds to wallet (after admin verification)
    // This will be done by admin panel
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment submitted for verification. Your wallet will be updated after admin approval.',
        'payment_id' => $payment_id,
        'status' => PAYMENT_COMPLETED
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
