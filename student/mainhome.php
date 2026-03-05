<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafafa;
            min-height: 100vh;
            color: #0a0a0a;
        }
        
        /* Navigation Bar */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e4;
            padding: 0;
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
        .logo i {
            font-size: 24px;
            color: #0a0a0a;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .wallet-badge {
            background: #0a0a0a;
            color: white;
            padding: 8px 16px;
            border-radius: 2px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }
        .notification-icon {
            position: relative;
            font-size: 20px;
            color: #6b7280;
            cursor: pointer;
            padding: 8px;
            transition: color 0.2s;
        }
        .notification-icon:hover {color: #1a1a1a;}
        .notification-count {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            padding: 0 5px;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            border-radius: 2px;
            background: #f5f5f3;
            cursor: pointer;
            transition: background 0.2s;
        }
        .user-menu:hover {background: #eaeae6;}
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #0a0a0a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-name {
            font-weight: 500;
            font-size: 14px;
            color: #0a0a0a;
        }
        .logout-btn {
            padding: 8px 16px;
            background: #fff;
            color: #666;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }
        .logout-btn:hover {
            background: #0a0a0a;
            color: white;
            border-color: #0a0a0a;
        }
        
        /* Cursive Typography */
        .cursive {
            font-family: 'Dancing Script', cursive;
            font-weight: 600;
        }
        .cursive-fancy {
            font-family: 'Great Vibes', cursive;
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 32px;
        }
        
        /* Welcome Section */
        .welcome-section {
            background: #0a0a0a;
            border-radius: 4px;
            padding: 52px 48px;
            margin-bottom: 40px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .welcome-section::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .welcome-section::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: 40px;
            width: 160px;
            height: 160px;
            background: rgba(255,255,255,0.02);
            border-radius: 50%;
        }
        .welcome-tagline {
            font-family: 'Great Vibes', cursive;
            font-size: 28px;
            color: rgba(255,255,255,0.55);
            margin-bottom: 12px;
            position: relative;
            display: block;
            letter-spacing: 0.5px;
        }
        .welcome-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .welcome-section h1 span.name-highlight {
            font-family: 'Dancing Script', cursive;
            font-size: 42px;
            font-weight: 700;
            color: #e8e8e4;
        }
        .welcome-section p {
            font-size: 16px;
            opacity: 0.75;
            position: relative;
            max-width: 560px;
            line-height: 1.7;
        }
        .welcome-quote {
            display: block;
            margin-top: 28px;
            font-family: 'Dancing Script', cursive;
            font-size: 22px;
            color: rgba(255,255,255,0.45);
            position: relative;
            padding-left: 20px;
            border-left: 2px solid rgba(255,255,255,0.2);
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 4px;
            border: 1px solid #e8e8e4;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-color: #0a0a0a;
        }
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 14px;
            color: #888;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }
        
        /* Section Title */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .section-subtitle {
            font-family: 'Dancing Script', cursive;
            font-size: 18px;
            color: #888;
            margin-bottom: 24px;
            display: block;
            font-weight: 500;
        }
        .section-divider {
            width: 48px;
            height: 3px;
            background: #0a0a0a;
            margin-bottom: 28px;
            border-radius: 2px;
        }
        
        /* Quick Actions Grid */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .action-card {
            background: white;
            padding: 32px;
            border-radius: 4px;
            border: 1px solid #e8e8e4;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            position: relative;
            overflow: hidden;
        }
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #0a0a0a;
            transform: scaleY(0);
            transition: transform 0.3s;
        }
        .action-card:hover::before {
            transform: scaleY(1);
        }
        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: #0a0a0a;
        }
        .action-icon {
            font-size: 36px;
            margin-bottom: 20px;
            display: block;
            color: #0a0a0a;
        }
        .action-card h3 {
            color: #0a0a0a;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .action-card-label {
            font-family: 'Dancing Script', cursive;
            font-size: 14px;
            color: #bbb;
            display: block;
            margin-bottom: 10px;
        }
        .action-card p {
            color: #888;
            font-size: 14px;
            line-height: 1.6;
        }
        
        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e8e8e4;
            padding: 12px 0;
            z-index: 1000;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
        }
        .bottom-nav-container {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: relative;
            padding: 0 20px;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #888;
            transition: all 0.2s;
            padding: 8px 12px;
            border-radius: 4px;
            min-width: 70px;
            cursor: pointer;
        }
        .nav-item i {
            font-size: 22px;
            transition: all 0.2s;
        }
        .nav-item span {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        .nav-item:hover {
            color: #0a0a0a;
        }
        .nav-item.active {
            color: #0a0a0a;
        }
        
        /* Center AI Button - Large and Elevated */
        .nav-item.center-ai {
            position: relative;
            top: -20px;
            background: #0a0a0a;
            color: #fff;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            padding: 0;
            min-width: unset;
            border: 4px solid #fff;
        }
        .nav-item.center-ai i {
            font-size: 32px;
            margin: 0;
        }
        .nav-item.center-ai span {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            white-space: nowrap;
            color: #0a0a0a;
            font-weight: 600;
        }
        .nav-item.center-ai:hover {
            background: #1a1a1a;
            transform: scale(1.05);
        }
        
        /* Add padding at bottom for fixed nav */
        body {
            padding-bottom: 90px;
        }
        
        @media (max-width: 768px) {
            .navbar-container {
                flex-wrap: wrap;
                height: auto;
                padding: 12px 16px;
                gap: 12px;
            }
            .logo {
                font-size: 18px;
            }
            .logo i {
                font-size: 20px;
            }
            .nav-right {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 8px;
            }
            .btn {
                font-size: 13px;
                padding: 8px 14px;
            }
            .container {
                padding: 24px 16px;
            }
            .welcome-section {
                padding: 32px 24px;
            }
            .welcome-section h1 {
                font-size: 26px;
            }
            .welcome-section h1 span.name-highlight {
                font-size: 32px;
            }
            .welcome-tagline {
                font-size: 22px;
            }
            .welcome-quote {
                font-size: 18px;
                margin-top: 20px;
            }
            .welcome-section p {
                font-size: 14px;
            }
            .section {
                padding: 20px 16px;
            }
            .section-header {
                margin-bottom: 16px;
            }
            .section-header h2 {
                font-size: 21px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .stat-card {
                padding: 16px;
            }
            .stat-info h3 {
                font-size: 24px;
            }
            .stat-info p {
                font-size: 12px;
            }
            .quick-actions {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .action-card {
                padding: 16px;
            }
            .action-card h3 {
                font-size: 16px;
            }
            .action-card p {
                font-size: 13px;
            }
            .bottom-nav {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .bottom-nav {
                display: none;
            }
            body {
                padding-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="mainhome.php" class="logo">
                <i class="fas fa-balance-scale"></i>
                Law Connectors
            </a>
            <div class="nav-right">
                <div class="wallet-badge">
                    <i class="fas fa-wallet"></i>
                    ₹<span id="walletBalance">0.00</span>
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count" id="notificationCount">0</span>
                </div>
                <div class="user-menu">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <span class="user-name" id="userName">User</span>
                </div>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <span class="welcome-tagline">Your Legal Journey Starts Here</span>
            <h1>Welcome back, <span class="name-highlight" id="welcomeName">User</span>!</h1>
            <p>Your one-stop platform for legal consultation, expert advice, and legal resources tailored for you.</p>
            <span class="welcome-quote">"Justice delayed is justice denied." &mdash; William Gladstone</span>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f5f5f3; color: #0a0a0a;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f5f5f3; color: #0a0a0a;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value" id="upcomingSessions">0</div>
                <div class="stat-label">Upcoming Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f5f5f3; color: #0a0a0a;">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
                <div class="stat-value" id="forumQuestions">0</div>
                <div class="stat-label">Forum Questions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f5f5f3; color: #0a0a0a;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="stat-value" id="walletBalanceStat">₹0</div>
                <div class="stat-label">Wallet Balance</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <span class="section-subtitle">Everything you need, right at your fingertips</span>
        <div class="section-divider"></div>
        <div id="quickActionsContainer" class="quick-actions">
            <!-- Dynamic content loaded based on user role -->
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
                    <a href="admin.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Admin Panel</h3>
                        <span class="action-card-label">Control & Oversight</span>
                        <p>Manage users, experts, and monitor system performance</p>
                    </a>
                `;
            }

            // Expert-specific card
            if (role === 'expert' || role === 'admin') {
                html += `
                    <a href="expert_dashboard.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Expert Dashboard</h3>
                        <span class="action-card-label">Your Professional Space</span>
                        <p>Manage session requests and update your profile</p>
                    </a>
                `;
            }

            // Law AI - Available to everyone
            html += `
                <a href="aitool.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>Law AI Assistant</h3>
                    <span class="action-card-label">Powered by Intelligence</span>
                    <p>Get instant legal answers 24/7 from our AI</p>
                </a>
            `;

            // Common cards for all users
            html += `
                <a href="connect.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Find Experts</h3>
                    <span class="action-card-label">Connect & Consult</span>
                    <p>Browse verified legal experts and book consultations</p>
                </a>
                
                <a href="profile.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3>My Profile</h3>
                    <span class="action-card-label">Your Account</span>
                    <p>View sessions, wallet balance and update your profile</p>
                </a>
                
                <a href="community.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Ask a Lawyer</h3>
                    <span class="action-card-label">Community Wisdom</span>
                    <p>Get free answers from legal experts in our community forum</p>
                </a>
            `;

            container.innerHTML = html;
        }

        async function loadWalletBalance() {
            try {
                const response = await fetch('lib/student/get_wallet_balance.php');
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
                const response = await fetch('lib/student/get_my_sessions.php');
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

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="mainhome.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="connect.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Connect</span>
            </a>
            <a href="aitool.php" class="nav-item center-ai">
                <i class="fas fa-robot"></i>
                <span>AI Tool</span>
            </a>
            <a href="community.php" class="nav-item">
                <i class="fas fa-comments"></i>
                <span>Community</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>
