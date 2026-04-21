<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();

// Revenue data
$stmt = $pdo->query("
    SELECT 
        DATE(session_date) as date, 
        COUNT(*) as count,
        SUM(amount) as revenue
    FROM consultation_sessions 
    WHERE status IN ('completed', 'confirmed')
    GROUP BY DATE(session_date)
    ORDER BY session_date DESC
    LIMIT 30
");
$revenueData = $stmt->fetchAll();

// Expert ratings
$stmt = $pdo->query("
    SELECT 
        rating,
        COUNT(*) as count
    FROM consultation_sessions 
    WHERE rating IS NOT NULL
    GROUP BY rating
    ORDER BY rating DESC
");
$ratingDistribution = $stmt->fetchAll();

// Monthly stats
$stmt = $pdo->query("
    SELECT 
        MONTH(created_at) as month,
        YEAR(created_at) as year,
        COUNT(*) as new_users
    FROM users
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC
    LIMIT 12
");
$monthlyStats = $stmt->fetchAll();

// Session types
$stmt = $pdo->query("
    SELECT 
        session_type,
        COUNT(*) as count
    FROM consultation_sessions 
    GROUP BY session_type
");
$sessionTypes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Expert specializations
$stmt = $pdo->query("
    SELECT 
        specialization,
        COUNT(*) as count,
        AVG(rating) as avg_rating
    FROM expert_profiles 
    GROUP BY specialization
    ORDER BY count DESC
");
$specializations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Dashboard</title>
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
                        <h1>Analytics</h1>
                        <p>System performance and insights</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-section">
                    <div class="chart-container">
                        <h3>Session Types Distribution</h3>
                        <canvas id="sessionTypeChart"></canvas>
                    </div>

                    <div class="chart-container">
                        <h3>Expert Specializations</h3>
                        <canvas id="specializationChart"></canvas>
                    </div>
                </div>

                <!-- Tables -->
                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Top Specializations</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Specialization</th>
                                <th>Experts Count</th>
                                <th>Avg Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($specializations as $spec): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($spec['specialization'] ?? 'N/A'); ?></td>
                                <td><?php echo $spec['count']; ?></td>
                                <td>
                                    <div class="rating">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo number_format($spec['avg_rating'] ?? 0, 1); ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Recent Revenue</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sessions</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($revenueData, 0, 10) as $revenue): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($revenue['date'])); ?></td>
                                <td><?php echo $revenue['count']; ?></td>
                                <td>₹<?php echo number_format($revenue['revenue'] ?? 0, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Session Type Chart
        const sessionTypeCtx = document.getElementById('sessionTypeChart')?.getContext('2d');
        if (sessionTypeCtx) {
            const typeData = <?php echo json_encode($sessionTypes); ?>;
            new Chart(sessionTypeCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(typeData),
                    datasets: [{
                        label: 'Sessions',
                        data: Object.values(typeData),
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Specialization Chart
        const specCtx = document.getElementById('specializationChart')?.getContext('2d');
        if (specCtx) {
            const specs = <?php echo json_encode(array_slice($specializations, 0, 6)); ?>;
            new Chart(specCtx, {
                type: 'bar',
                data: {
                    labels: specs.map(s => s.specialization),
                    datasets: [{
                        label: 'Experts',
                        data: specs.map(s => s.count),
                        backgroundColor: '#8b5cf6'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });
        }
    </script>
</body>
</html>
