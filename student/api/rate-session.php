<?php
require_once __DIR__ . '/../../lib/db.php';
$student = requireAuth();

if ($student['role'] != ROLE_STUDENT || $_SERVER['REQUEST_METHOD'] != 'POST') {
    sendErrorResponse('Unauthorized');
}

$pdo = getDBConnection();
$input = json_decode(file_get_contents('php://input'), true);
$userId = $student['id'];

// Validate input
if (empty($input['session_id']) || empty($input['rating'])) {
    sendErrorResponse('Missing required fields');
}

$sessionId = (int) $input['session_id'];
$rating = (int) $input['rating'];
$review = trim($input['review'] ?? '');

// Validate rating
if ($rating < 1 || $rating > 5) {
    sendErrorResponse('Invalid rating');
}

// Validate session belongs to student and is completed
$stmt = $pdo->prepare("
    SELECT id, status FROM consultation_sessions 
    WHERE id = ? AND user_id = ? AND status = 'completed'
");
$stmt->execute([$sessionId, $userId]);
$session = $stmt->fetch();

if (!$session) {
    sendErrorResponse('Invalid session or session not completed');
}

// Check if already rated
$stmt = $pdo->prepare("SELECT id FROM consultation_sessions WHERE id = ? AND rating IS NOT NULL");
$stmt->execute([$sessionId]);
if ($stmt->fetch()) {
    sendErrorResponse('Session already rated');
}

try {
    // Update session with rating
    $stmt = $pdo->prepare("
        UPDATE consultation_sessions 
        SET rating = ?, review = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$rating, $review, $sessionId]);

    // Update expert rating (recalculate average)
    $stmt = $pdo->prepare("
        SELECT expert_id FROM consultation_sessions WHERE id = ?
    ");
    $stmt->execute([$sessionId]);
    $expertId = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT AVG(CAST(rating AS FLOAT)) as avg_rating 
        FROM consultation_sessions 
        WHERE expert_id = ? AND rating IS NOT NULL
    ");
    $stmt->execute([$expertId]);
    $avgRating = $stmt->fetch()['avg_rating'] ?? 0;

    $stmt = $pdo->prepare("
        UPDATE expert_profiles SET rating = ? WHERE user_id = ?
    ");
    $stmt->execute([round($avgRating, 1), $expertId]);

    sendSuccessResponse(['message' => 'Session rated successfully']);

} catch (Exception $e) {
    sendErrorResponse('Error rating session: ' . $e->getMessage());
}
