<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if (!isset($_GET['session_id'])) { sendErrorResponse('Session ID required'); }
$session_id = intval($_GET['session_id']);
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM consultation_sessions WHERE id = ? AND (user_id = ? OR expert_id = ?)");
    $stmt->execute([$session_id, $user['id'], $user['id']]);
    if (!$stmt->fetch()) { sendErrorResponse('Session not found or access denied'); }
    $stmt = $pdo->prepare("SELECT sd.*, u.full_name as uploaded_by_name, u.role as uploaded_by_role
                           FROM session_documents sd
                           INNER JOIN users u ON sd.uploaded_by = u.id
                           WHERE sd.session_id = ? ORDER BY sd.created_at DESC");
    $stmt->execute([$session_id]);
    $docs = $stmt->fetchAll();
    sendSuccessResponse('Documents retrieved', ['documents' => $docs, 'count' => count($docs)]);
} catch (Exception $e) { sendErrorResponse('Error: ' . $e->getMessage()); }
