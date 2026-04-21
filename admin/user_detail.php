<?php 
require_once __DIR__ . '/../lib/db.php'; 
$adminUser = requireRole(ROLE_ADMIN);

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userId) {
    header('Location: manage_users.php');
    exit;
}

// Get user details
$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT u.*, 
        (SELECT COUNT(*) FROM expert_profiles ep WHERE ep.user_id = u.id) as has_expert_profile,
        (SELECT COUNT(*) FROM consultation_sessions cs WHERE cs.user_id = u.id) as total_sessions,
        (SELECT COUNT(*) FROM consultation_sessions cs WHERE cs.user_id = u.id AND cs.status = 'completed') as completed_sessions
    FROM users u 
    WHERE u.id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: manage_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['full_name']); ?> - User Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafafa;
            min-height: 100vh;
            color: #0a0a0a;
            padding-bottom: 90px;
        }

        .navbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e4;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #0a0a0a;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .admin-badge {
            background: #0a0a0a;
            color: white;
            padding: 4px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .back-btn {
            padding: 8px 16px;
            background: #f5f5f3;
            border: 1px solid #ddd;
            border-radius: 2px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #0a0a0a;
        }
        .back-btn:hover { background: #eaeae6; }

        .profile-card {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 32px;
            margin-bottom: 24px;
        }
        
        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 32px;
            padding-bottom: 32px;
            border-bottom: 1px solid #e8e8e4;
        }
        
        .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0a0a0a, #333);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 48px;
            flex-shrink: 0;
        }
        
        .profile-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .profile-meta {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            font-size: 14px;
        }

        .role-badge, .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .role-badge.student { background: #dbeafe; color: #1e40af; }
        .role-badge.expert { background: #dcfce7; color: #15803d; }
        .role-badge.admin { background: #fee2e2; color: #991b1b; }
        .status-badge.active { background: #dcfce7; color: #15803d; }
        .status-badge.inactive { background: #f3f4f6; color: #6b7280; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
        }
        .info-item {
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0ec;
        }
        .info-item:last-child { border-bottom: none; }
        .info-label {
            font-size: 12px;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #0a0a0a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #f5f5f3;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 16px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 2px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #0a0a0a;
            color: white;
        }
        .btn-primary:hover { background: #333; }
        .btn-secondary {
            background: #f5f5f3;
            color: #0a0a0a;
            border: 1px solid #ddd;
        }
        .btn-secondary:hover { background: #eaeae6; }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-container">
            <a href="1newpage.php" class="logo">
                <i class="fas fa-gavel"></i>
                Law Connectors
                <span class="admin-badge">Admin</span>
            </a>
        </div>
    </nav>

    <div class="container">
        <a href="manage_users.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>

        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-large">
                    <?php echo strtoupper(substr($user['full_name'] ?? '', 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></h1>
                    <div class="profile-meta">
                        <span class="role-badge <?php 
                            echo $user['role'] == 2 ? 'expert' : ($user['role'] == 3 ? 'admin' : 'student');
                        ?>">
                            <?php 
                                $roles = [1 => 'Student', 2 => 'Expert', 3 => 'Admin'];
                                echo $roles[$user['role']] ?? 'Unknown';
                            ?>
                        </span>
                        <span class="status-badge <?php echo $user['status'] === 'active' ? 'active' : 'inactive'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </div>
                    <div class="info-grid" style="margin-top: 16px;">
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Joined</div>
                            <div class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Wallet Balance</div>
                            <div class="info-value">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($user['bio']): ?>
            <div class="info-item" style="border: none; margin-bottom: 24px;">
                <div class="info-label">Bio</div>
                <div class="info-value"><?php echo htmlspecialchars($user['bio']); ?></div>
            </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $user['total_sessions']; ?></div>
                    <div class="stat-label">Total Sessions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $user['completed_sessions']; ?></div>
                    <div class="stat-label">Completed Sessions</div>
                </div>
                <?php if ($user['has_expert_profile']): ?>
                <div class="stat-card">
                    <div class="stat-value"><i class="fas fa-check" style="color: #16a34a;"></i></div>
                    <div class="stat-label">Expert Profile</div>
                </div>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="user_edit.php?id=<?php echo $userId; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                <button class="btn btn-secondary" onclick="alert('Delete functionality coming soon')">
                    <i class="fas fa-trash"></i> Delete User
                </button>
            </div>
        </div>
    </div>

</body>
</html>
