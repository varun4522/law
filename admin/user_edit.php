<?php 
require_once __DIR__ . '/../lib/db.php'; 
$adminUser = requireRole(ROLE_ADMIN);

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userId) {
    header('Location: manage_users.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = intval($_POST['role'] ?? 1);
    $status = $_POST['status'] ?? 'active';
    $bio = trim($_POST['bio'] ?? '');

    if (empty($fullName)) {
        $message = 'Full name is required';
        $messageType = 'error';
    } else {
        try {
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET full_name = ?, phone = ?, role = ?, status = ?, bio = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $updateStmt->execute([$fullName, $phone, $role, $status, $bio, $userId]);
            
            // Reload user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $message = 'User updated successfully';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error updating user: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - <?php echo htmlspecialchars($user['full_name']); ?></title>
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
            max-width: 800px;
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

        .form-card {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 32px;
        }

        .message {
            padding: 12px 16px;
            border-radius: 2px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }
        .message.success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .form-group {
            margin-bottom: 24px;
        }
        .form-group:last-child { margin-bottom: 0; }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: #0a0a0a;
        }
        .form-label-hint {
            font-size: 12px;
            color: #888;
            font-weight: 400;
            margin-top: 4px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #0a0a0a;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-divider {
            border-top: 1px solid #e8e8e4;
            margin: 32px 0;
            padding-top: 32px;
        }
        .form-divider h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
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
        <a href="user_detail.php?id=<?php echo $userId; ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <div class="form-card">
            <h1 style="margin-bottom: 24px; font-family: 'Playfair Display', serif; font-size: 28px;">
                Edit User: <?php echo htmlspecialchars($user['full_name']); ?>
            </h1>

            <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Basic Info Section -->
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Basic Information</h3>

                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required />
                </div>

                <div class="form-group">
                    <label for="email">Email (Read-only)</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled />
                    <div class="form-label-hint">Email cannot be changed</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" />
                    </div>
                </div>

                <!-- Bio -->
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" placeholder="User bio or description"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <!-- Role & Status Section -->
                <div class="form-divider">
                    <h3>Role & Status</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">User Role *</label>
                        <select id="role" name="role" required>
                            <option value="1" <?php echo $user['role'] == 1 ? 'selected' : ''; ?>>
                                1 - Student (Default)
                            </option>
                            <option value="2" <?php echo $user['role'] == 2 ? 'selected' : ''; ?>>
                                2 - Expert (Legal Expert)
                            </option>
                            <option value="3" <?php echo $user['role'] == 3 ? 'selected' : ''; ?>>
                                3 - Admin (Administrator)
                            </option>
                        </select>
                        <div class="form-label-hint">
                            Changing role to Expert (2) requires expert profile setup. Admin role grants full system access.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Account Status *</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>
                                Inactive
                            </option>
                        </select>
                        <div class="form-label-hint">
                            Inactive users cannot access their accounts.
                        </div>
                    </div>
                </div>

                <!-- System Info (Read-only) -->
                <div class="form-divider">
                    <h3>System Information</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" value="<?php echo $user['id']; ?>" disabled />
                    </div>
                    <div class="form-group">
                        <label>Joined Date</label>
                        <input type="text" value="<?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?>" disabled />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Last Updated</label>
                        <input type="text" value="<?php echo date('M d, Y H:i', strtotime($user['updated_at'])); ?>" disabled />
                    </div>
                    <div class="form-group">
                        <label>Wallet Balance</label>
                        <input type="text" value="₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?>" disabled />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="user_detail.php?id=<?php echo $userId; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
