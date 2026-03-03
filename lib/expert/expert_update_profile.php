<?php
require_once '../db.php';

header('Content-Type: application/json');

$user = requireAuth();
if ($user['role'] !== 'expert' && $user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn = getDBConnection();
    
    // Check if expert profile exists
    $stmt = $conn->prepare("SELECT id FROM expert_profiles WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profile) {
        // Update existing profile
        $stmt = $conn->prepare("
            UPDATE expert_profiles 
            SET specialization = ?,
                experience_years = ?,
                hourly_rate = ?,
                languages = ?,
                bio = ?,
                is_available = ?
            WHERE user_id = ?
        ");
        
        $stmt->execute([
            $data['specialization'] ?? 'General Law',
            $data['experience_years'] ?? 1,
            $data['hourly_rate'] ?? 500,
            $data['languages'] ?? 'English',
            $data['bio'] ?? '',
            isset($data['is_available']) ? $data['is_available'] : 1,
            $user['id']
        ]);
    } else {
        // Create new profile
        $stmt = $conn->prepare("
            INSERT INTO expert_profiles (user_id, specialization, experience_years, hourly_rate, languages, bio, is_available)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user['id'],
            $data['specialization'] ?? 'General Law',
            $data['experience_years'] ?? 1,
            $data['hourly_rate'] ?? 500,
            $data['languages'] ?? 'English',
            $data['bio'] ?? '',
            isset($data['is_available']) ? $data['is_available'] : 1
        ]);
    }
    
    sendSuccessResponse(['message' => 'Expert profile updated successfully']);
    
} catch (Exception $e) {
    sendErrorResponse('Error updating profile: ' . $e->getMessage());
}
