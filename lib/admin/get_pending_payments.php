<?php
require_once '../db.php';

requireAuth();
setJsonHeader();

$user = requireAuth();

// Check if user is admin
if ($user['role'] !== 'admin') {
    sendErrorResponse('Unauthorized access', 403);
}

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            u.full_name,
            u.email,
            u.phone_number,
            v.full_name as verified_by_name
        FROM payments p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN users v ON p.verified_by = v.id
        WHERE p.status IN ('pending', 'completed')
        ORDER BY 
            CASE 
                WHEN p.status = 'completed' THEN 1
                WHEN p.status = 'pending' THEN 2
            END,
            p.created_at DESC
    ");
    
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendSuccessResponse($payments);
    
} catch (PDOException $e) {
    error_log("Get pending payments error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
