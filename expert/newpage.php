<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Dashboard - Law Connectors</title>
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
        .expert-badge {
            background: #0a0a0a;
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
        .earnings-badge {
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
        .user-menu:hover { background: #eaeae6; }
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
        .status-toggle {
            position: absolute;
            top: 32px;
            right: 48px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1;
        }
        .status-label {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.5px;
        }
        .toggle-switch {
            position: relative;
            width: 48px;
            height: 26px;
            cursor: pointer;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 26px;
            transition: 0.3s;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            left: 3px; bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }
        .toggle-switch input:checked + .toggle-slider { background: #22c55e; }
        .toggle-switch input:checked + .toggle-slider::before { transform: translateX(22px); }
        .online-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #6b7280;
            display: inline-block;
            margin-right: 4px;
            transition: background 0.3s;
        }
        .online-dot.online { background: #22c55e; }

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
        .action-card p { color: #888; font-size: 14px; line-height: 1.6; }

        /* ── Pending Requests Section ── */
        .requests-section { margin-bottom: 40px; }
        .request-list { display: flex; flex-direction: column; gap: 14px; }
        .request-card {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 22px 26px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.2s;
        }
        .request-card:hover {
            border-color: #0a0a0a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .request-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: #0a0a0a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .request-info { flex: 1; }
        .request-name {
            font-weight: 600;
            font-size: 15px;
            color: #0a0a0a;
            margin-bottom: 4px;
        }
        .request-meta {
            font-size: 13px;
            color: #888;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .request-meta span { display: flex; align-items: center; gap: 4px; }
        .request-actions { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-accept {
            padding: 8px 18px;
            background: #0a0a0a;
            color: white;
            border: none;
            border-radius: 2px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-accept:hover { background: #1a1a1a; }
        .btn-decline {
            padding: 8px 18px;
            background: white;
            color: #666;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-decline:hover { background: #fee2e2; color: #dc2626; border-color: #dc2626; }
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
        }
        .empty-state i { font-size: 40px; color: #ddd; margin-bottom: 12px; display: block; }
        .empty-state p { color: #aaa; font-size: 15px; }

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
            cursor: pointer;
            background: none;
            border: none;
            font-family: inherit;
        }
        .nav-item i { font-size: 22px; }
        .nav-item span { font-size: 11px; font-weight: 500; letter-spacing: 0.3px; }
        .nav-item:hover, .nav-item.active { color: #0a0a0a; }
        .nav-item.center-fab {
            position: relative;
            top: -20px;
            background: #0a0a0a;
            color: #fff;
            width: 70px; height: 70px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            padding: 0;
            min-width: unset;
            border: 4px solid #fff;
        }
        .nav-item.center-fab i { font-size: 28px; }
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
        .nav-item.center-fab:hover { background: #1a1a1a; transform: scale(1.05); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .navbar-container { flex-wrap: wrap; height: auto; padding: 12px 16px; gap: 12px; }
            .nav-right { width: 100%; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
            .container { padding: 24px 16px; }
            .welcome-section { padding: 32px 24px; }
            .welcome-section h1 { font-size: 26px; }
            .welcome-section h1 span.name-highlight { font-size: 32px; }
            .welcome-tagline { font-size: 22px; }
            .welcome-quote { font-size: 18px; margin-top: 20px; }
            .welcome-section p { font-size: 14px; }
            .status-toggle { position: static; margin-top: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-value { font-size: 22px; }
            .quick-actions { grid-template-columns: 1fr; gap: 12px; }
            .action-card { padding: 20px; }
            .request-card { flex-wrap: wrap; }
            .request-actions { width: 100%; }
            .btn-accept, .btn-decline { flex: 1; text-align: center; }
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
            <a href="newpage.php" class="logo">
                <i class="fas fa-balance-scale"></i>
                Law Connectors
                <span class="expert-badge">Expert</span>
            </a>
            <div class="nav-right">
                <div class="earnings-badge">
                    <i class="fas fa-indian-rupee-sign"></i>
                    <span id="totalEarnings">0.00</span>
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count" id="notificationCount">0</span>
                </div>
                <div class="user-menu">
                    <div class="user-avatar" id="userAvatar">E</div>
                    <span class="user-name" id="userName">Expert</span>
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
            <div class="status-toggle">
                <span class="online-dot" id="onlineDot"></span>
                <span class="status-label" id="statusLabel">Offline</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="availabilityToggle" onchange="toggleAvailability(this)">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <span class="welcome-tagline">Your Professional Legal Hub</span>
            <h1>Welcome back, <span class="name-highlight" id="welcomeName">Counsellor</span>!</h1>
            <p>Manage your consultations, respond to client requests, and grow your legal practice — all from one place.</p>
            <span class="welcome-quote">"The first duty of society is justice." &mdash; Alexander Hamilton</span>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                </div>
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-value" id="pendingCount">0</div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-value" id="completedCount">0</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                </div>
                <div class="stat-value" id="communityAnswers">0</div>
                <div class="stat-label">Community Answers</div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="requests-section">
            <h2 class="section-title">Pending Requests</h2>
            <span class="section-subtitle">Clients awaiting your response</span>
            <div class="section-divider"></div>
            <div class="request-list" id="pendingList">
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Loading requests...</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <span class="section-subtitle">Everything at your fingertips</span>
        <div class="section-divider"></div>
        <div class="quick-actions">
            <a href="sessions.php" class="action-card">
                <span class="action-icon"><i class="fas fa-calendar-alt"></i></span>
                <h3>My Sessions</h3>
                <span class="action-card-label">All Consultations</span>
                <p>View, manage and track all your client consultation sessions.</p>
            </a>
            <a href="../student/community.php" class="action-card">
                <span class="action-icon"><i class="fas fa-comments"></i></span>
                <h3>Community Forum</h3>
                <span class="action-card-label">Give Back &amp; Build Reputation</span>
                <p>Answer legal questions from the community and build your profile.</p>
            </a>
            <a href="profile.php" class="action-card">
                <span class="action-icon"><i class="fas fa-user-tie"></i></span>
                <h3>Expert Profile</h3>
                <span class="action-card-label">Your Public Presence</span>
                <p>Update your bio, specialisations, hourly rate, and availability.</p>
            </a>
            <a href="earnings.php" class="action-card">
                <span class="action-icon"><i class="fas fa-wallet"></i></span>
                <h3>Earnings & Wallet</h3>
                <span class="action-card-label">Financial Overview</span>
                <p>Track your earnings, payout history, and request withdrawals.</p>
            </a>
            <a href="../student/aitool.php" class="action-card">
                <span class="action-icon"><i class="fas fa-robot"></i></span>
                <h3>Law AI Assistant</h3>
                <span class="action-card-label">Research Tool</span>
                <p>Use AI-powered legal research to help prepare for consultations.</p>
            </a>
            <a href="../student/connect.php" class="action-card">
                <span class="action-icon"><i class="fas fa-users"></i></span>
                <h3>Find Peers</h3>
                <span class="action-card-label">Expert Network</span>
                <p>Connect with other legal experts and explore the platform.</p>
            </a>
        </div>

    </div>

    <script>
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

        async function loadSessions() {
            try {
                const res  = await fetch('../lib/expert/get_my_sessions.php');
                const data = await res.json();
                if (data.data) {
                    const sessions  = data.data;
                    const pending   = sessions.filter(s => s.status === 'pending');
                    const completed = sessions.filter(s => s.status === 'completed');

                    document.getElementById('totalSessions').textContent  = sessions.length;
                    document.getElementById('pendingCount').textContent   = pending.length;
                    document.getElementById('completedCount').textContent = completed.length;

                    renderPendingRequests(pending);
                }
            } catch (e) {
                console.error('Sessions error', e);
                renderPendingRequests([]);
            }
        }

        function renderPendingRequests(pending) {
            const list = document.getElementById('pendingList');
            if (!pending.length) {
                list.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending requests — you're all caught up!</p>
                    </div>`;
                return;
            }
            list.innerHTML = pending.map(s => {
                const initials = (s.student_name || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                const date     = s.scheduled_at ? new Date(s.scheduled_at).toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' }) : 'TBD';
                return `
                <div class="request-card">
                    <div class="request-avatar">${initials}</div>
                    <div class="request-info">
                        <div class="request-name">${s.student_name || 'Client'}</div>
                        <div class="request-meta">
                            <span><i class="fas fa-tag"></i> ${s.category || 'General'}</span>
                            <span><i class="fas fa-calendar"></i> ${date}</span>
                            <span><i class="fas fa-clock"></i> ${s.duration || 60} min</span>
                        </div>
                    </div>
                    <div class="request-actions">
                        <button class="btn-accept" onclick="respondSession(${s.id}, 'confirmed')">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button class="btn-decline" onclick="respondSession(${s.id}, 'cancelled')">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </div>
                </div>`;
            }).join('');
        }

        async function respondSession(sessionId, status) {
            try {
                const res  = await fetch('../lib/expert/update_session_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ session_id: sessionId, status })
                });
                const data = await res.json();
                if (data.success) loadSessions();
            } catch (e) { console.error('Respond error', e); }
        }

        async function loadEarnings() {
            try {
                const res  = await fetch('../lib/expert/get_earnings.php');
                const data = await res.json();
                if (data.data) {
                    document.getElementById('totalEarnings').textContent =
                        parseFloat(data.data.total || 0).toFixed(2);
                }
            } catch (e) { console.error('Earnings error', e); }
        }

        async function loadNotifications() {
            try {
                const res  = await fetch('../lib/get_notifications.php?unread_only=true');
                const data = await res.json();
                if (data.data) {
                    document.getElementById('notificationCount').textContent = data.data.length;
                }
            } catch (e) { console.error('Notification error', e); }
        }

        async function loadCommunityStats() {
            try {
                const res  = await fetch('../lib/forum_get_answers.php?mine=true');
                const data = await res.json();
                if (data.data) {
                    document.getElementById('communityAnswers').textContent = data.data.length;
                }
            } catch (e) { /* optional endpoint */ }
        }

        async function toggleAvailability(checkbox) {
            const isOnline = checkbox.checked;
            document.getElementById('onlineDot').className  = 'online-dot' + (isOnline ? ' online' : '');
            document.getElementById('statusLabel').textContent = isOnline ? 'Online' : 'Offline';
            try {
                await fetch('../lib/expert/update_availability.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ available: isOnline })
                });
            } catch (e) { console.error('Availability error', e); }
        }

        async function logout() {
            try {
                const res  = await fetch('../lib/logout.php');
                const data = await res.json();
                if (data.success) window.location.href = '../index.php';
            } catch (e) { window.location.href = '../index.php'; }
        }

        window.addEventListener('load', () => {
            loadProfile();
            loadSessions();
            loadEarnings();
            loadNotifications();
            loadCommunityStats();
        });
    </script>

    <!-- Bottom Nav (mobile) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="newpage.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="sessions.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Sessions</span>
            </a>
            <a href="../student/community.php" class="nav-item center-fab">
                <i class="fas fa-comments"></i>
                <span>Forum</span>
            </a>
            <a href="earnings.php" class="nav-item">
                <i class="fas fa-wallet"></i>
                <span>Earnings</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Profile</span>
            </a>
        </div>
    </nav>

</body>
</html>
