<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['title'], $input['description'], $input['duration'], $input['price'])) { sendErrorResponse('Missing fields'); }
try {
    $pdo = getDBConnection();
    $pdo->prepare("INSERT INTO session_templates (expert_id, title, description, duration_minutes, price, session_type) VALUES (?,?,?,?,?,?)")
        ->execute([$user['id'], $input['title'], $input['description'], intval($input['duration']), floatval($input['price']), $input['session_type'] ?? 'consultation']);
    sendSuccessResponse('Template saved', ['id' => $pdo->lastInsertId()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
