<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    $conn = getDBConnection();
    
    $query = "SELECT * FROM session_templates WHERE expert_id = ?";
    $params = [$user['id']];
    $types = 'i';
    
    if ($category) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $templates = [];
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
    
    sendSuccessResponse('Templates retrieved', [
        'templates' => $templates,
        'count' => count($templates)
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching templates: ' . $e->getMessage());
}
