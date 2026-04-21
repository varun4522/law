<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();
$userId = $student['id'];

// Get filters
$status = $_GET['status'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE cs.user_id = ?";
$params = [$userId];

if (!empty($status)) {
    $where .= " AND cs.status = ?";
    $params[] = $status;
}

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM consultation_sessions cs $where");
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Get sessions
$stmt = $pdo->prepare("
    SELECT cs.*, u.full_name as expert_name, ep.specialization, ep.rating as expert_rating
    FROM consultation_sessions cs
    JOIN users u ON cs.expert_id = u.id
    JOIN expert_profiles ep ON ep.user_id = u.id
    $where
    ORDER BY cs.session_date DESC
    LIMIT $offset, $perPage
");
$stmt->execute($params);
$sessions = $stmt->fetchAll();

// Get status summary
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count 
    FROM consultation_sessions 
    WHERE user_id = ? 
    GROUP BY status
");
$stmt->execute([$userId]);
$statusSummary = [];
foreach ($stmt->fetchAll() as $row) {
    $statusSummary[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - LawConnect</title>
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
                <a href="articles.php" class="nav-link">Articles</a>
                <a href="community.php" class="nav-link">Community</a>
                <a href="sessions.php" class="nav-link active">My Sessions</a>
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
        <aside class="sidebar">
            <h3>Sessions</h3>
            <ul class="sidebar-menu">
                <li><a href="sessions.php" class="<?php echo empty($status) ? 'active' : ''; ?>">All Sessions</a></li>
                <li><a href="?status=pending" class="<?php echo $status === 'pending' ? 'active' : ''; ?>">
                    Pending <span class="badge"><?php echo $statusSummary['pending'] ?? 0; ?></span>
                </a></li>
                <li><a href="?status=confirmed" class="<?php echo $status === 'confirmed' ? 'active' : ''; ?>">
                    Confirmed <span class="badge"><?php echo $statusSummary['confirmed'] ?? 0; ?></span>
                </a></li>
                <li><a href="?status=in_progress" class="<?php echo $status === 'in_progress' ? 'active' : ''; ?>">
                    In Progress <span class="badge"><?php echo $statusSummary['in_progress'] ?? 0; ?></span>
                </a></li>
                <li><a href="?status=completed" class="<?php echo $status === 'completed' ? 'active' : ''; ?>">
                    Completed <span class="badge"><?php echo $statusSummary['completed'] ?? 0; ?></span>
                </a></li>
                <li><a href="?status=cancelled" class="<?php echo $status === 'cancelled' ? 'active' : ''; ?>">
                    Cancelled <span class="badge"><?php echo $statusSummary['cancelled'] ?? 0; ?></span>
                </a></li>
            </ul>

            <a href="book-session.php" class="btn btn-primary btn-block" style="margin-top: 20px;">
                <i class="fas fa-plus"></i> Book New Session
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>My Consultation Sessions</h1>
                <p><?php echo $total; ?> total sessions</p>
            </div>

            <?php if (empty($sessions)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <h2>No Sessions Yet</h2>
                <p>Start by booking your first consultation with an expert</p>
                <a href="book-session.php" class="btn btn-primary">Book Session</a>
            </div>
            <?php else: ?>
            <div class="sessions-table-container">
                <table class="sessions-table">
                    <thead>
                        <tr>
                            <th>Expert</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                        <tr class="session-row">
                            <td>
                                <div class="expert-cell">
                                    <div class="expert-avatar"><?php echo strtoupper(substr($session['expert_name'], 0, 1)); ?></div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($session['expert_name']); ?></strong>
                                        <p><?php echo htmlspecialchars($session['specialization']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="date-time">
                                    <div><?php echo date('M d, Y', strtotime($session['session_date'])); ?></div>
                                    <div class="time"><?php echo date('h:i A', strtotime($session['session_date'])); ?></div>
                                </div>
                            </td>
                            <td><?php echo $session['duration']; ?> min</td>
                            <td><strong>₹<?php echo number_format($session['amount'] ?? 0, 0); ?></strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $session['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $session['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="session-detail.php?id=<?php echo $session['id']; ?>" class="btn btn-small" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($session['status'] === 'completed' && is_null($session['rating'])): ?>
                                    <button class="btn btn-small" onclick="openRatingModal(<?php echo $session['id']; ?>, '<?php echo htmlspecialchars($session['expert_name']); ?>')" title="Rate Session">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1<?php echo $status ? "&status=$status" : ''; ?>" class="page-link">« First</a>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $status ? "&status=$status" : ''; ?>" class="page-link">‹ Prev</a>
                <?php endif; ?>

                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <a href="?page=<?php echo $i; ?><?php echo $status ? "&status=$status" : ''; ?>" 
                       class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $status ? "&status=$status" : ''; ?>" class="page-link">Next ›</a>
                    <a href="?page=<?php echo $totalPages; ?><?php echo $status ? "&status=$status" : ''; ?>" class="page-link">Last »</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Rating Modal -->
    <div class="modal" id="ratingModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeRatingModal()">×</button>
            <h2>Rate Your Session</h2>
            <p id="ratingExpertName"></p>
            
            <div class="rating-input">
                <div class="stars" id="ratingStars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star" data-rating="<?php echo $i; ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="rating-text" id="ratingText">Click to rate</p>
            </div>

            <textarea id="reviewText" placeholder="Share your experience (optional)" class="form-input" rows="4"></textarea>

            <button onclick="submitRating()" class="btn btn-primary btn-block">Submit Rating</button>
        </div>
    </div>

    <script src="../assets/student-common.js"></script>
    <script>
        let selectedSessionId = null;
        let selectedRating = 0;

        function openRatingModal(sessionId, expertName) {
            selectedSessionId = sessionId;
            document.getElementById('ratingExpertName').textContent = `Session with ${expertName}`;
            document.getElementById('ratingModal').style.display = 'flex';
            setupRatingStars();
        }

        function closeRatingModal() {
            document.getElementById('ratingModal').style.display = 'none';
            selectedRating = 0;
            document.getElementById('reviewText').value = '';
        }

        function setupRatingStars() {
            const stars = document.querySelectorAll('#ratingStars i');
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = this.dataset.rating;
                    updateStars();
                });
            });
        }

        function updateStars() {
            const stars = document.querySelectorAll('#ratingStars i');
            stars.forEach(star => {
                if (star.dataset.rating <= selectedRating) {
                    star.classList.add('filled');
                } else {
                    star.classList.remove('filled');
                }
            });
        }

        function submitRating() {
            if (selectedRating === 0) {
                alert('Please select a rating');
                return;
            }

            const review = document.getElementById('reviewText').value;
            
            fetch('api/rate-session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id: selectedSessionId,
                    rating: selectedRating,
                    review: review
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your feedback!');
                    closeRatingModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>
