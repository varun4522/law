<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$sessionId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$sessionId) {
    header('Location: sessions.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT cs.*, u.full_name as student_name, u.email as student_email, u.phone as student_phone,
           e.full_name as expert_name, e.email as expert_email
    FROM consultation_sessions cs
    JOIN users u ON cs.user_id = u.id
    JOIN users e ON cs.expert_id = e.id
    WHERE cs.id = ?
");
$stmt->execute([$sessionId]);
$session = $stmt->fetch();

if (!$session) {
    header('Location: sessions.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - Admin Dashboard</title>
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
                        <a href="sessions.php" style="color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">
                            <i class="fas fa-arrow-left"></i> Back to Sessions
                        </a>
                        <h1>Session #<?php echo $sessionId; ?></h1>
                        <p><?php echo date('M d, Y H:i', strtotime($session['session_date'])); ?></p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon sessions">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $session['duration']; ?> min</div>
                            <div class="stat-label">Duration</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($session['amount'] ?? 0, 2); ?></div>
                            <div class="stat-label">Amount</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon sessions">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($session['commission'] ?? 0, 2); ?></div>
                            <div class="stat-label">Commission</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon students">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $session['rating'] ? $session['rating'] . '⭐' : 'Not Rated'; ?></div>
                            <div class="stat-label">Rating</div>
                        </div>
                    </div>
                </div>

                <div class="table-container" style="margin-top: 32px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Session Information</h3>
                    <div style="padding: 20px;">
                        <table class="data-table" style="margin: 0;">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600; width: 30%;">Status</td>
                                    <td><span class="status-badge status-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Session Type</td>
                                    <td><?php echo ucfirst($session['session_type'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Session Date</td>
                                    <td><?php echo date('M d, Y H:i', strtotime($session['session_date'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Notes</td>
                                    <td><?php echo htmlspecialchars($session['notes'] ?? 'N/A'); ?></td>
                                </tr>
                                <?php if ($session['review']): ?>
                                <tr>
                                    <td style="font-weight: 600; vertical-align: top;">Review</td>
                                    <td><?php echo htmlspecialchars($session['review']); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 32px;">
                    <!-- Student Info -->
                    <div class="table-container">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Student Information</h3>
                        <div style="padding: 20px;">
                            <table class="data-table" style="margin: 0;">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: 600;">Name</td>
                                        <td><?php echo htmlspecialchars($session['student_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Email</td>
                                        <td><?php echo htmlspecialchars($session['student_email']); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Phone</td>
                                        <td><?php echo htmlspecialchars($session['student_phone'] ?? 'N/A'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expert Info -->
                    <div class="table-container">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Expert Information</h3>
                        <div style="padding: 20px;">
                            <table class="data-table" style="margin: 0;">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: 600;">Name</td>
                                        <td><?php echo htmlspecialchars($session['expert_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Email</td>
                                        <td><?php echo htmlspecialchars($session['expert_email']); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Status</td>
                                        <td><?php echo ucfirst($session['status']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
