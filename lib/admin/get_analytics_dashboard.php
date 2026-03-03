<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
try {
    $pdo = getDBConnection();
    $users   = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN role='expert' THEN 1 ELSE 0 END) as experts, SUM(CASE WHEN role='user' THEN 1 ELSE 0 END) as students, SUM(CASE WHEN created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY) THEN 1 ELSE 0 END) as new_this_week FROM users")->fetch();
    $sessions = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending, COALESCE(SUM(CASE WHEN status='completed' THEN amount END),0) as revenue FROM consultation_sessions")->fetch();
    $forum   = $pdo->query("SELECT COUNT(*) as questions FROM forum_questions")->fetch();
    sendSuccessResponse('Dashboard', ['users' => $users, 'sessions' => $sessions, 'forum' => $forum]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
