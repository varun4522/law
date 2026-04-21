<?php 
require_once __DIR__ . '/../lib/db.php'; 
$user = requireRole(ROLE_ADMIN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
        .admin-badge {
            background: #0a0a0a;
            color: white;
            padding: 4px 8px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 6px;
            letter-spacing: 0.3px;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            border-radius: 2px;
            background: #f5f5f3;
            cursor: pointer;
        }
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
        .logout-btn {
            padding: 8px 16px;
            background: #fff;
            color: #666;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .logout-btn:hover { background: #0a0a0a; color: white; }

        /* ── Container ── */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* ── Header ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #0a0a0a;
        }
        .search-bar {
            flex: 1;
            max-width: 400px;
            margin-left: auto;
            margin-right: 16px;
        }
        .search-input {
            width: 100%;
            padding: 10px 16px;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .search-input:focus {
            outline: none;
            border-color: #0a0a0a;
        }

        /* ── Stats Cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 20px;
            transition: all 0.2s;
        }
        .stat-card:hover {
            border-color: #0a0a0a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
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
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Table ── */
        .table-card {
            background: white;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            overflow: hidden;
        }
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e8e8e4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
        }
        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .filter-tab {
            padding: 8px 16px;
            background: #f5f5f3;
            border: 1px solid transparent;
            border-radius: 2px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s;
        }
        .filter-tab:hover {
            background: #eaeae6;
        }
        .filter-tab.active {
            background: #0a0a0a;
            color: white;
            border-color: #0a0a0a;
        }

        .table-wrapper {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        thead {
            background: #f5f5f3;
            border-bottom: 1px solid #e8e8e4;
        }
        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 16px;
            border-bottom: 1px solid #f0f0ec;
        }
        tbody tr:hover {
            background: #fafaf9;
        }

        /* ── User Cell ── */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0a0a0a, #333);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-info h4 {
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 4px;
        }
        .user-info p {
            font-size: 12px;
            color: #888;
        }

        /* ── Role Badge ── */
        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .role-badge.student {
            background: #dbeafe;
            color: #1e40af;
        }
        .role-badge.expert {
            background: #dcfce7;
            color: #15803d;
        }
        .role-badge.admin {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── Status Badge ── */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 2px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-badge.active {
            background: #dcfce7;
            color: #15803d;
        }
        .status-badge.inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* ── Actions ── */
        .actions {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            background: #0a0a0a;
            color: white;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-sm:hover {
            background: #333;
        }
        .btn-sm.secondary {
            background: #f5f5f3;
            color: #0a0a0a;
            border: 1px solid #ddd;
        }
        .btn-sm.secondary:hover {
            background: #eaeae6;
        }

        /* ── Pagination ── */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 20px;
            border-top: 1px solid #e8e8e4;
        }
        .pagination button, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 2px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
        }
        .pagination button:hover {
            background: #0a0a0a;
            color: white;
            border-color: #0a0a0a;
        }
        .pagination .active {
            background: #0a0a0a;
            color: white;
            border-color: #0a0a0a;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 16px;
        }
        .empty-state p {
            color: #888;
            font-size: 16px;
        }

        /* ── Loading ── */
        .loading {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        .spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid #ddd;
            border-top-color: #0a0a0a;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .search-bar {
                max-width: 100%;
                margin: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 12px 8px;
            }
            .user-cell {
                gap: 8px;
            }
            .actions {
                flex-wrap: wrap;
            }
        }

        /* ── Bottom Nav ── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e8e8e4;
            display: none;
            z-index: 50;
        }
        @media (max-width: 768px) {
            .bottom-nav { display: block; }
        }
        .bottom-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 70px;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #666;
            font-size: 11px;
            font-weight: 500;
            transition: color 0.2s;
            padding: 8px;
        }
        .nav-item:hover, .nav-item.active {
            color: #0a0a0a;
        }
        .nav-item i { font-size: 20px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="1newpage.php" class="logo">
                <i class="fas fa-gavel"></i>
                Law Connectors
                <span class="admin-badge">Admin</span>
            </a>
            <div class="user-menu">
                <div class="user-avatar"><?php echo strtoupper(substr($user['email'] ?? '', 0, 1)); ?></div>
                <span class="user-name"><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></span>
                <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Manage Users</h1>
            <div class="search-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="Search users by name or email..." />
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" id="totalUsers">0</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalStudents">0</div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalExperts">0</div>
                <div class="stat-label">Experts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="totalAdmins">0</div>
                <div class="stat-label">Admins</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterByRole('all')">All Users</button>
            <button class="filter-tab" onclick="filterByRole('1')">Students (Role 1)</button>
            <button class="filter-tab" onclick="filterByRole('2')">Experts (Role 2)</button>
            <button class="filter-tab" onclick="filterByRole('3')">Admins (Role 3)</button>
        </div>

        <!-- Users Table -->
        <div class="table-card">
            <div class="table-header">
                <h3>All Users</h3>
            </div>
            <div class="table-wrapper">
                <div id="usersTableContainer" class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Nav (mobile) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="1newpage.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="manage_users.php" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="manage_experts.php" class="nav-item">
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

    <script>
        let allUsers = [];
        let currentFilter = 'all';
        let searchTerm = '';

        // Fetch all users
        async function loadUsers() {
            try {
                const res = await fetch('../lib/get_all_users.php');
                const data = await res.json();
                
                if (data.success) {
                    allUsers = data.data || [];
                    updateStats();
                    renderTable();
                } else {
                    showError('Failed to load users');
                }
            } catch (e) {
                console.error(e);
                showError('Error loading users');
            }
        }

        // Update statistics
        function updateStats() {
            const total = allUsers.length;
            const students = allUsers.filter(u => u.role == 1).length;
            const experts = allUsers.filter(u => u.role == 2).length;
            const admins = allUsers.filter(u => u.role == 3).length;

            document.getElementById('totalUsers').textContent = total;
            document.getElementById('totalStudents').textContent = students;
            document.getElementById('totalExperts').textContent = experts;
            document.getElementById('totalAdmins').textContent = admins;
        }

        // Filter by role
        function filterByRole(role) {
            currentFilter = role;
            document.querySelectorAll('.filter-tab').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            renderTable();
        }

        // Get role name
        function getRoleName(role) {
            const roles = { 1: 'Student', 2: 'Expert', 3: 'Admin' };
            return roles[role] || 'Unknown';
        }

        // Get role badge class
        function getRoleBadgeClass(role) {
            const classes = { 1: 'student', 2: 'expert', 3: 'admin' };
            return classes[role] || 'student';
        }

        // Render table
        function renderTable() {
            const container = document.getElementById('usersTableContainer');
            
            // Filter data
            let filtered = allUsers;
            if (currentFilter !== 'all') {
                filtered = allUsers.filter(u => u.role == currentFilter);
            }
            if (searchTerm) {
                filtered = filtered.filter(u => 
                    u.full_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    u.email.toLowerCase().includes(searchTerm.toLowerCase())
                );
            }

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No users found</p>
                    </div>
                `;
                return;
            }

            // Create table
            let html = '<table>';
            html += `
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            `;

            filtered.forEach(user => {
                const roleName = getRoleName(user.role);
                const roleBadgeClass = getRoleBadgeClass(user.role);
                const joined = new Date(user.created_at).toLocaleDateString();
                const status = user.status === 'active' ? 'Active' : 'Inactive';

                html += `
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar-small">${user.full_name.charAt(0).toUpperCase()}</div>
                                <div class="user-info">
                                    <h4>${user.full_name || 'N/A'}</h4>
                                </div>
                            </div>
                        </td>
                        <td>${user.email}</td>
                        <td>
                            <span class="role-badge ${roleBadgeClass}">${roleName}</span>
                        </td>
                        <td>
                            <span class="status-badge ${user.status === 'active' ? 'active' : 'inactive'}">${status}</span>
                        </td>
                        <td>${joined}</td>
                        <td>
                            <div class="actions">
                                <button class="btn-sm secondary" onclick="viewUser(${user.id})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-sm secondary" onclick="editUser(${user.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        }

        // View user
        function viewUser(id) {
            window.location.href = `user_detail.php?id=${id}`;
        }

        // Edit user
        function editUser(id) {
            window.location.href = `user_edit.php?id=${id}`;
        }

        // Search
        document.getElementById('searchInput')?.addEventListener('input', (e) => {
            searchTerm = e.target.value;
            renderTable();
        });

        // Show error
        function showError(msg) {
            const container = document.getElementById('usersTableContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${msg}</p>
                </div>
            `;
        }

        // Logout
        async function logout() {
            try {
                const res = await fetch('../lib/logout.php');
                const data = await res.json();
                if (data.success) window.location.href = '../index.php';
            } catch (e) {
                window.location.href = '../index.php';
            }
        }

        // Load on page load
        window.addEventListener('load', loadUsers);
    </script>

</body>
</html>
