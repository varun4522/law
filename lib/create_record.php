<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['title']) || empty(trim($input['title']))) {
    sendErrorResponse('Title is required');
}

if (!isset($input['types']) || empty(trim($input['types']))) {
    sendErrorResponse('Type is required');
}

$title = trim($input['title']);
$types = trim($input['types']);
$description = isset($input['description']) ? trim($input['description']) : '';
$content = isset($input['content']) ? trim($input['content']) : '';
$isPublic = isset($input['is_public']) ? (bool)$input['is_public'] : false;

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO data_records (user_id, types, title, description, content, is_public, status, created_by_role)
        VALUES (?, ?, ?, ?, ?, ?, 'draft', ?)
    ");
    $stmt->execute([$userId, $types, $title, $description, $content, $isPublic ? 1 : 0, $userRole]);
    
    $recordId = $pdo->lastInsertId();
    
    sendSuccessResponse(['id' => $recordId], 'Record created successfully');
    
} catch (PDOException $e) {
    error_log("Create record error: " . $e->getMessage());
    sendErrorResponse('An error occurred while creating record', 500);
}
?>
