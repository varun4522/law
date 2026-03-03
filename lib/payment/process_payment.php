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
    $amount = floatval($data['amount'] ?? 0);
    $payment_type = $data['payment_type'] ?? 'wallet_recharge'; // wallet_recharge, session_payment
    $reference_id = $data['reference_id'] ?? null;
    $transaction_id = $data['transaction_id'] ?? '';
    
    // Validate amount
    if ($amount < MIN_PAYMENT || $amount > MAX_PAYMENT) {
        throw new Exception('Invalid payment amount. Must be between ₹' . MIN_PAYMENT . ' and ₹' . MAX_PAYMENT);
    }
    
    // Generate payment reference
    $payment_reference = generatePaymentReference();
    
    // Insert payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (
            user_id, 
            amount, 
            payment_type, 
            payment_method, 
            payment_reference, 
            transaction_id,
            reference_id,
            status, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $user['id'],
        $amount,
        $payment_type,
        'paytm',
        $payment_reference,
        $transaction_id,
        $reference_id,
        PAYMENT_PENDING
    ]);
    
    $payment_id = $pdo->lastInsertId();
    
    // Get payment details
    $payment_details = getPaymentDetails();
    
    echo json_encode([
        'success' => true,
        'payment_id' => $payment_id,
        'payment_reference' => $payment_reference,
        'amount' => $amount,
        'paytm_number' => $payment_details['paytm_number'],
        'upi_id' => $payment_details['upi_id'],
        'payment_note' => $payment_details['payment_note'] . ' - Ref: ' . $payment_reference,
        'message' => 'Please complete payment to Paytm number: ' . $payment_details['paytm_number']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
