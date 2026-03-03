<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['expert_id']) || !isset($input['rating'])) {
    sendErrorResponse('Expert ID and rating are required');
}

$expertId = intval($input['expert_id']);
$rating = intval($input['rating']);
$review = isset($input['review']) ? trim($input['review']) : '';
$sessionId = isset($input['session_id']) ? intval($input['session_id']) : null;

if ($rating < 1 || $rating > 5) {
    sendErrorResponse('Rating must be between 1 and 5');
}

$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $pdo->beginTransaction();
    
    // Insert review
    $stmt = $pdo->prepare("
        INSERT INTO reviews (expert_id, user_id, session_id, rating, review)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$expertId, $userId, $sessionId, $rating, $review]);
    
    // Update expert average rating
    $stmt = $pdo->prepare("
        UPDATE expert_profiles 
        SET rating = (SELECT AVG(rating) FROM reviews WHERE expert_id = ?),
            total_reviews = (SELECT COUNT(*) FROM reviews WHERE expert_id = ?)
        WHERE user_id = ?
    ");
    $stmt->execute([$expertId, $expertId, $expertId]);
    
    // Update session rating if session_id provided
    if ($sessionId) {
        $stmt = $pdo->prepare("UPDATE consultation_sessions SET rating = ?, review = ? WHERE id = ?");
        $stmt->execute([$rating, $review, $sessionId]);
    }
    
    $pdo->commit();
    
    sendSuccessResponse(null, 'Review submitted successfully');
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Submit review error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
