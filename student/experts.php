<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();

// Get filters
$specialization = $_GET['specialization'] ?? '';
$minRating = $_GET['rating'] ?? 0;
$sortBy = $_GET['sort'] ?? 'rating';
$search = $_GET['search'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE u.role = " . ROLE_EXPERT . " AND ep.verification_status = 'verified' AND u.status = 'active'";
$params = [];

if (!empty($search)) {
    $where .= " AND (u.full_name LIKE ? OR ep.specialization LIKE ? OR ep.bio LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($specialization)) {
    $where .= " AND ep.specialization = ?";
    $params[] = $specialization;
}

$orderBy = match($sortBy) {
    'price_low' => 'ep.hourly_rate ASC',
    'price_high' => 'ep.hourly_rate DESC',
    'experience' => 'ep.years_experience DESC',
    default => 'ep.rating DESC'
};

// Get total count
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    $where
");
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Get experts
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.email, ep.specialization, ep.bio, ep.hourly_rate, 
           ep.rating, ep.years_experience, COUNT(cs.id) as session_count
    FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    LEFT JOIN consultation_sessions cs ON cs.expert_id = u.id AND cs.status != 'cancelled'
    $where
    GROUP BY u.id
    ORDER BY $orderBy
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$experts = $stmt->fetchAll();

// Get specializations for filter
$stmt = $pdo->prepare("
    SELECT DISTINCT specialization FROM expert_profiles 
    WHERE verification_status = 'verified' 
    ORDER BY specialization
");
$stmt->execute();
$specializations = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Experts - LawConnect</title>
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="experts.php" class="nav-link active">Experts</a>
                <a href="articles.php" class="nav-link">Articles</a>
                <a href="community.php" class="nav-link">Community</a>
                <a href="sessions.php" class="nav-link">My Sessions</a>
            </div>
            <div class="nav-right">
                <button class="search-btn"><i class="fas fa-search"></i></button>
                <div class="user-menu">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <div class="avatar"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        <a href="../lib/logout.php" class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="student-container">
        <!-- Sidebar Filters -->
        <aside class="filter-sidebar">
            <h3>Filters</h3>
            <form method="GET" id="filterForm">
                <!-- Search -->
                <div class="filter-group">
                    <label>Search Expert</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Name or specialization" class="filter-input">
                </div>

                <!-- Specialization -->
                <div class="filter-group">
                    <label>Specialization</label>
                    <select name="specialization" class="filter-select">
                        <option value="">All Areas</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?php echo htmlspecialchars($spec); ?>" 
                                    <?php echo $specialization === $spec ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Rating -->
                <div class="filter-group">
                    <label>Minimum Rating</label>
                    <div class="rating-filter">
                        <?php for ($i = 5; $i >= 0; $i--): ?>
                        <label class="rating-option">
                            <input type="radio" name="rating" value="<?php echo $i; ?>" 
                                   <?php echo $minRating == $i ? 'checked' : ''; ?>>
                            <?php if ($i > 0): ?>
                                <?php for ($j = 0; $j < $i; $j++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            <?php else: ?>
                                All
                            <?php endif; ?>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Sort -->
                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort" class="filter-select">
                        <option value="rating" <?php echo $sortBy === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="experience" <?php echo $sortBy === 'experience' ? 'selected' : ''; ?>>Most Experienced</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                <a href="experts.php" class="btn btn-secondary btn-block">Clear Filters</a>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="experts-list">
            <div class="page-header">
                <h1>Find Legal Experts</h1>
                <p>Browse and connect with verified legal professionals</p>
            </div>

            <?php if (empty($experts)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h2>No Experts Found</h2>
                <p>Try adjusting your filters or search criteria</p>
            </div>
            <?php else: ?>
            <div class="results-info">
                <p>Showing <?php echo count($experts); ?> of <?php echo $total; ?> experts</p>
            </div>

            <div class="experts-grid-large">
                <?php foreach ($experts as $expert): ?>
                <div class="expert-card-large">
                    <div class="expert-card-body">
                        <div class="expert-header-large">
                            <div class="expert-avatar-large">
                                <?php echo strtoupper(substr($expert['full_name'], 0, 1)); ?>
                            </div>
                            <div class="expert-header-info">
                                <h3><?php echo htmlspecialchars($expert['full_name']); ?></h3>
                                <p class="specialization"><?php echo htmlspecialchars($expert['specialization']); ?></p>
                            </div>
                        </div>

                        <div class="expert-rating">
                            <?php 
                            $rating = $expert['rating'] ?? 0;
                            for ($i = 0; $i < 5; $i++): 
                            ?>
                                <i class="fas fa-star <?php echo $i < floor($rating) ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                            <span class="rating-text"><?php echo number_format($rating, 1); ?></span>
                        </div>

                        <p class="expert-bio"><?php echo htmlspecialchars(substr($expert['bio'] ?? 'Experienced legal professional', 0, 150)); ?>...</p>

                        <div class="expert-stats">
                            <div class="stat">
                                <i class="fas fa-graduation-cap"></i>
                                <span><?php echo $expert['years_experience']; ?>+ years</span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-video"></i>
                                <span><?php echo $expert['session_count']; ?> sessions</span>
                            </div>
                            <div class="stat price">
                                <i class="fas fa-rupee-sign"></i>
                                <span><?php echo $expert['hourly_rate']; ?>/hr</span>
                            </div>
                        </div>
                    </div>

                    <div class="expert-card-footer">
                        <a href="expert-profile.php?id=<?php echo $expert['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                        <a href="book-session.php?expert_id=<?php echo $expert['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-calendar"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search); ?>&specialization=<?php echo urlencode($specialization); ?>&sort=<?php echo $sortBy; ?>" class="page-link">« First</a>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&specialization=<?php echo urlencode($specialization); ?>&sort=<?php echo $sortBy; ?>" class="page-link">‹ Prev</a>
                <?php endif; ?>

                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&specialization=<?php echo urlencode($specialization); ?>&sort=<?php echo $sortBy; ?>" 
                       class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&specialization=<?php echo urlencode($specialization); ?>&sort=<?php echo $sortBy; ?>" class="page-link">Next ›</a>
                    <a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&specialization=<?php echo urlencode($specialization); ?>&sort=<?php echo $sortBy; ?>" class="page-link">Last »</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="../assets/student-common.js"></script>
</body>
</html>
