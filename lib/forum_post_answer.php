<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['question_id']) || !isset($input['answer'])) {
    sendErrorResponse('Question ID and answer are required');
}

$questionId = intval($input['question_id']);
$answer = trim($input['answer']);
$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    // Check if question exists
    $stmt = $pdo->prepare("SELECT id, user_id FROM forum_questions WHERE id = ?");
    $stmt->execute([$questionId]);
    $question = $stmt->fetch();
    
    if (!$question) {
        sendErrorResponse('Question not found', 404);
    }
    
    // Insert answer
    $stmt = $pdo->prepare("
        INSERT INTO forum_answers (question_id, user_id, answer)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$questionId, $userId, $answer]);
    
    $answerId = $pdo->lastInsertId();
    
    // Notify question author if not anonymous
    if ($question['user_id']) {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, 'New Answer', 'Someone answered your question', 'message')
        ");
        $stmt->execute([$question['user_id']]);
    }
    
    sendSuccessResponse(['answer_id' => $answerId], 'Answer posted successfully');
    
} catch (PDOException $e) {
    error_log("Post answer error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
