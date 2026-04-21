<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($status)) {
    $whereConditions[] = "status = ?";
    $params[] = $status;
}

$where = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM content_reports $where");
$countStmt->execute($params);
$total = $countStmt->fetch()['count'];
$totalPages = ceil($total / $perPage);

// Get reports
$stmt = $pdo->prepare("
    SELECT cr.*, u.full_name as reported_by_name
    FROM content_reports cr
    LEFT JOIN users u ON cr.reported_by = u.id 
    $where
    ORDER BY cr.created_at DESC 
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
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
                        <h1>Content Reports</h1>
                        <p><?php echo $total; ?> total reports</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_review" <?php echo $status === 'in_review' ? 'selected' : ''; ?>>In Review</option>
                            <option value="resolved" <?php echo $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="reports.php" class="btn btn-ghost">Clear</a>
                    </form>
                </div>

                <!-- Reports Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Content ID</th>
                                <th>Reason</th>
                                <th>Reported By</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="7" class="empty-message">No reports found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><span class="badge"><?php echo ucfirst($report['content_type']); ?></span></td>
                                    <td>#<?php echo $report['content_id']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($report['reason'], 0, 50)); ?></td>
                                    <td><?php echo htmlspecialchars($report['reported_by_name'] ?? 'Anonymous'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                                    <td><span class="status-badge status-<?php echo $report['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewReport(<?php echo $report['id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($report['status'] === 'pending'): ?>
                                            <button class="btn-icon" onclick="updateReportStatus(<?php echo $report['id']; ?>, 'in_review')" title="Review">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1" class="page-link">First</a>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next</a>
                        <a href="?page=<?php echo $totalPages; ?>" class="page-link">Last</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function viewReport(reportId) {
            window.location.href = `report-detail.php?id=${reportId}`;
        }

        function updateReportStatus(reportId, newStatus) {
            fetch(`api/update_report.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${reportId}&status=${newStatus}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Report status updated!');
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
