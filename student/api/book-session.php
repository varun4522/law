<?php
require_once __DIR__ . '/../../lib/db.php';
$student = requireAuth();

if ($student['role'] != ROLE_STUDENT || $_SERVER['REQUEST_METHOD'] != 'POST') {
    sendErrorResponse('Unauthorized');
}

$pdo = getDBConnection();
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$required = ['expert_id', 'session_date', 'session_time', 'duration', 'topic', 'communication_type'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        sendErrorResponse("Missing required field: $field");
    }
}

$expertId = (int) $input['expert_id'];
$userId = $student['id'];
$sessionDateTime = $input['session_date'] . ' ' . $input['session_time'];
$duration = (int) $input['duration'];
$topic = trim($input['topic']);
$preferences = trim($input['preferences'] ?? '');
$communicationType = $input['communication_type'];
$paymentMethod = $input['payment_method'] ?? 'card';

// Validate expert
$stmt = $pdo->prepare("
    SELECT ep.hourly_rate 
    FROM expert_profiles ep
    JOIN users u ON ep.user_id = u.id
    WHERE u.id = ? AND u.role = ? AND ep.verification_status = 'verified'
");
$stmt->execute([$expertId, ROLE_EXPERT]);
$expert = $stmt->fetch();

if (!$expert) {
    sendErrorResponse('Invalid expert selected');
}

// Calculate amount
$durationHours = $duration / 60;
$amount = round($expert['hourly_rate'] * $durationHours);
$commission = round($amount * 0.05);
$expertEarnings = $amount - $commission;

// Validate session date
$sessionTs = strtotime($sessionDateTime);
if ($sessionTs <= time()) {
    sendErrorResponse('Session date must be in the future');
}

try {
    $pdo->beginTransaction();

    // Create session
    $stmt = $pdo->prepare("
        INSERT INTO consultation_sessions (
            user_id, expert_id, session_date, duration, topic, preferences,
            communication_type, amount, commission, expert_earnings, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $userId, $expertId, $sessionDateTime, $duration, $topic, $preferences,
        $communicationType, $amount, $commission, $expertEarnings
    ]);

    $sessionId = $pdo->lastInsertId();

    // TODO: Process payment based on payment method
    // For now, mark as confirmed after successful insertion
    $stmt = $pdo->prepare("UPDATE consultation_sessions SET status = 'confirmed' WHERE id = ?");
    $stmt->execute([$sessionId]);

    $pdo->commit();

    sendSuccessResponse(['message' => 'Session booked successfully', 'session_id' => $sessionId]);

} catch (Exception $e) {
    $pdo->rollBack();
    sendErrorResponse('Error booking session: ' . $e->getMessage());
}
