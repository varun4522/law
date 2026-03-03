<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT u.id, u.full_name, u.email, ep.specialization, ep.experience_years, ep.verification_status, ep.bar_council_number, ep.bio FROM users u INNER JOIN expert_profiles ep ON u.id=ep.user_id WHERE ep.verification_status='pending' ORDER BY u.created_at DESC");
    sendSuccessResponse('Verification requests', ['requests' => $stmt->fetchAll()]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
