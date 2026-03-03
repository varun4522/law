<?php
require_once '../db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }
$user = requireAuth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('Invalid request method', 405); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['expert_id']) || !isset($input['action'])) { sendErrorResponse('Expert ID and action required'); }
$expert_id = intval($input['expert_id']);
$action = $input['action'];
if (!in_array($action, ['add', 'remove'])) { sendErrorResponse('Action must be add or remove'); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'expert'");
    $stmt->execute([$expert_id]);
    if (!$stmt->fetch()) { sendErrorResponse('Expert not found'); }
    if ($action === 'add') {
        $stmt = $pdo->prepare("SELECT id FROM favorite_experts WHERE user_id = ? AND expert_id = ?");
        $stmt->execute([$user['id'], $expert_id]);
        if ($stmt->fetch()) { sendErrorResponse('Already in favorites'); }
        $pdo->prepare("INSERT INTO favorite_experts (user_id, expert_id) VALUES (?, ?)")->execute([$user['id'], $expert_id]);
        sendSuccessResponse('Expert added to favorites', ['action' => 'added']);
    } else {
        $stmt = $pdo->prepare("DELETE FROM favorite_experts WHERE user_id = ? AND expert_id = ?");
        $stmt->execute([$user['id'], $expert_id]);
        if ($stmt->rowCount() === 0) { sendErrorResponse('Expert not in favorites'); }
        sendSuccessResponse('Expert removed from favorites', ['action' => 'removed']);
    }
} catch (Exception $e) { sendErrorResponse('Error: ' . $e->getMessage()); }
