<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.profile_image, COUNT(cs.id) as total_sessions, MAX(cs.session_date) as last_session, SUM(CASE WHEN cs.status='completed' THEN cs.amount ELSE 0 END) as total_spent FROM consultation_sessions cs INNER JOIN users u ON cs.user_id = u.id WHERE cs.expert_id = ? GROUP BY u.id ORDER BY last_session DESC");
    $stmt->execute([$user['id']]);
    $clients = $stmt->fetchAll();
    sendSuccessResponse('Clients', ['clients' => $clients, 'count' => count($clients)]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
