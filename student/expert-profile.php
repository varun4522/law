<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();
$expertId = $_GET['id'] ?? null;

if (!$expertId) {
    header('Location: experts.php');
    exit;
}

// Get expert details
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.email, ep.specialization, ep.bio, ep.hourly_rate, 
           ep.rating, ep.years_experience, COUNT(cs.id) as session_count
    FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    LEFT JOIN consultation_sessions cs ON cs.expert_id = u.id AND cs.status != 'cancelled'
    WHERE u.id = ? AND u.role = ? AND ep.verification_status = 'verified'
    GROUP BY u.id
");
$stmt->execute([$expertId, ROLE_EXPERT]);
$expert = $stmt->fetch();

if (!$expert) {
    header('Location: experts.php');
    exit;
}

// Get expert reviews
$stmt = $pdo->prepare("
    SELECT cs.rating, cs.review, cs.updated_at, u.full_name
    FROM consultation_sessions cs
    JOIN users u ON cs.user_id = u.id
    WHERE cs.expert_id = ? AND cs.rating IS NOT NULL
    ORDER BY cs.updated_at DESC
    LIMIT 10
");
$stmt->execute([$expertId]);
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($expert['full_name']); ?> - LawConnect</title>
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
                <a href="experts.php" class="nav-link active">Experts</a>
                <a href="articles.php" class="nav-link">Articles</a>
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

    <div class="student-container">
        <main class="main-content profile-detail-page">
            <!-- Back Button -->
            <a href="experts.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Experts</a>

            <!-- Expert Header -->
            <div class="profile-header-section">
                <div class="profile-header-card">
                    <div class="profile-avatar-xlarge">
                        <?php echo strtoupper(substr($expert['full_name'], 0, 1)); ?>
                    </div>
                    <div class="profile-header-content">
                        <h1><?php echo htmlspecialchars($expert['full_name']); ?></h1>
                        <p class="specialization"><?php echo htmlspecialchars($expert['specialization']); ?></p>
                        
                        <div class="expert-meta-list">
                            <span><i class="fas fa-star"></i> <?php echo number_format($expert['rating'] ?? 0, 1); ?> Rating</span>
                            <span><i class="fas fa-video"></i> <?php echo $expert['session_count']; ?> Sessions</span>
                            <span><i class="fas fa-graduation-cap"></i> <?php echo $expert['years_experience']; ?> Years Experience</span>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <div class="price-section">
                        <p class="price-label">Consultation Fee</p>
                        <p class="price-amount">₹<?php echo $expert['hourly_rate']; ?>/hour</p>
                    </div>
                    <a href="book-session.php?expert_id=<?php echo $expert['id']; ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar"></i> Book Consultation
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('about')">About</button>
                <button class="tab-btn" onclick="switchTab('reviews')">Reviews (<?php echo count($reviews); ?>)</button>
                <button class="tab-btn" onclick="switchTab('availability')">Availability</button>
            </div>

            <!-- About Tab -->
            <div class="tab-content active" id="about-tab">
                <div class="card">
                    <h3>Professional Bio</h3>
                    <p><?php echo nl2br(htmlspecialchars($expert['bio'] ?? 'Experienced legal professional')); ?></p>
                </div>

                <div class="card">
                    <h3>Expertise Areas</h3>
                    <div class="expertise-tags">
                        <span class="tag"><?php echo htmlspecialchars($expert['specialization']); ?></span>
                        <span class="tag">Consultation</span>
                        <span class="tag">Legal Advice</span>
                    </div>
                </div>

                <div class="card">
                    <h3>Qualifications</h3>
                    <ul>
                        <li><?php echo $expert['years_experience']; ?> years of legal practice</li>
                        <li>Verified expert on LawConnect</li>
                        <li>Avg. Rating: <?php echo number_format($expert['rating'] ?? 0, 1); ?>/5.0</li>
                    </ul>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-content" id="reviews-tab">
                <?php if (empty($reviews)): ?>
                <div class="empty-state">
                    <p>No reviews yet</p>
                </div>
                <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div>
                                <h4><?php echo htmlspecialchars($review['full_name']); ?></h4>
                                <span class="date"><?php echo date('M d, Y', strtotime($review['updated_at'])); ?></span>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i < $review['rating'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php if ($review['review']): ?>
                        <p class="review-text"><?php echo htmlspecialchars($review['review']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Availability Tab -->
            <div class="tab-content" id="availability-tab">
                <div class="card">
                    <h3>Available for Consultation</h3>
                    <p>This expert is currently available for consultations via video call, phone, or chat.</p>
                    <div class="availability-grid">
                        <div class="availability-item">
                            <i class="fas fa-video"></i>
                            <span>Video Call</span>
                        </div>
                        <div class="availability-item">
                            <i class="fas fa-phone"></i>
                            <span>Phone Call</span>
                        </div>
                        <div class="availability-item">
                            <i class="fas fa-comments"></i>
                            <span>Text Chat</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Booking Information</h3>
                    <ul>
                        <li>✓ Instant booking confirmation</li>
                        <li>✓ Flexible session duration</li>
                        <li>✓ Money-back guarantee if not satisfied</li>
                        <li>✓ Secure video conferencing</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/student-common.js"></script>
    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');

            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
