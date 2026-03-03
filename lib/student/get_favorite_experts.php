<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT u.id, u.full_name, u.email, u.profile_image, u.bio,
               ep.specialization, ep.experience_years, ep.language,
               ep.availability_status, ep.hourly_rate, ep.rating,
               ep.total_reviews, ep.total_sessions, ep.verification_status,
               fe.created_at as favorited_at
        FROM favorite_experts fe
        INNER JOIN users u ON fe.expert_id = u.id
        LEFT JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE fe.user_id = ? ORDER BY fe.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $favorites = $stmt->fetchAll();
    sendSuccessResponse('Favorites retrieved', ['favorites' => $favorites, 'count' => count($favorites)]);
} catch (Exception $e) { sendErrorResponse('Error: ' . $e->getMessage()); }
