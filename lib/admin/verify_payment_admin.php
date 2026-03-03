<?php
require_once '../db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$user = requireAuth();

// Check if user is admin
if ($user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['payment_id']) || !isset($input['action'])) {
    sendErrorResponse('Payment ID and action are required');
}

$payment_id = intval($input['payment_id']);
$action = $input['action']; // 'approve' or 'reject'
$admin_notes = $input['admin_notes'] ?? '';

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $pdo->beginTransaction();
    
    // Get payment details
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        throw new Exception('Payment not found');
    }
    
    if ($payment['status'] === 'verified' || $payment['status'] === 'failed') {
        throw new Exception('Payment already processed');
    }
    
    if ($action === 'approve') {
        // Update payment status
        $stmt = $pdo->prepare("
            UPDATE payments 
            SET status = 'verified', 
                verified_by = ?, 
                verified_at = NOW(),
                admin_notes = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$user['id'], $admin_notes, $payment_id]);
        
        // Credit wallet
        $stmt = $pdo->prepare("
            UPDATE users 
            SET wallet_balance = wallet_balance + ? 
            WHERE id = ?
        ");
        $stmt->execute([$payment['amount'], $payment['user_id']]);
        
        // Add wallet transaction
        $stmt = $pdo->prepare("
            INSERT INTO wallet_transactions 
            (user_id, transaction_type, amount, description, status, created_at) 
            VALUES (?, 'credit', ?, ?, 'completed', NOW())
        ");
        $description = 'Wallet recharge - ' . $payment['payment_reference'];
        $stmt->execute([$payment['user_id'], $payment['amount'], $description]);
        
        $message = 'Payment approved and wallet credited';
        
    } else if ($action === 'reject') {
        // Update payment status
        $stmt = $pdo->prepare("
            UPDATE payments 
            SET status = 'failed', 
                verified_by = ?, 
                verified_at = NOW(),
                admin_notes = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$user['id'], $admin_notes, $payment_id]);
        
        $message = 'Payment rejected';
        
    } else {
        throw new Exception('Invalid action');
    }
    
    $pdo->commit();
    
    sendSuccessResponse(null, $message);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Verify payment error: " . $e->getMessage());
    sendErrorResponse($e->getMessage(), 500);
}
?>
