<?php
require_once __DIR__ . '/../../lib/db.php';
$student = requireAuth();

if ($student['role'] != ROLE_STUDENT) {
    sendErrorResponse('Unauthorized');
}

$pdo = getDBConnection();
$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    sendErrorResponse('Query too short');
}

$q = '%' . $q . '%';
$results = [];

// Search experts
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, ep.specialization, 'expert' as type
    FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    WHERE u.role = ? AND (u.full_name LIKE ? OR ep.specialization LIKE ?)
    LIMIT 5
");
$stmt->execute([ROLE_EXPERT, $q, $q]);

foreach ($stmt->fetchAll() as $expert) {
    $results[] = [
        'title' => $expert['full_name'] . ' - ' . $expert['specialization'],
        'type' => 'Expert',
        'icon' => 'user-tie',
        'url' => 'expert-profile.php?id=' . $expert['id']
    ];
}

// Search articles
$stmt = $pdo->prepare("
    SELECT id, title, types
    FROM data_records
    WHERE is_public = 1 AND status = 'published' AND (title LIKE ? OR description LIKE ?)
    LIMIT 5
");
$stmt->execute([$q, $q]);

foreach ($stmt->fetchAll() as $article) {
    $results[] = [
        'title' => $article['title'],
        'type' => 'Article - ' . $article['types'],
        'icon' => 'file-alt',
        'url' => 'article.php?id=' . $article['id']
    ];
}

sendSuccessResponse(['results' => $results]);
