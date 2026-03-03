<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$specialization = isset($_GET['specialization']) ? $_GET['specialization'] : null;

try {
    $query = "
        SELECT u.id, u.full_name, u.email, u.profile_image, u.bio,
               ep.specialization, ep.experience_years, ep.language, 
               ep.availability_status, ep.hourly_rate, ep.rating, ep.total_reviews, ep.total_sessions
        FROM users u
        INNER JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE u.role IN ('expert', 'admin') AND ep.verification_status = 'verified'
    ";
    
    if ($specialization) {
        $query .= " AND ep.specialization LIKE ?";
        $stmt = $pdo->prepare($query . " ORDER BY ep.rating DESC, ep.total_reviews DESC");
        $stmt->execute(['%' . $specialization . '%']);
    } else {
        $stmt = $pdo->prepare($query . " ORDER BY ep.rating DESC, ep.total_reviews DESC");
        $stmt->execute();
    }
    
    $experts = $stmt->fetchAll();
    
    sendSuccessResponse($experts);
    
} catch (PDOException $e) {
    error_log("Get all experts error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
