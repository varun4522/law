<?php
require_once '../db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }
$user = requireAuth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('Invalid request method', 405); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['session_id'])) { sendErrorResponse('Session ID required'); }
$session_id = intval($input['session_id']);
$reason = isset($input['reason']) ? trim($input['reason']) : 'Cancelled by user';
try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT cs.*, u.full_name as user_name, e.full_name as expert_name
                           FROM consultation_sessions cs
                           INNER JOIN users u ON cs.user_id = u.id
                           INNER JOIN users e ON cs.expert_id = e.id
                           WHERE cs.id = ? AND cs.user_id = ?");
    $stmt->execute([$session_id, $user['id']]);
    $session = $stmt->fetch();
    if (!$session) { $pdo->rollBack(); sendErrorResponse('Session not found'); }
    if (!in_array($session['status'], ['pending','confirmed'])) { $pdo->rollBack(); sendErrorResponse('Cannot cancel this session'); }
    $hours_until = (strtotime($session['session_date']) - time()) / 3600;
    $refund_amount = 0; $refund_pct = 0;
    if ($hours_until > 24) { $refund_pct = 100; $refund_amount = $session['amount']; }
    elseif ($hours_until > 12) { $refund_pct = 50; $refund_amount = $session['amount'] * 0.5; }
    $pdo->prepare("UPDATE consultation_sessions SET status='cancelled', notes=CONCAT(IFNULL(notes,''),?) WHERE id=?")
        ->execute(["\nCancelled: $reason", $session_id]);
    if ($refund_amount > 0) {
        $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?")->execute([$refund_amount, $user['id']]);
        $pdo->prepare("INSERT INTO wallet_transactions (user_id, transaction_type, amount, description, reference_id, status) VALUES (?, 'refund', ?, ?, ?, 'completed')")
            ->execute([$user['id'], $refund_amount, "Refund session #$session_id ($refund_pct%)", "REFUND_$session_id"]);
    }
    $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Session Cancelled', ?, 'session')")
        ->execute([$session['expert_id'], "{$session['user_name']} cancelled the session."]);
    $pdo->commit();
    sendSuccessResponse('Session cancelled', ['refund_amount' => $refund_amount, 'refund_percentage' => $refund_pct]);
} catch (Exception $e) { $pdo->rollBack(); sendErrorResponse('Error: ' . $e->getMessage()); }
