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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1600px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-primary {background: #667eea; color: white;}
        .btn-secondary {background: #f0f0f0; color: #333;}
        .btn-success {background: #28a745; color: white;}
        .btn-danger {background: #dc3545; color: white;}
        
        .stats-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;}
        .stat-card {background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .stat-icon {font-size: 36px; margin-bottom: 15px;}
        .stat-value {font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 5px;}
        .stat-label {color: #666; font-size: 14px;}
        
        .content-box {background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 25px;}
        .table {width: 100%; border-collapse: collapse; margin-top: 20px;}
        .table th {background: #f8f9fa; padding: 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6;}
        .table td {padding: 12px; border-bottom: 1px solid #f0f0f0;}
        .table tr:hover {background: #f8f9fa;}
        .badge {padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;}
        .badge-user {background: #d1ecf1; color: #0c5460;}
        .badge-expert {background: #d4edda; color: #155724;}
        .badge-admin {background: #f8d7da; color: #721c24;}
        
        select.role-select {padding: 5px 10px; border-radius: 5px; border: 2px solid #e0e0e0; font-size: 12px; font-weight: 600;}
        @media (max-width: 768px) {.stats-grid {grid-template-columns: repeat(2, 1fr);}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-shield-alt"></i> Admin Control Panel</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-secondary"><i class="fas fa-users"></i> Experts</a>
                <button class="btn btn-danger" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #667eea;"><i class="fas fa-users"></i></div>
                <div class="stat-value" id="totalUsers">0</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #28a745;"><i class="fas fa-user-tie"></i></div>
                <div class="stat-value" id="totalExperts">0</div>
                <div class="stat-label">Total Experts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #17a2b8;"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #ffc107;"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-value" id="totalRevenue">₹0</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #fd7e14;"><i class="fas fa-clock"></i></div>
                <div class="stat-value" id="pendingSessions">0</div>
                <div class="stat-label">Pending Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #6610f2;"><i class="fas fa-comments"></i></div>
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
                const response = await fetch('lib/admin_get_stats.php');
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
                const response = await fetch('lib/admin_get_all_users.php');
                const result = await response.json();
                
                if (result.data) {
                    displayUsers(result.data);
                }
            } catch (error) {
                document.getElementById('usersContainer').innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading users</p>';
            }
        }

        function displayUsers(users) {
            let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Wallet</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';
            
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
            
            html += '</tbody></table>';
            document.getElementById('usersContainer').innerHTML = html;
        }

        async function updateUserRole(userId, newRole) {
            if (!confirm('Are you sure you want to change this user\'s role?')) {
                loadUsers();
                return;
            }
            
            try {
                const response = await fetch('lib/admin_update_user_role.php', {
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
