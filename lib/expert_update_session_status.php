<?php
require_once 'db.php';

header('Content-Type: application/json');

$user = requireAuth();
if ($user['role'] !== 'expert' && $user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['session_id']) || !isset($data['status'])) {
    sendErrorResponse('Session ID and status are required');
}

$sessionId = $data['session_id'];
$newStatus = $data['status'];

if (!in_array($newStatus, ['confirmed', 'cancelled', 'completed'])) {
    sendErrorResponse('Invalid status');
}

try {
    $conn = getDBConnection();
    $conn->beginTransaction();
    
    // Get session details
    $stmt = $conn->prepare("SELECT * FROM consultation_sessions WHERE id = ? AND expert_id = ?");
    $stmt->execute([$sessionId, $user['id']]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        sendErrorResponse('Session not found or unauthorized');
    }
    
    // If cancelling, refund the client
    if ($newStatus === 'cancelled' && $session['status'] !== 'cancelled') {
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
        $stmt->execute([$session['amount'], $session['client_id']]);
        
        // Log refund transaction
        $stmt = $conn->prepare("
            INSERT INTO wallet_transactions (user_id, amount, transaction_type, description)
            VALUES (?, ?, 'credit', ?)
        ");
        $stmt->execute([
            $session['client_id'],
            $session['amount'],
            'Refund for cancelled session'
        ]);
        
        // Create notification
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, 'Session Cancelled', ?, 'session')
        ");
        $stmt->execute([
            $session['client_id'],
            'Your session has been cancelled and your payment has been refunded.'
        ]);
    }
    
    // If confirming, send notification
    if ($newStatus === 'confirmed') {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, 'Session Confirmed', ?, 'session')
        ");
        $stmt->execute([
            $session['client_id'],
            'Your consultation session has been confirmed!'
        ]);
    }
    
    // Update session status
    $stmt = $conn->prepare("UPDATE consultation_sessions SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $sessionId]);
    
    $conn->commit();
    
    sendSuccessResponse(['message' => 'Session status updated successfully']);
    
} catch (Exception $e) {
    $conn->rollBack();
    sendErrorResponse('Error updating session: ' . $e->getMessage());
}
