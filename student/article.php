<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();
$articleId = $_GET['id'] ?? null;

if (!$articleId) {
    header('Location: articles.php');
    exit;
}

// Get article
$stmt = $pdo->prepare("
    SELECT * FROM data_records 
    WHERE id = ? AND is_public = 1 AND status = 'published'
");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: articles.php');
    exit;
}

// Increment views
$stmt = $pdo->prepare("UPDATE data_records SET views = views + 1 WHERE id = ?");
$stmt->execute([$articleId]);

// Get related articles
$stmt = $pdo->prepare("
    SELECT id, title, description, types, created_at FROM data_records 
    WHERE types = ? AND id != ? AND is_public = 1 AND status = 'published'
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->execute([$article['types'], $articleId]);
$relatedArticles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - LawConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/student-styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="logo"><i class="fas fa-gavel"></i> LawConnect</a>
            <div class="nav-center">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="experts.php" class="nav-link">Experts</a>
                <a href="articles.php" class="nav-link active">Articles</a>
                <a href="community.php" class="nav-link">Community</a>
                <a href="sessions.php" class="nav-link">My Sessions</a>
            </div>
            <div class="nav-right">
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

    <div class="article-page">
        <a href="articles.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Articles</a>

        <article class="article-detail">
            <!-- Article Header -->
            <header class="article-header">
                <div class="article-meta">
                    <span class="article-category"><?php echo htmlspecialchars($article['types']); ?></span>
                    <span class="article-date"><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                    <span class="article-views"><i class="fas fa-eye"></i> <?php echo $article['views']; ?> views</span>
                </div>
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="article-excerpt"><?php echo htmlspecialchars($article['description']); ?></p>
            </header>

            <!-- Article Content -->
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>

            <!-- Article Footer -->
            <footer class="article-footer">
                <div class="share-buttons">
                    <h4>Share This Article</h4>
                    <a href="#" class="share-btn facebook" title="Share on Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="share-btn twitter" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="share-btn linkedin" title="Share on LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="share-btn whatsapp" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </footer>
        </article>

        <!-- Sidebar -->
        <aside class="article-sidebar">
            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
            <div class="sidebar-widget">
                <h3>Related Articles</h3>
                <ul class="related-list">
                    <?php foreach ($relatedArticles as $rel): ?>
                    <li>
                        <a href="article.php?id=<?php echo $rel['id']; ?>">
                            <?php echo htmlspecialchars(substr($rel['title'], 0, 50)); ?>...
                        </a>
                        <p class="related-date"><?php echo date('M d', strtotime($rel['created_at'])); ?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- CTA Widget -->
            <div class="sidebar-widget cta-widget">
                <h3>Need Legal Help?</h3>
                <p>Consult with our verified legal experts</p>
                <a href="experts.php" class="btn btn-primary btn-block">
                    <i class="fas fa-users"></i> Find Expert
                </a>
            </div>
        </aside>
    </div>

    <script src="../assets/student-common.js"></script>
    <script>
        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = window.location.href;
                const title = document.querySelector('.article-detail h1').textContent;
                
                const platform = this.classList.contains('facebook') ? 'facebook' :
                                this.classList.contains('twitter') ? 'twitter' :
                                this.classList.contains('linkedin') ? 'linkedin' :
                                this.classList.contains('whatsapp') ? 'whatsapp' : '';

                const shareUrls = {
                    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
                    twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
                    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
                    whatsapp: `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`
                };

                if (shareUrls[platform]) {
                    window.open(shareUrls[platform], '_blank');
                }
            });
        });
    </script>
</body>
</html>
