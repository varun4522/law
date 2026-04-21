<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();

// Get filters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'latest';
$page = max(1, $_GET['page'] ?? 1);
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE is_public = 1 AND status = 'published'";
$params = [];

if (!empty($search)) {
    $where .= " AND (title LIKE ? OR description LIKE ? OR content LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if (!empty($category)) {
    $where .= " AND types = ?";
    $params[] = $category;
}

$orderBy = $sortBy === 'popular' ? 'views DESC' : 'created_at DESC';

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM data_records $where");
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Get articles
$stmt = $pdo->prepare("
    SELECT * FROM data_records 
    $where
    ORDER BY $orderBy
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories
$stmt = $pdo->prepare("
    SELECT DISTINCT types FROM data_records 
    WHERE is_public = 1 AND status = 'published'
    ORDER BY types
");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn Law - Articles & Knowledge Base</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/student-styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="logo"><i class="fas fa-gavel"></i> LawConnect</a>
            <div class="nav-center">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="experts.php" class="nav-link">Experts</a>
                <a href="articles.php" class="nav-link active">Articles</a>
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
        <!-- Sidebar -->
        <aside class="filter-sidebar">
            <h3>Knowledge Base</h3>
            <form method="GET" id="filterForm">
                <div class="filter-group">
                    <label>Search Articles</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search..." class="filter-input">
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select name="category" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort" class="filter-select">
                        <option value="latest" <?php echo $sortBy === 'latest' ? 'selected' : ''; ?>>Latest</option>
                        <option value="popular" <?php echo $sortBy === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Search</button>
                <a href="articles.php" class="btn btn-secondary btn-block">Clear</a>
            </form>

            <div class="sidebar-promo">
                <h4>💡 Quick Tips</h4>
                <p>Learn about common legal topics, procedures, and rights through our comprehensive knowledge base.</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="articles-list">
            <div class="page-header">
                <h1>Learn Law</h1>
                <p>Comprehensive guides and articles on various legal topics</p>
            </div>

            <?php if (empty($articles)): ?>
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h2>No Articles Found</h2>
                <p>Try a different search or browse all categories</p>
            </div>
            <?php else: ?>
            <div class="results-info">
                <p>Found <?php echo count($articles); ?> of <?php echo $total; ?> articles</p>
            </div>

            <div class="articles-grid-large">
                <?php foreach ($articles as $article): 
                    $readTime = ceil(str_word_count($article['content'] ?? '') / 200);
                ?>
                <article class="article-card-large">
                    <div class="article-thumbnail">
                        <div class="article-icon">
                            <i class="fas fa-<?php echo match($article['types']) {
                                'guide' => 'book',
                                'tutorial' => 'graduation-cap',
                                'case' => 'gavel',
                                'news' => 'newspaper',
                                default => 'file-alt'
                            }; ?>"></i>
                        </div>
                    </div>

                    <div class="article-content">
                        <div class="article-meta">
                            <span class="category"><?php echo htmlspecialchars($article['types']); ?></span>
                            <span class="date"><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                            <span class="read-time"><?php echo $readTime; ?> min read</span>
                        </div>

                        <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                        <p><?php echo htmlspecialchars(substr($article['description'], 0, 150)); ?>...</p>

                        <div class="article-footer">
                            <div class="article-stats">
                                <span><i class="fas fa-eye"></i> <?php echo $article['views'] ?? 0; ?> views</span>
                            </div>
                            <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-secondary btn-small">
                                Read Article →
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sortBy; ?>" class="page-link">« First</a>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sortBy; ?>" class="page-link">‹ Prev</a>
                <?php endif; ?>

                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sortBy; ?>" 
                       class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sortBy; ?>" class="page-link">Next ›</a>
                    <a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sortBy; ?>" class="page-link">Last »</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="../assets/student-common.js"></script>
</body>
</html>
