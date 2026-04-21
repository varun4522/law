<?php
require_once __DIR__ . '/../../lib/db.php';

$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();

$notifications = [];
$count = 0;

// Get pending consultations
$stmt = $pdo->query("SELECT COUNT(*) as count FROM consultation_sessions WHERE status = 'pending' LIMIT 5");
$result = $stmt->fetch();
if ($result['count'] > 0) {
    $notifications[] = [
        'type' => 'warning',
        'icon' => 'hourglass-half',
        'message' => $result['count'] . ' pending consultation requests',
        'time' => 'Just now'
    ];
    $count++;
}

// Get pending expert verifications
$stmt = $pdo->query("SELECT COUNT(*) as count FROM expert_profiles WHERE verification_status = 'pending' LIMIT 5");
$result = $stmt->fetch();
if ($result['count'] > 0) {
    $notifications[] = [
        'type' => 'warning',
        'icon' => 'user-check',
        'message' => $result['count'] . ' experts pending verification',
        'time' => 'Recently'
    ];
    $count++;
}

// Get pending reports
$stmt = $pdo->query("SELECT COUNT(*) as count FROM content_reports WHERE status = 'pending' LIMIT 5");
$result = $stmt->fetch();
if ($result['count'] > 0) {
    $notifications[] = [
        'type' => 'danger',
        'icon' => 'flag',
        'message' => $result['count'] . ' content reports to review',
        'time' => 'Soon'
    ];
    $count++;
}

// Get new users (last 24 hours)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$result = $stmt->fetch();
if ($result['count'] > 0) {
    $notifications[] = [
        'type' => 'success',
        'icon' => 'user-plus',
        'message' => $result['count'] . ' new user(s) joined',
        'time' => 'Today'
    ];
    $count++;
}

sendSuccessResponse([
    'notifications' => $notifications,
    'count' => $count
]);
