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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['session_id'])) {
    sendErrorResponse('Session ID is required');
}

$session_id = intval($input['session_id']);
$reason = isset($input['reason']) ? trim($input['reason']) : 'No reason provided';

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Get session details
    $stmt = $conn->prepare("
        SELECT cs.*, u.full_name as user_name, e.full_name as expert_name
        FROM consultation_sessions cs
        INNER JOIN users u ON cs.user_id = u.id
        INNER JOIN users e ON cs.expert_id = e.id
        WHERE cs.id = ? AND cs.user_id = ?
    ");
    $stmt->bind_param("ii", $session_id, $user['id']);
    $stmt->execute();
    $session = $stmt->get_result()->fetch_assoc();
    
    if (!$session) {
        $conn->rollback();
        sendErrorResponse('Session not found');
    }
    
    if (!in_array($session['status'], ['pending', 'confirmed'])) {
        $conn->rollback();
        sendErrorResponse('Only pending or confirmed sessions can be cancelled');
    }
    
    // Check if session is within 24 hours
    $session_time = strtotime($session['session_date']);
    $now = time();
    $hours_until = ($session_time - $now) / 3600;
    
    $refund_amount = 0;
    $refund_percentage = 0;
    
    // Refund policy: 
    // - More than 24 hours: 100% refund
    // - 12-24 hours: 50% refund
    // - Less than 12 hours: No refund
    if ($hours_until > 24) {
        $refund_percentage = 100;
        $refund_amount = $session['amount'];
    } elseif ($hours_until > 12) {
        $refund_percentage = 50;
        $refund_amount = $session['amount'] * 0.5;
    }
    
    // Update session status
    $stmt = $conn->prepare("
        UPDATE consultation_sessions 
        SET status = 'cancelled', notes = CONCAT(IFNULL(notes, ''), '\nCancelled by user: ', ?)
        WHERE id = ?
    ");
    $stmt->bind_param("si", $reason, $session_id);
    $stmt->execute();
    
    // Process refund if applicable
    if ($refund_amount > 0) {
        // Refund to user
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
        $stmt->bind_param("di", $refund_amount, $user['id']);
        $stmt->execute();
        
        // Create transaction record
        $description = "Refund for cancelled session (ID: $session_id) - $refund_percentage% refund";
        $ref = "REFUND_SESSION_$session_id";
        $status = 'completed';
        
        $stmt = $conn->prepare("
            INSERT INTO wallet_transactions (user_id, transaction_type, amount, description, reference_id, status)
            VALUES (?, 'refund', ?, ?, ?, ?)
        ");
        $stmt->bind_param("idsss", $user['id'], $refund_amount, $description, $ref, $status);
        $stmt->execute();
    }
    
    // Notify expert
    $notif_title = "Session Cancelled";
    $notif_message = "{$session['user_name']} has cancelled the session scheduled for " . date('M d, Y h:i A', $session_time);
    $notif_type = 'session';
    $notif_link = '/expert_dashboard.php';
    
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type, link)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $session['expert_id'], $notif_title, $notif_message, $notif_type, $notif_link);
    $stmt->execute();
    
    $conn->commit();
    
    sendSuccessResponse('Session cancelled successfully', [
        'session_id' => $session_id,
        'refund_amount' => $refund_amount,
        'refund_percentage' => $refund_percentage,
        'hours_until_session' => round($hours_until, 1)
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error cancelling session: ' . $e->getMessage());
}
