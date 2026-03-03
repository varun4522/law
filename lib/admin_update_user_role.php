<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is admin
$user = requireAuth();
if ($user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['role'])) {
    sendErrorResponse('User ID and role are required');
}

$userId = $data['user_id'];
$newRole = $data['role'];

if (!in_array($newRole, ['user', 'expert', 'admin'])) {
    sendErrorResponse('Invalid role');
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $userId]);
    
    // If changing to expert, create expert profile if it doesn't exist
    if ($newRole === 'expert') {
        $stmt = $conn->prepare("SELECT id FROM expert_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        if (!$stmt->fetch()) {
            $stmt = $conn->prepare("
                INSERT INTO expert_profiles (user_id, specialization, experience_years, hourly_rate, is_available) 
                VALUES (?, 'General Law', 1, 500, 1)
            ");
            $stmt->execute([$userId]);
        }
    }
    
    sendSuccessResponse(['message' => 'User role updated successfully']);
    
} catch (Exception $e) {
    sendErrorResponse('Error updating role: ' . $e->getMessage());
}
