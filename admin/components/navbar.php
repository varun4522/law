<!-- Top Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search users, experts, sessions...">
            </div>
        </div>

        <div class="navbar-right">
            <div class="notification-bell">
                <button class="icon-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount">0</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <button class="close-btn" onclick="document.getElementById('notificationDropdown').style.display='none'">×</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <p class="empty-message">No new notifications</p>
                    </div>
                </div>
            </div>

            <div class="user-profile">
                <button class="profile-btn" onclick="toggleProfileMenu()">
                    <div class="avatar">
                        <?php echo strtoupper(substr($adminUser['full_name'], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <div class="name"><?php echo htmlspecialchars($adminUser['full_name']); ?></div>
                        <div class="role">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </button>

                <div class="profile-menu" id="profileMenu">
                    <a href="#profile" class="menu-item">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                    <a href="#settings" class="menu-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="#help" class="menu-item">
                        <i class="fas fa-question-circle"></i> Help & Support
                    </a>
                    <hr>
                    <a href="../lib/logout.php" class="menu-item logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.getElementById('notificationBtn')?.addEventListener('click', function() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
});

function toggleProfileMenu() {
    const menu = document.getElementById('profileMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.notification-bell') && !e.target.closest('.user-profile')) {
        document.getElementById('notificationDropdown').style.display = 'none';
        document.getElementById('profileMenu').style.display = 'none';
    }
});

// Load notifications
async function loadNotifications() {
    try {
        const response = await fetch('api/get_notifications.php');
        const data = await response.json();
        const badge = document.getElementById('notificationCount');
        const list = document.getElementById('notificationList');
        
        badge.textContent = data.count || 0;
        if (data.notifications && data.notifications.length > 0) {
            list.innerHTML = data.notifications.map(n => `
                <div class="notification-item">
                    <div class="notification-icon ${n.type}">
                        <i class="fas fa-${n.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <p>${n.message}</p>
                        <small>${n.time}</small>
                    </div>
                </div>
            `).join('');
        }
    } catch (e) {
        console.error('Failed to load notifications:', e);
    }
}

loadNotifications();
setInterval(loadNotifications, 30000); // Refresh every 30 seconds
</script>
