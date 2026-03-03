<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$category = isset($_GET['category']) ? $_GET['category'] : null;

try {
    $query = "
        SELECT fq.id, fq.title, fq.question, fq.category, fq.is_anonymous, 
               fq.views, fq.status, fq.created_at, fq.user_id,
               u.full_name as author_name,
               COUNT(DISTINCT fa.id) as answer_count
        FROM forum_questions fq
        LEFT JOIN users u ON fq.user_id = u.id
        LEFT JOIN forum_answers fa ON fq.id = fa.question_id
    ";
    
    if ($category) {
        $query .= " WHERE fq.category = ?";
        $query .= " GROUP BY fq.id ORDER BY fq.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$category]);
    } else {
        $query .= " GROUP BY fq.id ORDER BY fq.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }
    
    $questions = $stmt->fetchAll();
    
    // Hide user info for anonymous questions
    foreach ($questions as &$question) {
        if ($question['is_anonymous']) {
            $question['author_name'] = 'Anonymous';
            $question['user_id'] = null;
        }
    }
    
    sendSuccessResponse($questions);
    
} catch (PDOException $e) {
    error_log("Get questions error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
