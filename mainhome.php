<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1400px; margin: 0 auto;}
        
        /* Header */
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .user-section {display: flex; align-items: center; gap: 20px;}
        .wallet-badge {background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 15px; border-radius: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;}
        .notification-bell {position: relative; font-size: 24px; color: #667eea; cursor: pointer;}
        .notification-count {position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .user-info {display: flex; align-items: center; gap: 10px;}
        .user-avatar {width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .logout-btn {padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;}
        .logout-btn:hover {background: #c82333;}
        
        /* Welcome Banner */
        .welcome-banner {background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .welcome-banner h1 {color: #333; margin-bottom: 10px;}
        .welcome-banner p {color: #666; font-size: 16px;}
        
        /* Quick Actions Grid */
        .quick-actions {display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;}
        .action-card {background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s; text-decoration: none; display: block;}
        .action-card:hover {transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15);}
        .action-icon {font-size: 48px; margin-bottom: 15px;}
        .action-card h3 {color: #333; margin-bottom: 8px;}
        .action-card p {color: #999; font-size: 14px;}
        
        /* Stats Row */
        .stats-row {display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;}
        .stat-box {background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .stat-value {font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 5px;}
        .stat-label {color: #666; font-size: 14px;}
        
        @media (max-width: 768px) {
            .header {flex-direction: column; align-items: stretch;}
            .user-section {flex-direction: column;}
            .quick-actions {grid-template-columns: 1fr;}
            .stats-row {grid-template-columns: repeat(2, 1fr);}
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo"><i class="fas fa-balance-scale"></i> Law Connectors</div>
            <div class="user-section">
                <div class="wallet-badge"><i class="fas fa-wallet"></i> ₹<span id="walletBalance">0.00</span></div>
                <div class="notification-bell" onclick="window.location.href='#'">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count" id="notificationCount">0</span>
                </div>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <span id="userName">User</span>
                </div>
                <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h1>Welcome back, <span id="welcomeName">User</span>!</h1>
            <p>Your one-stop platform for legal consultation, expert advice, and legal resources.</p>
        </div>

        <!-- Quick Actions -->
        <div id="quickActionsContainer" class="quick-actions">
            <!-- Dynamic content loaded based on user role -->
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="upcomingSessions">0</div>
                <div class="stat-label">Upcoming Sessions</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="forumQuestions">0</div>
                <div class="stat-label">Forum Questions</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" id="walletBalanceStat">₹0</div>
                <div class="stat-label">Wallet Balance</div>
            </div>
        </div>
    </div>

    <script>
        let currentUser = null;

        async function loadUserProfile() {
            try {
                const response = await fetch('lib/get_profile.php');
                const result = await response.json();
                
                if (result.data) {
                    currentUser = result.data;
                    const displayName = result.data.full_name || result.data.email;
                    document.getElementById('userName').textContent = displayName;
                    document.getElementById('welcomeName').textContent = displayName;
                    const initials = displayName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0,2);
                    document.getElementById('userAvatar').textContent = initials;
                    
                    // Load role-specific quick actions
                    loadQuickActions(result.data.role);
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        function loadQuickActions(role) {
            const container = document.getElementById('quickActionsContainer');
            let html = '';

            // Admin-specific card
            if (role === 'admin') {
                html += `
                    <a href="admin.php" class="action-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                        <div class="action-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3 style="color: white;">Admin Panel</h3>
                        <p style="color: rgba(255,255,255,0.9);">Manage users, experts, and monitor system</p>
                    </a>
                `;
            }

            // Expert-specific card
            if (role === 'expert' || role === 'admin') {
                html += `
                    <a href="expert_dashboard.php" class="action-card" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white;">
                        <div class="action-icon"><i class="fas fa-user-tie"></i></div>
                        <h3 style="color: white;">Expert Dashboard</h3>
                        <p style="color: rgba(255,255,255,0.9);">Manage session requests and your profile</p>
                    </a>
                `;
            }

            // Law AI - Available to everyone
            html += `
                <a href="law_ai.php" class="action-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="action-icon"><i class="fas fa-robot"></i></div>
                    <h3 style="color: white;">Law AI Assistant</h3>
                    <p style="color: rgba(255,255,255,0.9);">Get instant legal answers 24/7 from our AI</p>
                </a>
            `;

            // Common cards for all users
            html += `
                <a href="experts.php" class="action-card">
                    <div class="action-icon" style="color: #6610f2;"><i class="fas fa-users"></i></div>
                    <h3>Find Experts</h3>
                    <p>Browse verified legal experts and book consultations</p>
                </a>
                
                <a href="sessions.php" class="action-card">
                    <div class="action-icon" style="color: #28a745;"><i class="fas fa-calendar-check"></i></div>
                    <h3>My Sessions</h3>
                    <p>View and manage your consultation bookings</p>
                </a>
                
                <a href="forum.php" class="action-card">
                    <div class="action-icon" style="color: #17a2b8;"><i class="fas fa-comments"></i></div>
                    <h3>Ask a Lawyer</h3>
                    <p>Get free answers from legal experts in our community forum</p>
                </a>
                
                <a href="wallet.php" class="action-card">
                    <div class="action-icon" style="color: #ffc107;"><i class="fas fa-wallet"></i></div>
                    <h3>Wallet</h3>
                    <p>Manage your balance and view transaction history</p>
                </a>
            `;

            container.innerHTML = html;
        }

        async function loadWalletBalance() {
            try {
                const response = await fetch('lib/get_wallet_balance.php');
                const result = await response.json();
                
                if (result.data) {
                    const balance = parseFloat(result.data.balance).toFixed(2);
                    document.getElementById('walletBalance').textContent = balance;
                    document.getElementById('walletBalanceStat').textContent = '₹' + balance;
                }
            } catch (error) {
                console.error('Error loading balance:', error);
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch('lib/get_notifications.php?unread_only=true');
                const result = await response.json();
                
                if (result.data) {
                    document.getElementById('notificationCount').textContent = result.data.length;
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('lib/get_my_sessions.php');
                const result = await response.json();
                
                if (result.data) {
                    document.getElementById('totalSessions').textContent = result.data.length;
                    const upcoming = result.data.filter(s => s.status === 'pending' || s.status === 'confirmed').length;
                    document.getElementById('upcomingSessions').textContent = upcoming;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }

            try {
                const response = await fetch('lib/forum_get_questions.php');
                const result = await response.json();
                
                if (result.data) {
                    document.getElementById('forumQuestions').textContent = result.data.length;
                }
            } catch (error) {
                console.error('Error loading forum stats:', error);
            }
        }

        async function logout() {
            try {
                const response = await fetch('lib/logout.php');
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Error logging out:', error);
                window.location.href = 'index.php';
            }
        }

        window.addEventListener('load', () => {
            loadUserProfile();
            loadWalletBalance();
            loadNotifications();
            loadStats();
        });
    </script>
</body>
</html>
