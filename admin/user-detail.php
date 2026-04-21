<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userId) {
    header('Location: users.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit;
}

// Get expert profile if exists
$expertProfile = null;
if ($user['role'] == ROLE_EXPERT) {
    $stmt = $pdo->prepare("SELECT * FROM expert_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $expertProfile = $stmt->fetch();
}

// Get consultation history
$stmt = $pdo->prepare("
    SELECT cs.*, other_user.full_name as other_name 
    FROM consultation_sessions cs
    LEFT JOIN users other_user ON 
        CASE WHEN cs.user_id = ? THEN cs.expert_id ELSE cs.user_id END = other_user.id
    WHERE cs.user_id = ? OR cs.expert_id = ?
    ORDER BY cs.created_at DESC
    LIMIT 10
");
$stmt->execute([$userId, $userId, $userId]);
$consultations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['full_name']); ?> - Admin Dashboard</title>
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
                        <a href="users.php" style="color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-secondary" onclick="editUser()">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>

                <!-- User Info Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo getRoleName($user['role']); ?></div>
                            <div class="stat-label">Role</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo ucfirst($user['status']); ?></div>
                            <div class="stat-label">Status</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></div>
                            <div class="stat-label">Phone</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                            <div class="stat-label">Joined</div>
                        </div>
                    </div>
                </div>

                <!-- Expert Profile -->
                <?php if ($expertProfile): ?>
                <div class="table-container" style="margin-top: 32px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Expert Profile</h3>
                    <div style="padding: 20px;">
                        <table class="data-table" style="margin: 0;">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600; width: 30%;">Specialization</td>
                                    <td><?php echo htmlspecialchars($expertProfile['specialization'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Experience</td>
                                    <td><?php echo $expertProfile['years_experience'] ?? 0; ?> years</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Hourly Rate</td>
                                    <td>₹<?php echo number_format($expertProfile['hourly_rate'] ?? 0, 0); ?>/hour</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Rating</td>
                                    <td>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo number_format($expertProfile['rating'] ?? 0, 1); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Verification Status</td>
                                    <td><span class="status-badge status-<?php echo $expertProfile['verification_status']; ?>"><?php echo ucfirst($expertProfile['verification_status']); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Bio</td>
                                    <td><?php echo htmlspecialchars(substr($expertProfile['bio'] ?? '', 0, 200)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Consultation History -->
                <div class="table-container" style="margin-top: 32px;">
                    <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Consultation History</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>With</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($consultations)): ?>
                            <tr>
                                <td colspan="6" class="empty-message">No consultations yet</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($consultations as $cons): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', strtotime($cons['session_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($cons['other_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo $cons['duration']; ?> min</td>
                                    <td>₹<?php echo number_format($cons['amount'] ?? 0, 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $cons['status']; ?>"><?php echo ucfirst($cons['status']); ?></span></td>
                                    <td><?php echo $cons['rating'] ? $cons['rating'] . '⭐' : 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editUser() {
            // Implement edit functionality
            alert('Edit functionality to be implemented');
        }
    </script>
</body>
</html>
