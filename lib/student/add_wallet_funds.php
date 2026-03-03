<?php
require_once '../db.php';
require_once '../payment/payment_config.php';

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

if ($amount < MIN_PAYMENT) {
    sendErrorResponse('Minimum amount is ₹' . MIN_PAYMENT);
}

if ($amount > MAX_PAYMENT) {
    sendErrorResponse('Maximum amount is ₹' . number_format(MAX_PAYMENT));
}

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    // Generate payment reference
    $payment_reference = generatePaymentReference();
    
    // Create payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (
            user_id, 
            amount, 
            payment_type, 
            payment_method, 
            payment_reference, 
            status, 
            created_at
        ) VALUES (?, ?, 'wallet_recharge', 'paytm', ?, 'pending', NOW())
    ");
    
    $stmt->execute([$userId, $amount, $payment_reference]);
    $payment_id = $pdo->lastInsertId();
    
    // Get payment details
    $payment_details = getPaymentDetails();
    
    sendSuccessResponse([
        'payment_id' => $payment_id,
        'payment_reference' => $payment_reference,
        'amount' => $amount,
        'paytm_number' => $payment_details['paytm_number'],
        'upi_id' => $payment_details['upi_id'],
        'payment_note' => $payment_details['payment_note'] . ' - ' . $payment_reference
    ], 'Complete payment and submit transaction ID');
    
} catch (PDOException $e) {
    error_log("Add funds error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
