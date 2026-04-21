<?php
require_once __DIR__ . '/../lib/db.php';
$student = requireAuth();
if ($student['role'] != ROLE_STUDENT) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDBConnection();
$expertId = $_GET['expert_id'] ?? null;
$expert = null;

if ($expertId) {
    $stmt = $pdo->prepare("
        SELECT u.id, u.full_name, ep.specialization, ep.hourly_rate, ep.bio, ep.rating
        FROM users u
        JOIN expert_profiles ep ON ep.user_id = u.id
        WHERE u.id = ? AND u.role = ? AND ep.verification_status = 'verified'
    ");
    $stmt->execute([$expertId, ROLE_EXPERT]);
    $expert = $stmt->fetch();
}

// Get all verified experts
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, ep.specialization, ep.hourly_rate, ep.rating
    FROM users u
    JOIN expert_profiles ep ON ep.user_id = u.id
    WHERE u.role = ? AND ep.verification_status = 'verified' AND u.status = 'active'
    ORDER BY ep.rating DESC
");
$stmt->execute([ROLE_EXPERT]);
$allExperts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Expert Consultation - LawConnect</title>
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
        <main class="main-content booking-page">
            <div class="page-header">
                <h1>Book Expert Consultation</h1>
                <p>Schedule a session with a legal expert</p>
            </div>

            <div class="booking-container">
                <div class="booking-form">
                    <h2>Booking Details</h2>
                    <form id="bookingForm">
                        <!-- Expert Selection -->
                        <div class="form-group">
                            <label for="expertSelect">Select Expert *</label>
                            <select id="expertSelect" required class="form-select">
                                <option value="">Choose an expert...</option>
                                <?php foreach ($allExperts as $exp): ?>
                                <option value="<?php echo $exp['id']; ?>" 
                                        data-rate="<?php echo $exp['hourly_rate']; ?>"
                                        <?php echo $expertId == $exp['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($exp['full_name']); ?> - 
                                    <?php echo htmlspecialchars($exp['specialization']); ?> 
                                    (₹<?php echo $exp['hourly_rate']; ?>/hr)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Session Date -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sessionDate">Session Date *</label>
                                <input type="date" id="sessionDate" required class="form-input" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>

                            <div class="form-group">
                                <label for="sessionTime">Time *</label>
                                <input type="time" id="sessionTime" required class="form-input">
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label for="duration">Session Duration (minutes) *</label>
                            <select id="duration" required class="form-select" onchange="updatePrice()">
                                <option value="">Select duration...</option>
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                            </select>
                        </div>

                        <!-- Topic -->
                        <div class="form-group">
                            <label for="topic">Topic of Discussion *</label>
                            <textarea id="topic" required class="form-input" rows="4" 
                                     placeholder="Describe your legal issue or question..."></textarea>
                        </div>

                        <!-- Preferences -->
                        <div class="form-group">
                            <label for="preferences">Special Requests (optional)</label>
                            <textarea id="preferences" class="form-input" rows="3" 
                                     placeholder="Any documents or specific requirements..."></textarea>
                        </div>

                        <!-- Communication Type -->
                        <div class="form-group">
                            <label>Communication Type *</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="communicationType" value="video" checked>
                                    <span><i class="fas fa-video"></i> Video Call</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="communicationType" value="phone">
                                    <span><i class="fas fa-phone"></i> Phone Call</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="communicationType" value="chat">
                                    <span><i class="fas fa-comments"></i> Text Chat</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-calendar"></i> Proceed to Payment
                        </button>
                    </form>
                </div>

                <!-- Price Summary -->
                <div class="price-summary">
                    <h2>Price Summary</h2>
                    
                    <div class="expert-info" id="selectedExpertInfo" style="display: none;">
                        <div class="expert-card-mini">
                            <div class="expert-avatar-mini" id="summaryExpertAvatar">A</div>
                            <div>
                                <h4 id="summaryExpertName">Select an expert</h4>
                                <p id="summaryExpertSpecialization">Specialization</p>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-breakdown">
                        <div class="price-row">
                            <span>Rate</span>
                            <span id="priceRate">₹0/hr</span>
                        </div>
                        <div class="price-row">
                            <span>Duration</span>
                            <span id="priceDuration">-</span>
                        </div>
                        <div class="price-row">
                            <span>Subtotal</span>
                            <span id="priceSubtotal">₹0</span>
                        </div>
                        <div class="price-row">
                            <span>Platform Fee (5%)</span>
                            <span id="priceFee">₹0</span>
                        </div>
                        <div class="price-divider"></div>
                        <div class="price-row total">
                            <span>Total</span>
                            <span id="priceTotal">₹0</span>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <h4>Payment Method</h4>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="card" checked>
                            <span><i class="fas fa-credit-card"></i> Credit/Debit Card</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="upi">
                            <span><i class="fas fa-mobile-alt"></i> UPI</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="wallet">
                            <span><i class="fas fa-wallet"></i> Wallet (₹<?php echo $student['wallet_balance'] ?? 0; ?>)</span>
                        </label>
                    </div>

                    <div class="booking-info">
                        <h4>Booking Info</h4>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Instant confirmation</li>
                            <li><i class="fas fa-check-circle"></i> Money-back guarantee</li>
                            <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/student-common.js"></script>
    <script>
        const expertSelect = document.getElementById('expertSelect');
        const durationSelect = document.getElementById('duration');

        expertSelect.addEventListener('change', updatePrice);

        function updatePrice() {
            const expertId = expertSelect.value;
            const duration = durationSelect.value;

            if (!expertId || !duration) {
                document.getElementById('selectedExpertInfo').style.display = 'none';
                document.getElementById('priceRate').textContent = '₹0/hr';
                document.getElementById('priceDuration').textContent = '-';
                document.getElementById('priceSubtotal').textContent = '₹0';
                document.getElementById('priceFee').textContent = '₹0';
                document.getElementById('priceTotal').textContent = '₹0';
                return;
            }

            // Get selected expert data
            const selectedOption = expertSelect.options[expertSelect.selectedIndex];
            const rate = parseFloat(selectedOption.dataset.rate);
            const expertName = selectedOption.text.split(' - ')[0];
            const specialization = selectedOption.text.split(' - ')[1]?.split('(')[0]?.trim() || 'Legal Expert';

            // Update expert info
            document.getElementById('summaryExpertAvatar').textContent = expertName.charAt(0).toUpperCase();
            document.getElementById('summaryExpertName').textContent = expertName;
            document.getElementById('summaryExpertSpecialization').textContent = specialization;
            document.getElementById('selectedExpertInfo').style.display = 'block';

            // Calculate pricing
            const durationHours = duration / 60;
            const subtotal = Math.round(rate * durationHours);
            const fee = Math.round(subtotal * 0.05);
            const total = subtotal + fee;

            // Update display
            document.getElementById('priceRate').textContent = '₹' + rate + '/hr';
            document.getElementById('priceDuration').textContent = duration + ' minutes';
            document.getElementById('priceSubtotal').textContent = '₹' + subtotal;
            document.getElementById('priceFee').textContent = '₹' + fee;
            document.getElementById('priceTotal').textContent = '₹' + total;
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                expert_id: document.getElementById('expertSelect').value,
                session_date: document.getElementById('sessionDate').value,
                session_time: document.getElementById('sessionTime').value,
                duration: document.getElementById('duration').value,
                topic: document.getElementById('topic').value,
                preferences: document.getElementById('preferences').value,
                communication_type: document.querySelector('input[name="communicationType"]:checked').value,
                payment_method: document.querySelector('input[name="payment"]:checked').value
            };

            // Validate
            if (!formData.expert_id || !formData.session_date || !formData.session_time || !formData.duration || !formData.topic) {
                alert('Please fill in all required fields');
                return;
            }

            // Submit
            fetch('api/book-session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Session booked successfully!');
                    window.location.href = 'sessions.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(e => alert('Error booking session: ' + e.message));
        });

        // Set minimum date to tomorrow
        const today = new Date();
        today.setDate(today.getDate() + 1);
        document.getElementById('sessionDate').min = today.toISOString().split('T')[0];
    </script>
</body>
</html>
