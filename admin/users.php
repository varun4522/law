<?php
require_once __DIR__ . '/../lib/db.php';
$adminUser = requireRole(ROLE_ADMIN);

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? intval($_GET['role']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role !== '') {
    $whereConditions[] = "role = ?";
    $params[] = $role;
}

if (!empty($status)) {
    $whereConditions[] = "status = ?";
    $params[] = $status;
}

$where = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM users $where");
$countStmt->execute($params);
$total = $countStmt->fetch()['count'];
$totalPages = ceil($total / $perPage);

// Get users
$stmt = $pdo->prepare("
    SELECT * FROM users $where 
    ORDER BY created_at DESC 
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
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
                    <div>
                        <h1>User Management</h1>
                        <p><?php echo $total; ?> total users</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="openAddUserModal()">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Search by name or email..." 
                                   value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
                        </div>
                        
                        <select name="role" class="filter-select">
                            <option value="">All Roles</option>
                            <option value="<?php echo ROLE_STUDENT; ?>" <?php echo $role == ROLE_STUDENT ? 'selected' : ''; ?>>Student</option>
                            <option value="<?php echo ROLE_EXPERT; ?>" <?php echo $role == ROLE_EXPERT ? 'selected' : ''; ?>>Expert</option>
                            <option value="<?php echo ROLE_ADMIN; ?>" <?php echo $role == ROLE_ADMIN ? 'selected' : ''; ?>>Administrator</option>
                        </select>

                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="users.php" class="btn btn-ghost">Clear</a>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="empty-message">No users found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-small"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                                            <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                    <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo getRoleName($user['role']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="user-detail.php?id=<?php echo $user['id']; ?>" class="btn-icon" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn-icon" onclick="editUser(<?php echo $user['id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-icon danger" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>" class="page-link">First</a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>" class="page-link">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>" 
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>" class="page-link">Next</a>
                        <a href="?page=<?php echo $totalPages; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>" class="page-link">Last</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New User</h2>
                <button class="modal-close" onclick="closeUserModal()">×</button>
            </div>
            <form id="userForm" onsubmit="submitUserForm(event)">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" required class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role" required class="form-control">
                            <option value="">Select Role</option>
                            <option value="<?php echo ROLE_STUDENT; ?>">Student</option>
                            <option value="<?php echo ROLE_EXPERT; ?>">Expert</option>
                            <option value="<?php echo ROLE_ADMIN; ?>">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password (leave blank to auto-generate)</label>
                    <input type="password" name="password" class="form-control">
                    <small>Auto-generated password will be sent to email if left blank</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeUserModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingUserId = null;

        function openAddUserModal() {
            editingUserId = null;
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userModal').style.display = 'flex';
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
            document.getElementById('userForm').reset();
        }

        function editUser(userId) {
            editingUserId = userId;
            document.getElementById('modalTitle').textContent = 'Edit User';
            // Load user data via API
            fetch(`api/get_user.php?id=${userId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const user = data.user;
                        document.querySelector('[name="full_name"]').value = user.full_name;
                        document.querySelector('[name="email"]').value = user.email;
                        document.querySelector('[name="phone"]').value = user.phone || '';
                        document.querySelector('[name="role"]').value = user.role;
                        document.querySelector('[name="status"]').value = user.status;
                        document.getElementById('userModal').style.display = 'flex';
                    }
                });
        }

        function submitUserForm(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('userForm'));
            const endpoint = editingUserId ? `api/update_user.php?id=${editingUserId}` : 'api/create_user.php';
            const method = editingUserId ? 'PUT' : 'POST';

            fetch(endpoint, {
                method: method,
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('User saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(e => alert('Error: ' + e.message));
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`api/delete_user.php?id=${userId}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('User deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(e => alert('Error: ' + e.message));
            }
        }

        // Close modal when clicking outside
        window.onclick = function(e) {
            const modal = document.getElementById('userModal');
            if (e.target === modal) {
                closeUserModal();
            }
        }
    </script>
</body>
</html>
