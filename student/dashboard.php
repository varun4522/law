<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();
$userId = $student['id'];

// Get student statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultation_sessions WHERE user_id = ?");
$stmt->execute([$userId]);
$totalSessions = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultation_sessions WHERE user_id = ? AND status IN ('completed', 'in_progress')");
$stmt->execute([$userId]);
$completedSessions = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM consultation_sessions WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$userId]);
$pendingSessions = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT AVG(CAST(rating AS FLOAT)) as avg_rating FROM consultation_sessions WHERE user_id = ? AND rating IS NOT NULL");
$stmt->execute([$userId]);
$avgRating = $stmt->fetch()['avg_rating'] ?? 0;

// Get upcoming sessions
$stmt = $pdo->prepare("
    SELECT cs.*, e.full_name as expert_name, ep.specialization 
    FROM consultation_sessions cs
    JOIN users e ON cs.expert_id = e.id
    JOIN expert_profiles ep ON ep.user_id = e.id
    WHERE cs.user_id = ? AND cs.session_date > NOW()
    ORDER BY cs.session_date ASC
    LIMIT 5
");
$stmt->execute([$userId]);
$upcomingSessions = $stmt->fetchAll();

// Get recent articles
$stmt = $pdo->prepare("
    SELECT * FROM data_records 
    WHERE types = 'article' AND is_public = 1 AND status = 'published'
    ORDER BY created_at DESC
    LIMIT 6
");
$stmt->execute();
$recentArticles = $stmt->fetchAll();

// Get trending experts
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, ep.specialization, ep.hourly_rate, ep.rating, COUNT(cs.id) as session_count
    FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    LEFT JOIN consultation_sessions cs ON cs.expert_id = u.id AND cs.status != 'cancelled'
    WHERE u.role = ? AND ep.verification_status = 'verified' AND u.status = 'active'
    GROUP BY u.id
    ORDER BY ep.rating DESC, session_count DESC
    LIMIT 6
");
$stmt->execute([ROLE_EXPERT]);
$trendingExperts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LawConnect Student</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/student-styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="logo">
                <i class="fas fa-gavel"></i> LawConnect
            </a>
            
            <div class="nav-center">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="experts.php" class="nav-link">Experts</a>
                <a href="articles.php" class="nav-link">Articles</a>
                <a href="community.php" class="nav-link">Community</a>
                <a href="sessions.php" class="nav-link">My Sessions</a>
            </div>

            <div class="nav-right">
                <button class="search-btn" onclick="openSearchModal()">
                    <i class="fas fa-search"></i>
                </button>
                <div class="user-menu">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <div class="avatar"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars(substr($student['full_name'], 0, 15)); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                        <a href="settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <a href="wishlist.php" class="dropdown-item">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                        <hr>
                        <a href="../lib/logout.php" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="student-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Quick Access</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="book-session.php"><i class="fas fa-calendar"></i> Book Expert</a></li>
                <li><a href="sessions.php"><i class="fas fa-video"></i> My Sessions</a></li>
                <li><a href="articles.php"><i class="fas fa-book"></i> Learn</a></li>
                <li><a href="community.php"><i class="fas fa-comments"></i> Community</a></li>
                <li><a href="profile.php"><i class="fas fa-id-card"></i> Profile</a></li>
            </ul>

            <div class="sidebar-promo">
                <div class="promo-card">
                    <h4>Getting Started?</h4>
                    <p>Browse our expert advocates and book your first consultation</p>
                    <a href="experts.php" class="btn btn-small">Explore</a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-text">
                    <h1>Welcome back, <?php echo htmlspecialchars($student['full_name']); ?>! 👋</h1>
                    <p>Find the perfect legal expert for your consultation</p>
                </div>
                <a href="book-session.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Book Expert Now
                </a>
            </div>

            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-icon sessions">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $totalSessions; ?></div>
                        <div class="stat-label">Total Sessions</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $completedSessions; ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $pendingSessions; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon rating">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($avgRating, 1); ?></div>
                        <div class="stat-label">Avg Rating</div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Sessions -->
            <?php if (!empty($upcomingSessions)): ?>
            <section class="upcoming-section">
                <div class="section-header">
                    <h2>Upcoming Sessions</h2>
                    <a href="sessions.php" class="view-all">View All</a>
                </div>
                <div class="sessions-grid">
                    <?php foreach ($upcomingSessions as $session): ?>
                    <div class="session-card">
                        <div class="session-header">
                            <div class="expert-info">
                                <div class="expert-avatar">
                                    <?php echo strtoupper(substr($session['expert_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4><?php echo htmlspecialchars($session['expert_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($session['specialization']); ?></p>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $session['status']; ?>">
                                <?php echo ucfirst($session['status']); ?>
                            </span>
                        </div>
                        <div class="session-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($session['session_date'])); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <?php echo date('h:i A', strtotime($session['session_date'])); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-hourglass-half"></i>
                                <?php echo $session['duration']; ?> min
                            </div>
                        </div>
                        <div class="session-footer">
                            <span class="price">₹<?php echo number_format($session['amount'] ?? 0, 0); ?></span>
                            <a href="session-detail.php?id=<?php echo $session['id']; ?>" class="btn btn-small">Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Trending Experts -->
            <section class="experts-section">
                <div class="section-header">
                    <h2>Top Rated Experts</h2>
                    <a href="experts.php" class="view-all">View All Experts</a>
                </div>
                <div class="experts-grid">
                    <?php foreach ($trendingExperts as $expert): ?>
                    <div class="expert-card">
                        <div class="expert-header">
                            <div class="expert-avatar large">
                                <?php echo strtoupper(substr($expert['full_name'], 0, 1)); ?>
                            </div>
                            <div class="expert-badge">
                                <i class="fas fa-star"></i> <?php echo number_format($expert['rating'] ?? 0, 1); ?>
                            </div>
                        </div>
                        <h3><?php echo htmlspecialchars($expert['full_name']); ?></h3>
                        <p class="specialization"><?php echo htmlspecialchars($expert['specialization'] ?? 'General Law'); ?></p>
                        <div class="expert-meta">
                            <span><i class="fas fa-video"></i> <?php echo $expert['session_count']; ?> sessions</span>
                            <span><i class="fas fa-rupee-sign"></i> ₹<?php echo $expert['hourly_rate']; ?>/hr</span>
                        </div>
                        <a href="expert-profile.php?id=<?php echo $expert['id']; ?>" class="btn btn-secondary btn-block">
                            View Profile
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Recent Articles -->
            <?php if (!empty($recentArticles)): ?>
            <section class="articles-section">
                <div class="section-header">
                    <h2>Learn Law</h2>
                    <a href="articles.php" class="view-all">See All Articles</a>
                </div>
                <div class="articles-grid">
                    <?php foreach ($recentArticles as $article): ?>
                    <article class="article-card">
                        <div class="article-header">
                            <span class="article-type"><?php echo ucfirst($article['types']); ?></span>
                            <span class="read-time">5 min read</span>
                        </div>
                        <h3><?php echo htmlspecialchars(substr($article['title'], 0, 60)); ?></h3>
                        <p><?php echo htmlspecialchars(substr($article['description'], 0, 100)); ?>...</p>
                        <div class="article-footer">
                            <span class="date"><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                            <a href="article.php?id=<?php echo $article['id']; ?>" class="read-more">Read More →</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Quick Tips -->
            <section class="tips-section">
                <h2>Quick Tips</h2>
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">📋</div>
                        <h4>Prepare Questions</h4>
                        <p>Write down your legal questions before the session for better discussion</p>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">⏰</div>
                        <h4>Be On Time</h4>
                        <p>Join 5 minutes early to ensure a smooth consultation experience</p>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">💡</div>
                        <h4>Take Notes</h4>
                        <p>Document important points discussed during your session</p>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">⭐</div>
                        <h4>Rate Your Expert</h4>
                        <p>Share your feedback to help other students find great experts</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Search Modal -->
    <div class="modal" id="searchModal">
        <div class="modal-content search-modal-content">
            <button class="modal-close" onclick="closeSearchModal()">×</button>
            <div class="search-header">
                <h2>Search Experts & Articles</h2>
            </div>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for experts, articles, topics..." class="search-input" onkeyup="performSearch(this.value)">
            </div>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>

    <script src="../assets/student-common.js"></script>
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'flex';
            document.getElementById('searchInput').focus();
        }

        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }

        function performSearch(query) {
            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }

            fetch(`api/search.php?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.results);
                    }
                });
        }

        function displaySearchResults(results) {
            const resultsDiv = document.getElementById('searchResults');
            if (results.length === 0) {
                resultsDiv.innerHTML = '<p class="no-results">No results found</p>';
                return;
            }

            resultsDiv.innerHTML = results.map(r => `
                <a href="${r.url}" class="search-result">
                    <i class="fas fa-${r.icon}"></i>
                    <div>
                        <div class="result-title">${r.title}</div>
                        <div class="result-type">${r.type}</div>
                    </div>
                </a>
            `).join('');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu')) {
                document.getElementById('userDropdown').style.display = 'none';
            }
        });

        // Close search modal when clicking outside
        window.onclick = function(e) {
            const modal = document.getElementById('searchModal');
            if (e.target === modal) {
                closeSearchModal();
            }
        }
    </script>
</body>
</html>
