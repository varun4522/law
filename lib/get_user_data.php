<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, user_id, types, title, description, content, status, is_public, created_at, updated_at
        FROM data_records
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $records = $stmt->fetchAll();
    
    sendSuccessResponse($records);
    
} catch (PDOException $e) {
    error_log("Get user data error: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching data', 500);
}
?>
