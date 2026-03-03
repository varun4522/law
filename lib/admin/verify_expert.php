<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['expert_id'], $input['action'])) { sendErrorResponse('expert_id and action required'); }
$status = $input['action'] === 'approve' ? 'verified' : 'rejected';
try {
    $pdo = getDBConnection();
    $pdo->prepare("UPDATE expert_profiles SET verification_status=? WHERE user_id=?")->execute([$status, intval($input['expert_id'])]);
    $msg = "Your expert profile has been " . ($status === 'verified' ? 'approved ' : 'rejected');
    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Profile Verification', ?, 'system')")->execute([intval($input['expert_id']), $msg]);
    sendSuccessResponse("Expert $status");
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
