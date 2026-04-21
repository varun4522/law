<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $stmt->fetch()['count'] ?? 0;

// Total experts
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = " . ROLE_EXPERT);
$stats['total_experts'] = $stmt->fetch()['count'] ?? 0;

// Total students
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = " . ROLE_STUDENT);
$stats['total_students'] = $stmt->fetch()['count'] ?? 0;

// Active consultations
$stmt = $pdo->query("SELECT COUNT(*) as count FROM consultation_sessions WHERE status = 'confirmed' OR status = 'in_progress'");
$stats['active_sessions'] = $stmt->fetch()['count'] ?? 0;

// Pending consultations
$stmt = $pdo->query("SELECT COUNT(*) as count FROM consultation_sessions WHERE status = 'pending'");
$stats['pending_sessions'] = $stmt->fetch()['count'] ?? 0;

// Total revenue
$stmt = $pdo->query("SELECT SUM(amount) as total FROM consultation_sessions WHERE status IN ('completed', 'confirmed')");
$result = $stmt->fetch();
$stats['total_revenue'] = floatval($result['total'] ?? 0);

// Pending reports
$stmt = $pdo->query("SELECT COUNT(*) as count FROM content_reports WHERE status = 'pending'");
$stats['pending_reports'] = $stmt->fetch()['count'] ?? 0;

// Recent users (last 10)
$stmt = $pdo->prepare("SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$recentUsers = $stmt->fetchAll();

// Recent consultations (last 10)
$stmt = $pdo->prepare("SELECT cs.*, u.full_name as user_name, e.full_name as expert_name FROM consultation_sessions cs 
                        JOIN users u ON cs.user_id = u.id 
                        JOIN users e ON cs.expert_id = e.id 
                        ORDER BY cs.created_at DESC LIMIT 10");
$stmt->execute();
$recentSessions = $stmt->fetchAll();

// Sessions by status (for chart)
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM consultation_sessions GROUP BY status");
$sessionStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LawConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <?php include 'components/navbar.php'; ?>
            
            <!-- Page Content -->
            <div class="page-content">
                <div class="content-header">
                    <div>
                        <h1>Dashboard</h1>
                        <p>Welcome back, <?php echo htmlspecialchars($adminUser['full_name']); ?>!</p>
                    </div>
                    <div class="header-actions">
                        <span class="date-time" id="dateTime"></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon experts">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['total_experts']; ?></div>
                            <div class="stat-label">Total Experts</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon students">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon sessions">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['active_sessions']; ?></div>
                            <div class="stat-label">Active Sessions</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $stats['pending_sessions']; ?></div>
                            <div class="stat-label">Pending Sessions</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 0); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <div class="chart-container">
                        <h3>Consultation Sessions by Status</h3>
                        <canvas id="sessionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Users Distribution</h3>
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="activity-card">
                        <div class="activity-header">
                            <h3>Recent Users</h3>
                            <a href="users.php" class="view-all">View All →</a>
                        </div>
                        <div class="activity-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo getRoleName($user['role']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="activity-card">
                        <div class="activity-header">
                            <h3>Recent Consultations</h3>
                            <a href="sessions.php" class="view-all">View All →</a>
                        </div>
                        <div class="activity-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Expert</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSessions as $session): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($session['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($session['expert_name']); ?></td>
                                        <td><?php echo date('M d, H:i', strtotime($session['session_date'])); ?></td>
                                        <td><span class="status-badge status-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update date/time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('dateTime').textContent = now.toLocaleDateString('en-US', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
            });
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Session Status Chart
        const sessionCtx = document.getElementById('sessionChart')?.getContext('2d');
        if (sessionCtx) {
            const sessionData = <?php echo json_encode($sessionStats); ?>;
            new Chart(sessionCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(sessionData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                    datasets: [{
                        data: Object.values(sessionData),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Users Distribution Chart
        const usersCtx = document.getElementById('usersChart')?.getContext('2d');
        if (usersCtx) {
            new Chart(usersCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Experts', 'Students'],
                    datasets: [{
                        data: [<?php echo $stats['total_experts']; ?>, <?php echo $stats['total_students']; ?>],
                        backgroundColor: ['#8b5cf6', '#3b82f6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
</body>
</html>
