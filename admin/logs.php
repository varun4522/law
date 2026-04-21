<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Get admin logs if table exists
$logs = [];
try {
    $stmt = $pdo->prepare("
        SELECT al.*, u.full_name FROM admin_logs al
        LEFT JOIN users u ON al.admin_id = u.id
        ORDER BY al.created_at DESC 
        LIMIT $offset, $perPage
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'components/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'components/navbar.php'; ?>
            
            <div class="page-content">
                <div class="content-header">
                    <div>
                        <h1>Audit Logs</h1>
                        <p>Track all admin actions</p>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Admin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="3" class="empty-message">No logs found. The logs table might not be set up yet.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($log['full_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="padding: 20px; background: #f3f4f6; border-radius: 6px; margin-top: 24px;">
                    <p style="color: #6b7280; font-size: 13px;">
                        <i class="fas fa-info-circle"></i> To enable audit logging, create the <code style="background: white; padding: 2px 6px; border-radius: 3px;">admin_logs</code> table in your database.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
