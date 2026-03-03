<?php
require_once 'db.php';

requireAuth();
setJsonHeader();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['notification_id'])) {
    sendErrorResponse('Notification ID is required');
}

$notificationId = $input['notification_id'];
$userId = $_SESSION['user_id'];

$pdo = getDBConnection();
if (!$pdo) {
    sendErrorResponse('Database connection failed', 500);
}

try {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notificationId, $userId]);
    
    sendSuccessResponse(null, 'Notification marked as read');
    
} catch (PDOException $e) {
    error_log("Mark notification read error: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
?>
