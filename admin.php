<?php require_once 'lib/db.php'; 
$user = requireAuth(); 
if ($user['role'] !== 'admin') {
    header('Location: mainhome.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fff; color: #0a0a0a;}
        
        /* Navbar */
        .navbar {background: #0a0a0a; padding: 15px 0; position: sticky; top: 0; z-index: 100;}
        .nav-container {max-width: 1600px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; font-weight: 700; text-decoration: none;}
        .nav-links {display: flex; gap: 0; align-items: center; flex-wrap: wrap;}
        .nav-links a, .nav-links button {color: #fff; text-decoration: none; padding: 10px 20px; font-size: 14px; font-weight: 500; transition: all 0.2s; border-radius: 2px; border: none; background: transparent; cursor: pointer; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 8px;}
        .nav-links a:hover, .nav-links button:hover {background: #1a1a1a;}
        .nav-links a.active {background: #fff; color: #0a0a0a;}
        
        /* Container */
        .container {max-width: 1600px; margin: 0 auto; padding: 40px 20px;}
        
        /* Page Header */
        .page-header {margin-bottom: 40px;}
        .page-header h1 {font-family: 'Playfair Display', serif; font-size: 42px; color: #0a0a0a; margin-bottom: 10px;}
        .page-header p {color: #888; font-size: 16px;}
        
        /* Stats Grid */
        .stats-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;}
        .stat-card {background: #fafafa; padding: 30px; border-radius: 4px; border: 1px solid #e8e8e4; transition: all 0.2s;}
        .stat-card:hover {border-color: #0a0a0a;}
        .stat-icon {font-size: 36px; margin-bottom: 20px; opacity: 0.9;}
        .stat-value {font-size: 36px; font-weight: 700; color: #0a0a0a; margin-bottom: 8px;}
        .stat-label {color: #888; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;}
        
        /* Content Box */
        .content-box {background: #fafafa; padding: 30px; border-radius: 4px; border: 1px solid #e8e8e4; margin-bottom: 25px;}
        .content-box h2 {font-family: 'Playfair Display', serif; font-size: 28px; color: #0a0a0a; margin-bottom: 25px;}
        
        /* Table */
        .table-container {background: #fff; border-radius: 4px; overflow-x: auto; border: 1px solid #e8e8e4;}
        .table {width: 100%; border-collapse: collapse;}
        .table th {background: #0a0a0a; color: #fff; padding: 15px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;}
        .table td {padding: 15px; border-bottom: 1px solid #e8e8e4; font-size: 14px;}
        .table tr:last-child td {border-bottom: none;}
        .table tbody tr:hover {background: #fafafa;}
        
        /* Badges */
        .badge {padding: 5px 12px; border-radius: 2px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
        .badge-user {background: #f5f5f3; color: #0a0a0a; border: 1px solid #e8e8e4;}
        .badge-expert {background: #0a0a0a; color: #fff;}
        .badge-admin {background: #333; color: #fff;}
        
        /* Select */
        select.role-select {padding: 8px 12px; border-radius: 2px; border: 1px solid #ddd; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; background: #fff; transition: all 0.2s;}
        select.role-select:focus {outline: none; border-color: #0a0a0a;}
        
        @media (max-width: 768px) {
            .stats-grid {grid-template-columns: repeat(2, 1fr);}
            .page-header h1 {font-size: 32px;}
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="admin.php" class="logo">Law Connectors Admin</a>
            <div class="nav-links">
                <a href="admin.php" class="active"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="admin_payments.php"><i class="fas fa-money-bill"></i> Payments</a>
                <a href="mainhome.php"><i class="fas fa-home"></i> Main Site</a>
                <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Admin Control Panel</h1>
            <p>Manage users, monitor system statistics, and oversee platform operations</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-users"></i></div>
                <div class="stat-value" id="totalUsers">0</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-user-tie"></i></div>
                <div class="stat-value" id="totalExperts">0</div>
                <div class="stat-label">Total Experts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-value" id="totalRevenue">₹0</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-clock"></i></div>
                <div class="stat-value" id="pendingSessions">0</div>
                <div class="stat-label">Pending Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #0a0a0a;"><i class="fas fa-comments"></i></div>
                <div class="stat-value" id="forumQuestions">0</div>
                <div class="stat-label">Forum Questions</div>
            </div>
        </div>

        <!-- User Management -->
        <div class="content-box">
            <h2 style="margin-bottom: 20px; color: #333;">User Management</h2>
            <div id="usersContainer">
                <p style="text-align: center; padding: 40px; color: #999;">Loading users...</p>
            </div>
        </div>
    </div>

    <script>
        async function loadStats() {
            try {
                const response = await fetch('lib/admin/admin_get_stats.php');
                const result = await response.json();
                
                if (result.data) {
                    document.getElementById('totalUsers').textContent = result.data.total_users;
                    document.getElementById('totalExperts').textContent = result.data.total_experts;
                    document.getElementById('totalSessions').textContent = result.data.total_sessions;
                    document.getElementById('totalRevenue').textContent = '₹' + parseFloat(result.data.total_revenue).toFixed(2);
                    document.getElementById('pendingSessions').textContent = result.data.pending_sessions;
                    document.getElementById('forumQuestions').textContent = result.data.forum_questions;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        async function loadUsers() {
            try {
                const response = await fetch('lib/admin/admin_get_all_users.php');
                const result = await response.json();
                
                if (result.data) {
                    displayUsers(result.data);
                }
            } catch (error) {
                document.getElementById('usersContainer').innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading users</p>';
            }
        }

        function displayUsers(users) {
            let html = '<div class="table-container"><table class="table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Wallet Balance</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';
            
            users.forEach(user => {
                const badgeClass = user.role === 'admin' ? 'badge-admin' : (user.role === 'expert' ? 'badge-expert' : 'badge-user');
                const date = new Date(user.created_at).toLocaleDateString();
                
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td><span class="badge ${badgeClass}">${user.role.toUpperCase()}</span></td>
                        <td>₹${parseFloat(user.wallet_balance).toFixed(2)}</td>
                        <td>${date}</td>
                        <td>
                            <select class="role-select" onchange="updateUserRole(${user.id}, this.value)">
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                                <option value="expert" ${user.role === 'expert' ? 'selected' : ''}>Expert</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            </select>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            document.getElementById('usersContainer').innerHTML = html;
        }

        async function updateUserRole(userId, newRole) {
            if (!confirm('Are you sure you want to change this user\'s role?')) {
                loadUsers();
                return;
            }
            
            try {
                const response = await fetch('lib/admin/admin_update_user_role.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({user_id: userId, role: newRole})
                });
                
                const result = await response.json();
                
                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('User role updated successfully!');
                    loadUsers();
                    loadStats();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        async function logout() {
            try {
                await fetch('lib/logout.php');
                window.location.href = 'index.php';
            } catch (error) {
                window.location.href = 'index.php';
            }
        }

        window.addEventListener('load', () => {
            loadStats();
            loadUsers();
        });
    </script>
</body>
</html>
