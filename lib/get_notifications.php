<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

$userId = $_SESSION['user_id'];
$unreadOnly = isset($_GET['unread']) && $_GET['unread'] === 'true';

try {
    $query = "SELECT id, title, message, type, is_read, link, created_at 
              FROM notifications 
              WHERE user_id = ?";
    
    if ($unreadOnly) {
        $query .= " AND is_read = 0";
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 50";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll();
    
    sendSuccessResponse($notifications);
    
} catch (PDOException $e) {
    error_log("Get notifications error: " . $e->getMessage());
    sendErrorResponse('An error occurred while fetching notifications', 500);
}
?>
