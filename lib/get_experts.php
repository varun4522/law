<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$userRole = $_SESSION['role'];

// Check if user has permission to view experts
if ($userRole !== 'admin' && $userRole !== 'expert') {
    sendErrorResponse('Access denied', 403);
}

try {
    $stmt = $pdo->prepare("
        SELECT id, email, full_name, role, created_at
        FROM users
        WHERE role IN ('expert', 'admin')
        ORDER BY full_name ASC
    ");
    $stmt->execute();
    $experts = $stmt->fetchAll();
    
    sendSuccessResponse($experts);
    
} catch (PDOException $e) {
    error_log("Get experts error: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching experts', 500);
}
?>
