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

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['title']) || !isset($input['content'])) {
    sendErrorResponse('Title and content are required');
}

$title = trim($input['title']);
$content = trim($input['content']);
$category = isset($input['category']) ? trim($input['category']) : 'general';

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        INSERT INTO session_templates (expert_id, title, content, category)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user['id'], $title, $content, $category);
    $stmt->execute();
    
    $template_id = $conn->insert_id;
    
    sendSuccessResponse('Template saved', [
        'template_id' => $template_id,
        'title' => $title
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error saving template: ' . $e->getMessage());
}
