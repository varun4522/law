<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.phone,
            u.profile_image,
            u.bio,
            ep.specialization,
            ep.experience_years,
            ep.language,
            ep.availability_status,
            ep.hourly_rate,
            ep.rating,
            ep.total_reviews,
            ep.total_sessions,
            ep.verification_status,
            fe.created_at as favorited_at
        FROM favorite_experts fe
        INNER JOIN users u ON fe.expert_id = u.id
        LEFT JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE fe.user_id = ?
        ORDER BY fe.created_at DESC
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $favorites = [];
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
    
    sendSuccessResponse('Favorite experts retrieved', ['favorites' => $favorites, 'count' => count($favorites)]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching favorites: ' . $e->getMessage());
}
