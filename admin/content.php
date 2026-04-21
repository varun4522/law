<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "title LIKE ? OR description LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($type)) {
    $whereConditions[] = "types = ?";
    $params[] = $type;
}

if (!empty($status)) {
    $whereConditions[] = "status = ?";
    $params[] = $status;
}

$where = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total
$countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM data_records $where");
$countStmt->execute($params);
$total = $countStmt->fetch()['count'];
$totalPages = ceil($total / $perPage);

// Get records
$stmt = $pdo->prepare("
    SELECT dr.*, u.full_name FROM data_records dr
    LEFT JOIN users u ON dr.user_id = u.id
    $where
    ORDER BY dr.created_at DESC 
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Admin Dashboard</title>
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
                        <h1>Content Management</h1>
                        <p><?php echo $total; ?> total content items</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <input type="text" name="search" placeholder="Search content..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
                        
                        <select name="type" class="filter-select">
                            <option value="">All Types</option>
                            <option value="article" <?php echo $type === 'article' ? 'selected' : ''; ?>>Article</option>
                            <option value="guide" <?php echo $type === 'guide' ? 'selected' : ''; ?>>Guide</option>
                            <option value="document" <?php echo $type === 'document' ? 'selected' : ''; ?>>Document</option>
                        </select>

                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="content.php" class="btn btn-ghost">Clear</a>
                    </form>
                </div>

                <!-- Content Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="7" class="empty-message">No content found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($record['title'], 0, 50)); ?></td>
                                    <td><span class="badge"><?php echo ucfirst($record['types']); ?></span></td>
                                    <td><?php echo htmlspecialchars($record['full_name'] ?? 'Unknown'); ?></td>
                                    <td><span class="status-badge status-<?php echo $record['status']; ?>"><?php echo ucfirst($record['status']); ?></span></td>
                                    <td><?php echo $record['is_public'] ? '✓ Public' : '✗ Private'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($record['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewContent(<?php echo $record['id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon danger" onclick="deleteContent(<?php echo $record['id']; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        function viewContent(id) {
            alert('View functionality to be implemented');
        }

        function deleteContent(id) {
            if (confirm('Delete this content? This action cannot be undone.')) {
                alert('Delete functionality to be implemented');
            }
        }
    </script>
</body>
</html>
