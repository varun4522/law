<?php
require_once '../db.php';
header('Content-Type: application/json');
$user   = requireAuth();
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$limit  = isset($_GET['limit'])  ? intval($_GET['limit'])  : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
try {
    $pdo = getDBConnection();
    $query  = "SELECT cs.*, expert.full_name as expert_name, expert.profile_image as expert_image,
                      ep.specialization, ep.rating as expert_rating
               FROM consultation_sessions cs
               INNER JOIN users expert ON cs.expert_id = expert.id
               LEFT JOIN expert_profiles ep ON expert.id = ep.user_id
               WHERE cs.user_id = ?";
    $params = [$user['id']];
    if ($status !== 'all') { $query .= " AND cs.status = ?"; $params[] = $status; }
    $query .= " ORDER BY cs.session_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit; $params[] = $offset;
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sessions = $stmt->fetchAll();
    $total_spent = 0;
    foreach ($sessions as $s) { if (in_array($s['status'], ['completed','confirmed'])) $total_spent += floatval($s['amount']); }
    $c = $pdo->prepare("SELECT COUNT(*) FROM consultation_sessions WHERE user_id = ?");
    $c->execute([$user['id']]);
    $total = $c->fetchColumn();
    sendSuccessResponse('Session history', ['sessions' => $sessions, 'count' => count($sessions), 'total_records' => $total, 'total_spent' => $total_spent]);
} catch (Exception $e) { sendErrorResponse('Error: ' . $e->getMessage()); }
