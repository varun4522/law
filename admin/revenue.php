<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();

// Get revenue by date
$stmt = $pdo->query("
    SELECT 
        DATE(session_date) as date,
        COUNT(*) as sessions,
        SUM(amount) as revenue,
        AVG(amount) as avg_amount
    FROM consultation_sessions 
    WHERE status IN ('completed', 'confirmed')
    GROUP BY DATE(session_date)
    ORDER BY session_date DESC
    LIMIT 30
");
$revenueByDate = $stmt->fetchAll();

// Get revenue by expert
$stmt = $pdo->query("
    SELECT 
        u.full_name,
        COUNT(cs.id) as sessions,
        SUM(cs.amount) as revenue,
        AVG(cs.amount) as avg_amount
    FROM consultation_sessions cs
    JOIN users u ON cs.expert_id = u.id
    WHERE cs.status IN ('completed', 'confirmed')
    GROUP BY cs.expert_id
    ORDER BY revenue DESC
    LIMIT 20
");
$topExperts = $stmt->fetchAll();

// Total statistics
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_sessions,
        SUM(amount) as total_revenue,
        AVG(amount) as avg_session_amount
    FROM consultation_sessions 
    WHERE status IN ('completed', 'confirmed')
");
$totalStats = $stmt->fetch();

// Monthly revenue
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(session_date, '%Y-%m') as month,
        SUM(amount) as revenue
    FROM consultation_sessions 
    WHERE status IN ('completed', 'confirmed')
    GROUP BY DATE_FORMAT(session_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthlyRevenue = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin-styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'components/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'components/navbar.php'; ?>
            
            <div class="page-content">
                <div class="content-header">
                    <div>
                        <h1>Revenue Analytics</h1>
                        <p>Financial overview and transactions</p>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($totalStats['total_revenue'] ?? 0, 0); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon sessions">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $totalStats['total_sessions'] ?? 0; ?></div>
                            <div class="stat-label">Total Sessions</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon experts">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($totalStats['avg_session_amount'] ?? 0, 0); ?></div>
                            <div class="stat-label">Avg Session Value</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-section">
                    <div class="chart-container">
                        <h3>Monthly Revenue</h3>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Tables -->
                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Top Performing Experts</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Expert Name</th>
                                <th>Sessions</th>
                                <th>Total Revenue</th>
                                <th>Avg Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topExperts as $expert): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expert['full_name']); ?></td>
                                <td><?php echo $expert['sessions']; ?></td>
                                <td>₹<?php echo number_format($expert['revenue'] ?? 0, 2); ?></td>
                                <td>₹<?php echo number_format($expert['avg_amount'] ?? 0, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Daily Revenue (Last 30 Days)</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sessions</th>
                                <th>Revenue</th>
                                <th>Avg Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($revenueByDate, 0, 15) as $day): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                <td><?php echo $day['sessions']; ?></td>
                                <td>₹<?php echo number_format($day['revenue'] ?? 0, 2); ?></td>
                                <td>₹<?php echo number_format($day['avg_amount'] ?? 0, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
        if (monthlyCtx) {
            const data = <?php echo json_encode($monthlyRevenue); ?>;
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: data.map(m => m.month),
                    datasets: [{
                        label: 'Revenue',
                        data: data.map(m => m.revenue),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
