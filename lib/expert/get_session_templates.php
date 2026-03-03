<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM session_templates WHERE expert_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]);
    sendSuccessResponse('Templates', ['templates' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
