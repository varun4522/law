<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$reportId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$reportId) {
    header('Location: reports.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT cr.*, u.full_name as reported_by_name 
    FROM content_reports cr
    LEFT JOIN users u ON cr.reported_by = u.id
    WHERE cr.id = ?
");
$stmt->execute([$reportId]);
$report = $stmt->fetch();

if (!$report) {
    header('Location: reports.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report #<?php echo $reportId; ?> - Admin Dashboard</title>
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
                        <a href="reports.php" style="color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <h1>Report #<?php echo $reportId; ?></h1>
                        <p><?php echo date('M d, Y H:i', strtotime($report['created_at'])); ?></p>
                    </div>
                </div>

                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Report Details</h3>
                    <div style="padding: 20px;">
                        <table class="data-table" style="margin: 0;">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600; width: 30%;">Status</td>
                                    <td><span class="status-badge status-<?php echo $report['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Content Type</td>
                                    <td><?php echo ucfirst($report['content_type']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Content ID</td>
                                    <td>#<?php echo $report['content_id']; ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Reported By</td>
                                    <td><?php echo htmlspecialchars($report['reported_by_name'] ?? 'Anonymous'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Reason</td>
                                    <td><?php echo htmlspecialchars($report['reason']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Description</td>
                                    <td><?php echo htmlspecialchars($report['description'] ?? 'No additional details'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Reported At</td>
                                    <td><?php echo date('M d, Y H:i:s', strtotime($report['created_at'])); ?></td>
                                </tr>
                                <?php if ($report['resolved_at']): ?>
                                <tr>
                                    <td style="font-weight: 600;">Resolved At</td>
                                    <td><?php echo date('M d, Y H:i:s', strtotime($report['resolved_at'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Admin Notes</td>
                                    <td><?php echo htmlspecialchars($report['admin_notes'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Resolution</td>
                                    <td><?php echo htmlspecialchars($report['resolution'] ?? 'N/A'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($report['status'] !== 'resolved'): ?>
                <div class="table-container" style="margin-top: 24px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Take Action</h3>
                    <form style="padding: 20px;" id="actionForm">
                        <div class="form-group">
                            <label>Resolution</label>
                            <select name="resolution" class="form-control">
                                <option value="">Select action...</option>
                                <option value="approved">Approve Content (No Action)</option>
                                <option value="deleted">Delete Content</option>
                                <option value="warned">Send Warning to User</option>
                                <option value="suspended">Suspend User Account</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Admin Notes</label>
                            <textarea class="form-control" name="admin_notes" rows="4" placeholder="Enter your notes..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Resolve Report</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('actionForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Report resolution functionality to be implemented');
        });
    </script>
</body>
</html>
