<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['dispute_id']) || !isset($input['resolution']) || !isset($input['refund_amount'])) {
    sendErrorResponse('Dispute ID, resolution, and refund amount are required');
}

$dispute_id = intval($input['dispute_id']);
$resolution = $input['resolution']; // approved, rejected
$refund_amount = floatval($input['refund_amount']);
$admin_notes = isset($input['admin_notes']) ? trim($input['admin_notes']) : '';

if (!in_array($resolution, ['approved', 'rejected'])) {
    sendErrorResponse('Resolution must be "approved" or "rejected"');
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Get dispute details
    $stmt = $conn->prepare("
        SELECT d.*, cs.amount, cs.expert_id 
        FROM disputes d
        INNER JOIN consultation_sessions cs ON d.session_id = cs.id
        WHERE d.id = ?
    ");
    $stmt->bind_param("i", $dispute_id);
    $stmt->execute();
    $dispute = $stmt->get_result()->fetch_assoc();
    
    if (!$dispute) {
        $conn->rollback();
        sendErrorResponse('Dispute not found');
    }
    
    if ($dispute['status'] !== 'pending') {
        $conn->rollback();
        sendErrorResponse('Dispute already resolved');
    }
    
    // Update dispute
    $new_status = $resolution === 'approved' ? 'resolved' : 'rejected';
    $stmt = $conn->prepare("
        UPDATE disputes 
        SET status = ?, refund_amount = ?, admin_notes = ?, resolved_at = NOW(), resolved_by = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sdsii", $new_status, $refund_amount, $admin_notes, $user['id'], $dispute_id);
    $stmt->execute();
    
    if ($resolution === 'approved' && $refund_amount > 0) {
        // Refund user
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
        $stmt->bind_param("di", $refund_amount, $dispute['user_id']);
        $stmt->execute();
        
        // Create transaction
        $description = "Refund approved for dispute #$dispute_id";
        $ref = "DISPUTE_REFUND_$dispute_id";
        $status_complete = 'completed';
        
        $stmt = $conn->prepare("
            INSERT INTO wallet_transactions (user_id, transaction_type, amount, description, reference_id, status)
            VALUES (?, 'refund', ?, ?, ?, ?)
        ");
        $stmt->bind_param("idsss", $dispute['user_id'], $refund_amount, $description, $ref, $status_complete);
        $stmt->execute();
        
        // Notify user
        $notif_title = "Dispute Resolved - Refund Approved";
        $notif_message = "Your dispute has been resolved. A refund of ₹" . number_format($refund_amount, 2) . " has been credited to your wallet.";
        $notif_type = 'payment';
        
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $dispute['user_id'], $notif_title, $notif_message, $notif_type);
        $stmt->execute();
    } else {
        // Notify user of rejection
        $notif_title = "Dispute Rejected";
        $notif_message = "Your dispute has been reviewed and rejected. " . ($admin_notes ? "Reason: $admin_notes" : "");
        $notif_type = 'system';
        
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $dispute['user_id'], $notif_title, $notif_message, $notif_type);
        $stmt->execute();
    }
    
    $conn->commit();
    
    sendSuccessResponse('Dispute resolved', [
        'dispute_id' => $dispute_id,
        'resolution' => $resolution,
        'refund_amount' => $refund_amount
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error resolving dispute: ' . $e->getMessage());
}
