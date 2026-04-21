<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-section">
            <h2 class="logo">
                <i class="fas fa-gavel"></i> LawConnect
            </h2>
            <span class="admin-badge">ADMIN</span>
        </div>
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-title">MAIN</div>
            <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">MANAGEMENT</div>
            <a href="users.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="experts.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'experts.php' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i>
                <span>Experts</span>
            </a>
            <a href="sessions.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'sessions.php' ? 'active' : ''; ?>">
                <i class="fas fa-video"></i>
                <span>Consultations</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">MODERATION</div>
            <a href="reports.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-flag"></i>
                <span>Reports</span>
            </a>
            <a href="content.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'content.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Content</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">ANALYTICS</div>
            <a href="analytics.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
            <a href="revenue.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'revenue.php' ? 'active' : ''; ?>">
                <i class="fas fa-rupee-sign"></i>
                <span>Revenue</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-title">SETTINGS</div>
            <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="logs.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="../lib/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
}
</script>
