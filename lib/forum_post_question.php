<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['title']) || !isset($input['question'])) {
    sendErrorResponse('Title and question are required');
}

$title = trim($input['title']);
$question = trim($input['question']);
$category = isset($input['category']) ? trim($input['category']) : 'General';
$isAnonymous = isset($input['is_anonymous']) ? (bool)$input['is_anonymous'] : false;

$userId = $isAnonymous ? null : $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO forum_questions (user_id, title, question, category, is_anonymous)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $title, $question, $category, $isAnonymous ? 1 : 0]);
    
    $questionId = $pdo->lastInsertId();
    
    sendSuccessResponse(['question_id' => $questionId], 'Question posted successfully');
    
} catch (PDOException $e) {
    error_log("Post question error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
