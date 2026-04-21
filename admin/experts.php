<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$verification = isset($_GET['verification']) ? trim($_GET['verification']) : '';

// Build query
$whereConditions = ["u.role = " . ROLE_EXPERT];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR ep.specialization LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($verification)) {
    $whereConditions[] = "ep.verification_status = ?";
    $params[] = $verification;
}

$where = "WHERE " . implode(" AND ", $whereConditions);

// Get total count
$countStmt = $pdo->prepare("
    SELECT COUNT(*) as count FROM expert_profiles ep
    JOIN users u ON ep.user_id = u.id $where
");
$countStmt->execute($params);
$total = $countStmt->fetch()['count'];
$totalPages = ceil($total / $perPage);

// Get experts
$stmt = $pdo->prepare("
    SELECT ep.*, u.id, u.full_name, u.email, u.phone, u.status, u.created_at FROM expert_profiles ep
    JOIN users u ON ep.user_id = u.id 
    $where
    ORDER BY u.created_at DESC 
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$experts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Management - Admin Dashboard</title>
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
                        <h1>Expert Management</h1>
                        <p><?php echo $total; ?> total experts</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <input type="text" name="search" placeholder="Search experts..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
                        
                        <select name="verification" class="filter-select">
                            <option value="">All Status</option>
                            <option value="verified" <?php echo $verification === 'verified' ? 'selected' : ''; ?>>Verified</option>
                            <option value="pending" <?php echo $verification === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="rejected" <?php echo $verification === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="experts.php" class="btn btn-ghost">Clear</a>
                    </form>
                </div>

                <!-- Experts Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Experience</th>
                                <th>Hourly Rate</th>
                                <th>Verification</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($experts)): ?>
                            <tr>
                                <td colspan="8" class="empty-message">No experts found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($experts as $expert): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-small"><?php echo strtoupper(substr($expert['full_name'], 0, 1)); ?></div>
                                            <span><?php echo htmlspecialchars($expert['full_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($expert['email']); ?></td>
                                    <td><?php echo htmlspecialchars($expert['specialization'] ?? '-'); ?></td>
                                    <td><?php echo intval($expert['years_experience'] ?? 0); ?> years</td>
                                    <td>₹<?php echo intval($expert['hourly_rate'] ?? 0); ?>/hr</td>
                                    <td>
                                        <span class="status-badge status-<?php echo $expert['verification_status']; ?>">
                                            <?php echo ucfirst($expert['verification_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo number_format($expert['rating'] ?? 0, 1); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewExpert(<?php echo $expert['id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($expert['verification_status'] !== 'verified'): ?>
                                            <button class="btn-icon" onclick="verifyExpert(<?php echo $expert['id']; ?>)" title="Verify">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn-icon danger" onclick="rejectExpert(<?php echo $expert['id']; ?>)" title="Reject">
                                                <i class="fas fa-times"></i>
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
        function viewExpert(expertId) {
            // Open expert detail modal or redirect
            window.location.href = `expert-detail.php?id=${expertId}`;
        }

        function verifyExpert(expertId) {
            if (confirm('Verify this expert?')) {
                fetch(`api/verify_expert.php?id=${expertId}`, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('Expert verified successfully!');
                            location.reload();
                        }
                    });
            }
        }

        function rejectExpert(expertId) {
            const reason = prompt('Enter rejection reason:');
            if (reason) {
                fetch(`api/reject_expert.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${expertId}&reason=${encodeURIComponent(reason)}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Expert rejected!');
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
