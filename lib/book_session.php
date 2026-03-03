<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['expert_id']) || !isset($input['session_date']) || !isset($input['duration'])) {
    sendErrorResponse('Expert ID, session date, and duration are required');
}

$expertId = intval($input['expert_id']);
$sessionDate = $input['session_date'];
$duration = intval($input['duration']);
$sessionType = isset($input['session_type']) ? $input['session_type'] : 'video';
$notes = isset($input['notes']) ? $input['notes'] : '';

$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $pdo->beginTransaction();
    
    // Get expert hourly rate    
    $stmt = $pdo->prepare("SELECT hourly_rate FROM expert_profiles WHERE user_id = ?");
    $stmt->execute([$expertId]);
    $expert = $stmt->fetch();
    
    if (!$expert) {
        sendErrorResponse('Expert not found', 404);
    }
    
    $hourlyRate = $expert['hourly_rate'];
    $amount = ($hourlyRate / 60) * $duration;
    $commission = $amount * 0.15; // 15% commission
    
    // Check user wallet balance
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user['wallet_balance'] < $amount) {
        sendErrorResponse('Insufficient wallet balance', 400);
    }
    
    // Deduct from user wallet
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
    $stmt->execute([$amount, $userId]);
    
    // Create session
    $stmt = $pdo->prepare("
        INSERT INTO consultation_sessions 
        (user_id, expert_id, session_date, duration, session_type, status, amount, commission, notes)
        VALUES (?, ?, ?, ?, ?, 'locked', ?, ?, ?)
    ");
    $stmt->execute([$userId, $expertId, $sessionDate, $duration, $sessionType, $amount, $commission, $notes]);
    $sessionId = $pdo->lastInsertId();
    
    // Add wallet transaction
    $stmt = $pdo->prepare("
        INSERT INTO wallet_transactions (user_id, transaction_type, amount, description, reference_id, status)
        VALUES (?, 'debit', ?, 'Session booking', ?, 'completed')
    ");
    $stmt->execute([$userId, $amount, 'SESSION_' . $sessionId]);
    
    // Create notification for expert
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        VALUES (?, 'New Session Booking', 'You have a new consultation session booking', 'session')
    ");
    $stmt->execute([$expertId]);
    
    $pdo->commit();
    
    sendSuccessResponse(['session_id' => $sessionId], 'Session booked successfully');
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Book session error: " . $e->getMessage());
    sendErrorResponse('An error occurred while booking session', 500);
}
?>
