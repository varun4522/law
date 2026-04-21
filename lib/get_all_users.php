<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

try {
    // Check if admin
    $user = requireAuth();
    if ((int)$user['role'] !== ROLE_ADMIN) {
        http_response_code(403);
        sendErrorResponse('Unauthorized: Admin access required');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendErrorResponse('Database connection failed', 500);
    }

    // Get all users with their details
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.email,
            u.full_name,
            u.phone,
            u.role,
            u.status,
            u.profile_image,
            u.bio,
            u.wallet_balance,
            u.created_at,
            u.updated_at,
            (SELECT COUNT(*) FROM expert_profiles ep WHERE ep.user_id = u.id) as is_expert,
            (SELECT COUNT(*) FROM consultation_sessions cs WHERE cs.user_id = u.id) as total_sessions
        FROM users u
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data
    $formattedUsers = [];
    foreach ($users as $user) {
        $formattedUsers[] = [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'phone' => $user['phone'],
            'role' => (int)$user['role'],
            'status' => $user['status'],
            'profile_image' => $user['profile_image'],
            'bio' => $user['bio'],
            'wallet_balance' => (float)$user['wallet_balance'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at'],
            'is_expert' => (int)$user['is_expert'] > 0,
            'total_sessions' => (int)$user['total_sessions']
        ];
    }

    sendSuccessResponse($formattedUsers, 'Users retrieved successfully');

} catch (Exception $e) {
    error_log("Error in get_all_users.php: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching users', 500);
}
?>
