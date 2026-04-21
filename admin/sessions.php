<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(u.full_name LIKE ? OR e.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $whereConditions[] = "cs.status = ?";
    $params[] = $status;
}

$where = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultation_sessions cs 
                            JOIN users u ON cs.user_id = u.id 
                            JOIN users e ON cs.expert_id = e.id $where");
$countStmt->execute($params);
$total = $countStmt->fetch()['count'];
$totalPages = ceil($total / $perPage);

// Get sessions
$stmt = $pdo->prepare("
    SELECT cs.*, u.full_name as student_name, u.email as student_email, 
           e.full_name as expert_name, e.email as expert_email
    FROM consultation_sessions cs
    JOIN users u ON cs.user_id = u.id 
    JOIN users e ON cs.expert_id = e.id 
    $where
    ORDER BY cs.session_date DESC 
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$sessions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultations - Admin Dashboard</title>
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
                        <h1>Consultation Sessions</h1>
                        <p><?php echo $total; ?> total sessions</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <input type="text" name="search" placeholder="Search students or experts..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
                        
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $status === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="sessions.php" class="btn btn-ghost">Clear</a>
                    </form>
                </div>

                <!-- Sessions Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Expert</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="8" class="empty-message">No sessions found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($session['expert_name']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($session['session_date'])); ?></td>
                                    <td><?php echo $session['duration']; ?> min</td>
                                    <td><?php echo ucfirst($session['session_type'] ?? 'N/A'); ?></td>
                                    <td>₹<?php echo number_format($session['amount'] ?? 0, 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewSession(<?php echo $session['id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($session['status'] === 'pending'): ?>
                                            <button class="btn-icon" onclick="updateSessionStatus(<?php echo $session['id']; ?>, 'confirmed')" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if (in_array($session['status'], ['pending', 'confirmed'])): ?>
                                            <button class="btn-icon danger" onclick="updateSessionStatus(<?php echo $session['id']; ?>, 'cancelled')" title="Cancel">
                                                <i class="fas fa-times"></i>
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
        function viewSession(sessionId) {
            window.location.href = `session-detail.php?id=${sessionId}`;
        }

        function updateSessionStatus(sessionId, newStatus) {
            fetch(`api/update_session.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${sessionId}&status=${newStatus}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Session updated!');
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
