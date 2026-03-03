<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['title'], $input['message'])) { sendErrorResponse('title and message required'); }
$target = $input['target'] ?? 'all';
try {
    $pdo = getDBConnection();
    $where = match($target) { 'students' => "WHERE role='user'", 'experts' => "WHERE role='expert'", default => '' };
    $ids = $pdo->query("SELECT id FROM users $where")->fetchAll(PDO::FETCH_COLUMN);
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?,?,?,'broadcast')");
    foreach ($ids as $id) { $stmt->execute([$id, $input['title'], $input['message']]); }
    sendSuccessResponse('Broadcast sent', ['recipients' => count($ids)]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
