<!-- Bottom Navigation Bar -->
<style>
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
    
    @media (min-width: 769px) {
        .bottom-nav {
            display: none;
        }
        body {
            padding-bottom: 0;
        }
    }
</style>

<nav class="bottom-nav">
    <div class="bottom-nav-container">
        <a href="mainhome.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'mainhome.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="experts.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'experts.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-tie"></i>
            <span>Connect</span>
        </a>
        <a href="law_ai.php" class="nav-item center-ai">
            <i class="fas fa-robot"></i>
            <span>AI Tool</span>
        </a>
        <a href="forum.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'forum.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i>
            <span>Community</span>
        </a>
        <a href="sessions.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'sessions.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>
    </div>
</nav>
