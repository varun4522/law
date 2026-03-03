<?php
require_once '../db.php';

header('Content-Type: application/json');

// Check if user is admin
$user = requireAuth();
if ($user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->query("
        SELECT id, email, name, role, wallet_balance, created_at 
        FROM users 
        ORDER BY created_at DESC
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendSuccessResponse($users);
    
} catch (Exception $e) {
    sendErrorResponse('Error fetching users: ' . $e->getMessage());
}
