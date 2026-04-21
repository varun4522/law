<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
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
                    <h1>Settings</h1>
                    <p>System configuration and preferences</p>
                </div>

                <!-- Settings Sections -->
                <div style="max-width: 800px;">
                    <!-- System Settings -->
                    <div class="table-container">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Platform Settings</h3>
                        <form style="padding: 20px;">
                            <div class="form-group">
                                <label>Platform Name</label>
                                <input type="text" value="LawConnect" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Support Email</label>
                                <input type="email" value="support@lawconnect.in" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Commission Percentage</label>
                                <div style="display: flex; gap: 8px;">
                                    <input type="number" min="0" max="100" value="20" class="form-control" style="max-width: 100px;">
                                    <span style="display: flex; align-items: center;">%</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Session Timeout (minutes)</label>
                                <input type="number" value="30" class="form-control" style="max-width: 150px;">
                            </div>

                            <button type="button" class="btn btn-primary" style="margin-top: 16px;">Save Changes</button>
                        </form>
                    </div>

                    <!-- Notification Settings -->
                    <div class="table-container" style="margin-top: 24px;">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Notifications</h3>
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                <div>
                                    <p style="font-weight: 600;">Session Alerts</p>
                                    <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">Get notified of new consultation bookings</p>
                                </div>
                                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                <div>
                                    <p style="font-weight: 600;">Expert Verifications</p>
                                    <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">Notify when experts submit verification requests</p>
                                </div>
                                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                                <div>
                                    <p style="font-weight: 600;">Content Reports</p>
                                    <p style="font-size: 13px; color: #6b7280; margin-top: 4px;">Notify when content is reported</p>
                                </div>
                                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="table-container" style="margin-top: 24px;">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0;">Account Settings</h3>
                        <form style="padding: 20px;">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($adminUser['full_name']); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($adminUser['email']); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Change Password</label>
                                <input type="password" placeholder="Enter new password" class="form-control">
                            </div>

                            <button type="button" class="btn btn-primary" style="margin-top: 16px;">Update Account</button>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="table-container" style="margin-top: 24px; border-color: #fee2e2;">
                        <h3 style="padding: 20px 20px 0; margin-bottom: 0; color: #991b1b;">Danger Zone</h3>
                        <div style="padding: 20px;">
                            <p style="color: #6b7280; margin-bottom: 16px;">Irreversible and dangerous actions</p>
                            <button type="button" class="btn" style="background: #fee2e2; color: #991b1b;">
                                <i class="fas fa-trash"></i> Clear All Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
