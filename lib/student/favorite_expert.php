<?php
require_once '../db.php';

// Enable CORS and JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Authenticate user
$user = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['expert_id']) || !isset($input['action'])) {
    sendErrorResponse('Expert ID and action (add/remove) are required');
}

$expert_id = intval($input['expert_id']);
$action = $input['action'];

if (!in_array($action, ['add', 'remove'])) {
    sendErrorResponse('Action must be "add" or "remove"');
}

try {
    $conn = getDBConnection();
    
    // Check if expert exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'expert'");
    $stmt->bind_param("i", $expert_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        sendErrorResponse('Expert not found');
    }
    
    if ($action === 'add') {
        // Check if already favorited
        $stmt = $conn->prepare("SELECT id FROM favorite_experts WHERE user_id = ? AND expert_id = ?");
        $stmt->bind_param("ii", $user['id'], $expert_id);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            sendErrorResponse('Expert already in favorites');
        }
        
        // Add to favorites
        $stmt = $conn->prepare("INSERT INTO favorite_experts (user_id, expert_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user['id'], $expert_id);
        $stmt->execute();
        
        sendSuccessResponse('Expert added to favorites', ['action' => 'added']);
    } else {
        // Remove from favorites
        $stmt = $conn->prepare("DELETE FROM favorite_experts WHERE user_id = ? AND expert_id = ?");
        $stmt->bind_param("ii", $user['id'], $expert_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            sendErrorResponse('Expert not in favorites');
        }
        
        sendSuccessResponse('Expert removed from favorites', ['action' => 'removed']);
    }
} catch (Exception $e) {
    sendErrorResponse('Error managing favorites: ' . $e->getMessage());
}
