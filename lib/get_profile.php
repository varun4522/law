<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$user = getCurrentUser();
if (!$user) {
    sendErrorResponse('User not found', 404);
}

try {
    // Get user profile
    $stmt = $pdo->prepare("SELECT id, email, full_name, role, wallet_balance, phone, bio, profile_image, created_at FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        sendErrorResponse('Profile not found', 404);
    }
    
    // Add wallet balance to main data for convenience
    $profile['name'] = $profile['full_name'];
    
    sendSuccessResponse($profile);
    
} catch (PDOException $e) {
    error_log("Get profile error: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching profile', 500);
}
?>
