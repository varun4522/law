<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
$expert_id = isset($_GET['expert_id']) ? intval($_GET['expert_id']) : $user['id'];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM expert_certifications WHERE expert_id = ? ORDER BY created_at DESC");
    $stmt->execute([$expert_id]);
    sendSuccessResponse('Certifications', ['certifications' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
