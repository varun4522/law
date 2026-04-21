<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$expertId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$expertId) {
    header('Location: experts.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT ep.*, u.id as user_id, u.full_name, u.email, u.phone, u.status 
    FROM expert_profiles ep
    JOIN users u ON ep.user_id = u.id
    WHERE ep.id = ?
");
$stmt->execute([$expertId]);
$expert = $stmt->fetch();

if (!$expert) {
    header('Location: experts.php');
    exit;
}

// Get sessions count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultation_sessions WHERE expert_id = ?");
$stmt->execute([$expert['user_id']]);
$sessionCount = $stmt->fetch()['count'];

// Get total revenue
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM consultation_sessions WHERE expert_id = ? AND status IN ('completed', 'confirmed')");
$stmt->execute([$expert['user_id']]);
$totalRevenue = $stmt->fetch()['total'] ?? 0;

// Get recent sessions
$stmt = $pdo->prepare("
    SELECT cs.*, u.full_name as student_name 
    FROM consultation_sessions cs
    JOIN users u ON cs.user_id = u.id
    WHERE cs.expert_id = ?
    ORDER BY cs.created_at DESC
    LIMIT 10
");
$stmt->execute([$expert['user_id']]);
$recentSessions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($expert['full_name']); ?> - Expert Profile</title>
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
                        <a href="experts.php" style="color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">
                            <i class="fas fa-arrow-left"></i> Back to Experts
                        </a>
                        <h1><?php echo htmlspecialchars($expert['full_name']); ?></h1>
                        <p><?php echo htmlspecialchars($expert['specialization']); ?></p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon sessions">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $sessionCount; ?></div>
                            <div class="stat-label">Total Sessions</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($totalRevenue, 0); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon experts">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($expert['rating'] ?? 0, 1); ?></div>
                            <div class="stat-label">Average Rating</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon students">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $expert['years_experience']; ?></div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="table-container" style="margin-top: 32px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Profile Information</h3>
                    <div style="padding: 20px;">
                        <table class="data-table" style="margin: 0;">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600; width: 30%;">Email</td>
                                    <td><?php echo htmlspecialchars($expert['email']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Phone</td>
                                    <td><?php echo htmlspecialchars($expert['phone'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Hourly Rate</td>
                                    <td>₹<?php echo number_format($expert['hourly_rate'] ?? 0, 0); ?>/hour</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Verification Status</td>
                                    <td><span class="status-badge status-<?php echo $expert['verification_status']; ?>"><?php echo ucfirst($expert['verification_status']); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Account Status</td>
                                    <td><span class="status-badge status-<?php echo $expert['status']; ?>"><?php echo ucfirst($expert['status']); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Bio</td>
                                    <td><?php echo htmlspecialchars($expert['bio'] ?? 'N/A'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="table-container" style="margin-top: 32px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Recent Sessions</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSessions)): ?>
                            <tr>
                                <td colspan="6" class="empty-message">No sessions yet</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($recentSessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['student_name']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($session['session_date'])); ?></td>
                                    <td><?php echo $session['duration']; ?> min</td>
                                    <td>₹<?php echo number_format($session['amount'] ?? 0, 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                                    <td><?php echo $session['rating'] ? $session['rating'] . '⭐' : 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
