<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
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

        /* ── Navbar ── */
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
        .logo i { font-size: 24px; }
        .admin-badge {
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 2px;
            text-transform: uppercase;
            margin-left: 4px;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .revenue-badge {
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
        .notification-icon:hover { color: #1a1a1a; }
        .notification-count {
            position: absolute;
            top: 2px; right: 2px;
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
        .user-menu:hover { background: #eaeae6; }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #dc2626;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-name { font-weight: 500; font-size: 14px; color: #0a0a0a; }
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
        .logout-btn:hover { background: #0a0a0a; color: white; border-color: #0a0a0a; }

        /* ── Container ── */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* ── Welcome Section ── */
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
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .welcome-section::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 40px;
            width: 160px; height: 160px;
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
        .welcome-meta {
            position: absolute;
            top: 32px; right: 48px;
            z-index: 1;
            text-align: right;
        }
        .welcome-meta .date-label {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .welcome-meta .time-display {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: rgba(255,255,255,0.85);
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
            width: 48px; height: 48px;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: #f5f5f3;
            color: #0a0a0a;
        }
        .stat-icon.red   { background: #fee2e2; color: #dc2626; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.blue  { background: #dbeafe; color: #2563eb; }
        .stat-icon.amber { background: #fef3c7; color: #d97706; }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px;
            color: #888;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-trend {
            font-size: 12px;
            font-weight: 600;
            margin-top: 6px;
        }
        .stat-trend.up   { color: #16a34a; }
        .stat-trend.down { color: #dc2626; }

        /* ── Section titles ── */
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
            width: 48px; height: 3px;
            background: #0a0a0a;
            margin-bottom: 28px;
            border-radius: 2px;
        }

        /* ── Two-column layout ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }

        /* ── Approval / List cards ── */
        .panel {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            overflow: hidden;
        }
        .panel-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e8e8e4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .panel-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: #0a0a0a;
        }
        .panel-header a {
            font-size: 13px;
            font-weight: 600;
            color: #888;
            text-decoration: none;
            transition: color 0.2s;
        }
        .panel-header a:hover { color: #0a0a0a; }
        .panel-body { padding: 0; }

        .item-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 24px;
            border-bottom: 1px solid #f0f0ec;
            transition: background 0.15s;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row:hover { background: #fafaf8; }
        .item-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #0a0a0a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .item-avatar.expert-av { background: #2563eb; }
        .item-info { flex: 1; min-width: 0; }
        .item-name {
            font-weight: 600;
            font-size: 14px;
            color: #0a0a0a;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-sub {
            font-size: 12px;
            color: #999;
        }
        .item-actions { display: flex; gap: 8px; flex-shrink: 0; }
        .btn-sm-approve {
            padding: 6px 14px;
            background: #0a0a0a;
            color: white;
            border: none;
            border-radius: 2px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-sm-approve:hover { background: #1a1a1a; }
        .btn-sm-reject {
            padding: 6px 14px;
            background: white;
            color: #666;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-sm-reject:hover { background: #fee2e2; color: #dc2626; border-color: #dc2626; }

        .status-pill {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .pill-pending   { background: #fef3c7; color: #d97706; }
        .pill-confirmed { background: #dcfce7; color: #16a34a; }
        .pill-cancelled { background: #fee2e2; color: #dc2626; }
        .pill-completed { background: #dbeafe; color: #2563eb; }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        .empty-state i { font-size: 36px; color: #ddd; margin-bottom: 10px; display: block; }
        .empty-state p { color: #aaa; font-size: 14px; }

        /* ── Quick Actions ── */
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
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: #0a0a0a;
            transform: scaleY(0);
            transition: transform 0.3s;
        }
        .action-card:hover::before { transform: scaleY(1); }
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
        .action-card h3 { color: #0a0a0a; font-size: 18px; font-weight: 600; margin-bottom: 6px; }
        .action-card-label {
            font-family: 'Dancing Script', cursive;
            font-size: 14px;
            color: #bbb;
            display: block;
            margin-bottom: 10px;
        }
        .action-card p { color: #888; font-size: 14px; line-height: 1.6; }

        /* ── Bottom Nav ── */
        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
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
            background: none;
            border: none;
            font-family: inherit;
            cursor: pointer;
        }
        .nav-item i { font-size: 22px; }
        .nav-item span { font-size: 11px; font-weight: 500; letter-spacing: 0.3px; }
        .nav-item:hover, .nav-item.active { color: #0a0a0a; }
        .nav-item.center-fab {
            position: relative;
            top: -20px;
            background: #dc2626;
            color: #fff;
            width: 70px; height: 70px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(220,38,38,0.35);
            padding: 0;
            min-width: unset;
            border: 4px solid #fff;
        }
        .nav-item.center-fab i { font-size: 26px; }
        .nav-item.center-fab span {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            white-space: nowrap;
            color: #0a0a0a;
            font-weight: 600;
        }
        .nav-item.center-fab:hover { background: #b91c1c; transform: scale(1.05); }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .two-col { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .navbar-container { flex-wrap: wrap; height: auto; padding: 12px 16px; gap: 12px; }
            .nav-right { width: 100%; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
            .container { padding: 24px 16px; }
            .welcome-section { padding: 32px 24px; }
            .welcome-section h1 { font-size: 26px; }
            .welcome-section h1 span.name-highlight { font-size: 32px; }
            .welcome-tagline { font-size: 22px; }
            .welcome-meta { position: static; text-align: left; margin-top: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-value { font-size: 22px; }
            .quick-actions { grid-template-columns: 1fr; gap: 12px; }
            .action-card { padding: 20px; }
        }
        @media (min-width: 769px) {
            .bottom-nav { display: none; }
            body { padding-bottom: 0; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="1newpage.php" class="logo">
                <i class="fas fa-balance-scale"></i>
                Law Connectors
                <span class="admin-badge">Admin</span>
            </a>
            <div class="nav-right">
                <div class="revenue-badge">
                    <i class="fas fa-chart-line"></i>
                    ₹<span id="totalRevenue">0.00</span>
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count" id="notificationCount">0</span>
                </div>
                <div class="user-menu">
                    <div class="user-avatar" id="userAvatar">A</div>
                    <span class="user-name" id="userName">Admin</span>
                </div>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Main -->
    <div class="container">

        <!-- Welcome -->
        <div class="welcome-section">
            <div class="welcome-meta">
                <div class="date-label" id="dateLabel"></div>
                <div class="time-display" id="timeDisplay"></div>
            </div>
            <span class="welcome-tagline">Command & Control Centre</span>
            <h1>Welcome back, <span class="name-highlight" id="welcomeName">Admin</span>!</h1>
            <p>Monitor platform activity, approve experts, manage users, and ensure Law Connectors runs smoothly.</p>
            <span class="welcome-quote">"With great power comes great responsibility." &mdash; Stan Lee</span>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-value" id="totalUsers">0</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-trend up" id="userTrend"></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i class="fas fa-user-tie"></i></div>
                </div>
                <div class="stat-value" id="totalExperts">0</div>
                <div class="stat-label">Verified Experts</div>
                <div class="stat-trend amber" id="expertTrend"></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background:#f5f5f3; color:#0a0a0a;"><i class="fas fa-briefcase"></i></div>
                </div>
                <div class="stat-value" id="totalSessionsStat">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-value" id="pendingApprovals">0</div>
                <div class="stat-label">Pending Approvals</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green"><i class="fas fa-indian-rupee-sign"></i></div>
                </div>
                <div class="stat-value" id="revenueStat">₹0</div>
                <div class="stat-label">Platform Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue"><i class="fas fa-comments"></i></div>
                </div>
                <div class="stat-value" id="forumPosts">0</div>
                <div class="stat-label">Forum Posts</div>
            </div>
        </div>

        <!-- Expert Approvals + Recent Sessions -->
        <div class="two-col">
            <!-- Expert Approvals -->
            <div class="panel">
                <div class="panel-header">
                    <h3><i class="fas fa-user-check" style="margin-right:8px; color:#d97706;"></i>Expert Approvals</h3>
                    <a href="manage_experts.php">View all &rarr;</a>
                </div>
                <div class="panel-body" id="approvalList">
                    <div class="empty-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Recent Sessions -->
            <div class="panel">
                <div class="panel-header">
                    <h3><i class="fas fa-calendar-check" style="margin-right:8px; color:#2563eb;"></i>Recent Sessions</h3>
                    <a href="manage_sessions.php">View all &rarr;</a>
                </div>
                <div class="panel-body" id="recentSessionsList">
                    <div class="empty-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <h2 class="section-title">Recent Registrations</h2>
        <span class="section-subtitle">Newest members on the platform</span>
        <div class="section-divider"></div>
        <div class="panel" style="margin-bottom: 40px;">
            <div class="panel-body" id="recentUsersList">
                <div class="empty-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <span class="section-subtitle">Admin tools at your fingertips</span>
        <div class="section-divider"></div>
        <div class="quick-actions">
            <a href="manage_users.php" class="action-card">
                <span class="action-icon"><i class="fas fa-users-cog"></i></span>
                <h3>Manage Users</h3>
                <span class="action-card-label">User Administration</span>
                <p>View, edit, suspend, or delete student and expert accounts.</p>
            </a>
            <a href="manage_experts.php" class="action-card">
                <span class="action-icon"><i class="fas fa-user-check"></i></span>
                <h3>Manage Experts</h3>
                <span class="action-card-label">Expert Verification</span>
                <p>Approve pending expert applications and manage verified experts.</p>
            </a>
            <a href="manage_sessions.php" class="action-card">
                <span class="action-icon"><i class="fas fa-calendar-alt"></i></span>
                <h3>All Sessions</h3>
                <span class="action-card-label">Session Oversight</span>
                <p>Monitor, review, and manage all consultation sessions on the platform.</p>
            </a>
            <a href="manage_forum.php" class="action-card">
                <span class="action-icon"><i class="fas fa-comments"></i></span>
                <h3>Forum Moderation</h3>
                <span class="action-card-label">Community Health</span>
                <p>Review flagged posts, manage questions and answers in the community.</p>
            </a>
            <a href="manage_revenue.php" class="action-card">
                <span class="action-icon"><i class="fas fa-chart-bar"></i></span>
                <h3>Revenue & Reports</h3>
                <span class="action-card-label">Financial Insights</span>
                <p>View earnings, payout requests, platform fees, and financial reports.</p>
            </a>
            <a href="settings.php" class="action-card">
                <span class="action-icon"><i class="fas fa-sliders-h"></i></span>
                <h3>Platform Settings</h3>
                <span class="action-card-label">Configuration</span>
                <p>Manage site-wide settings, commission rates, and system configuration.</p>
            </a>
        </div>

    </div>

    <script>
        /* ── Clock ── */
        function updateClock() {
            const now  = new Date();
            document.getElementById('timeDisplay').textContent =
                now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('dateLabel').textContent =
                now.toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
        updateClock();
        setInterval(updateClock, 60000);

        /* ── Profile ── */
        async function loadProfile() {
            try {
                const res  = await fetch('../lib/get_profile.php');
                const data = await res.json();
                if (data.data) {
                    const name = data.data.full_name || data.data.email;
                    document.getElementById('userName').textContent    = name;
                    document.getElementById('welcomeName').textContent = name;
                    const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                    document.getElementById('userAvatar').textContent  = initials;
                }
            } catch (e) { console.error('Profile error', e); }
        }

        /* ── Platform Stats ── */
        async function loadStats() {
            try {
                const res  = await fetch('../lib/admin/get_stats.php');
                const data = await res.json();
                if (data.data) {
                    const d = data.data;
                    document.getElementById('totalUsers').textContent       = d.total_users       ?? 0;
                    document.getElementById('totalExperts').textContent     = d.total_experts     ?? 0;
                    document.getElementById('totalSessionsStat').textContent= d.total_sessions    ?? 0;
                    document.getElementById('pendingApprovals').textContent = d.pending_approvals ?? 0;
                    document.getElementById('forumPosts').textContent       = d.forum_posts       ?? 0;
                    const rev = parseFloat(d.total_revenue || 0).toFixed(2);
                    document.getElementById('revenueStat').textContent  = '₹' + rev;
                    document.getElementById('totalRevenue').textContent = rev;
                }
            } catch (e) { console.error('Stats error', e); }
        }

        /* ── Expert Approvals ── */
        async function loadApprovals() {
            const box = document.getElementById('approvalList');
            try {
                const res  = await fetch('../lib/admin/get_pending_experts.php');
                const data = await res.json();
                const list = data.data || [];
                document.getElementById('pendingApprovals').textContent = list.length;
                if (!list.length) {
                    box.innerHTML = `<div class="empty-state"><i class="fas fa-check-circle"></i><p>No pending approvals</p></div>`;
                    return;
                }
                box.innerHTML = list.slice(0, 6).map(e => {
                    const initials = (e.full_name || 'E').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                    return `
                    <div class="item-row">
                        <div class="item-avatar expert-av">${initials}</div>
                        <div class="item-info">
                            <div class="item-name">${e.full_name || 'Unknown'}</div>
                            <div class="item-sub">${e.specialisation || 'Legal Expert'} &bull; ${e.email || ''}</div>
                        </div>
                        <div class="item-actions">
                            <button class="btn-sm-approve" onclick="approveExpert(${e.id}, 'approved')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-sm-reject" onclick="approveExpert(${e.id}, 'rejected')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>`;
                }).join('');
            } catch (e) {
                box.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Could not load approvals</p></div>`;
            }
        }

        async function approveExpert(expertId, status) {
            try {
                const res  = await fetch('../lib/admin/update_expert_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ expert_id: expertId, status })
                });
                const data = await res.json();
                if (data.success) { loadApprovals(); loadStats(); }
            } catch (e) { console.error('Approve error', e); }
        }

        /* ── Recent Sessions ── */
        async function loadRecentSessions() {
            const box = document.getElementById('recentSessionsList');
            try {
                const res  = await fetch('../lib/admin/get_recent_sessions.php?limit=6');
                const data = await res.json();
                const list = data.data || [];
                if (!list.length) {
                    box.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-times"></i><p>No sessions yet</p></div>`;
                    return;
                }
                box.innerHTML = list.map(s => {
                    const pillClass = { pending:'pill-pending', confirmed:'pill-confirmed', cancelled:'pill-cancelled', completed:'pill-completed' }[s.status] || 'pill-pending';
                    return `
                    <div class="item-row">
                        <div class="item-avatar">${(s.student_name||'S')[0].toUpperCase()}</div>
                        <div class="item-info">
                            <div class="item-name">${s.student_name || 'Student'}</div>
                            <div class="item-sub">with ${s.expert_name || 'Expert'} &bull; ${s.category || 'General'}</div>
                        </div>
                        <span class="status-pill ${pillClass}">${s.status}</span>
                    </div>`;
                }).join('');
            } catch (e) {
                box.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Could not load sessions</p></div>`;
            }
        }

        /* ── Recent Users ── */
        async function loadRecentUsers() {
            const box = document.getElementById('recentUsersList');
            try {
                const res  = await fetch('../lib/admin/get_recent_users.php?limit=6');
                const data = await res.json();
                const list = data.data || [];
                if (!list.length) {
                    box.innerHTML = `<div class="empty-state"><i class="fas fa-user-slash"></i><p>No recent registrations</p></div>`;
                    return;
                }
                box.innerHTML = list.map(u => {
                    const initials = (u.full_name || u.email || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                    const role     = u.role ? u.role.charAt(0).toUpperCase() + u.role.slice(1) : 'Student';
                    const joined   = u.created_at ? new Date(u.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
                    return `
                    <div class="item-row">
                        <div class="item-avatar">${initials}</div>
                        <div class="item-info">
                            <div class="item-name">${u.full_name || u.email}</div>
                            <div class="item-sub">${u.email || ''} &bull; ${role} &bull; Joined ${joined}</div>
                        </div>
                        <button class="btn-sm-reject" onclick="window.location='manage_users.php?id=${u.id}'" style="white-space:nowrap;">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </div>`;
                }).join('');
            } catch (e) {
                box.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Could not load users</p></div>`;
            }
        }

        /* ── Notifications ── */
        async function loadNotifications() {
            try {
                const res  = await fetch('../lib/get_notifications.php?unread_only=true');
                const data = await res.json();
                if (data.data) document.getElementById('notificationCount').textContent = data.data.length;
            } catch (e) { /* silent */ }
        }

        /* ── Logout ── */
        async function logout() {
            try {
                const res  = await fetch('../lib/logout.php');
                const data = await res.json();
                if (data.success) window.location.href = '../index.php';
            } catch (e) { window.location.href = '../index.php'; }
        }

        /* ── Init ── */
        window.addEventListener('load', () => {
            loadProfile();
            loadStats();
            loadApprovals();
            loadRecentSessions();
            loadRecentUsers();
            loadNotifications();
        });
    </script>

    <!-- Bottom Nav (mobile) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="1newpage.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="manage_users.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="manage_experts.php" class="nav-item center-fab">
                <i class="fas fa-user-check"></i>
                <span>Experts</span>
            </a>
            <a href="manage_sessions.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Sessions</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-sliders-h"></i>
                <span>Settings</span>
            </a>
        </div>
    </nav>

</body>
</html>
