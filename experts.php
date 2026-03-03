<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experts Directory - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafafa;
            min-height: 100vh;
            color: #0a0a0a;
        }
        
        /* Navigation Bar */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e4;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #0a0a0a;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo i {
            font-size: 24px;
            color: #0a0a0a;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary {
            background: #0a0a0a;
            color: white;
            letter-spacing: 0.3px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            background: #222;
        }
        .btn-secondary {
            background: #f5f5f3;
            color: #0a0a0a;
        }
        .btn-secondary:hover {
            background: #eaeae6;
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }
        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .page-header p {
            font-size: 16px;
            color: #888;
        }
        
        /* Search & Filter */
        .search-filter {
            background: white;
            padding: 24px;
            border-radius: 4px;
            border: 1px solid #e8e8e4;
            margin-bottom: 32px;
        }
        .search-box {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-box input,
        .search-box select {
            flex: 1;
            min-width: 200px;
            padding: 12px 16px;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }
        .search-box input:focus,
        .search-box select:focus {
            outline: none;
            border-color: #0a0a0a;
        }
        
        /* Experts Grid */
        .experts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }
        
        .expert-card {
            background: white;
            border-radius: 4px;
            padding: 24px;
            border: 1px solid #e8e8e4;
            transition: all 0.3s;
            cursor: pointer;
        }
        .expert-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: #0a0a0a;
        }
        
        .expert-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .expert-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #0a0a0a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .expert-info h3 {
            color: #0a0a0a;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .expert-specialization {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .expert-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 16px 0;
            padding: 16px 0;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }
        .stat {
            text-align: center;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 2px;
        }
        .stat-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .expert-details {
            margin: 16px 0;
            font-size: 14px;
            color: #666;
        }
        .expert-details div {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        .expert-details i {
            width: 18px;
            color: #888;
        }
        
        .expert-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
        }
        .expert-rate {
            font-size: 20px;
            font-weight: 700;
            color: #0a0a0a;
        }
        .expert-rate span {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 4px;
            padding: 32px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .modal-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a;
            letter-spacing: -0.5px;
        }
        .close-btn {
            font-size: 28px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
            line-height: 1;
        }
        .close-btn:hover {
            color: #0a0a0a;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #0a0a0a;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #ddd;
            border-radius: 2px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0a0a0a;
        }
        
        @media (max-width: 768px) {
            .navbar-container {
                flex-wrap: wrap;
                height: auto;
                padding: 16px 20px;
                gap: 12px;
            }
            .nav-right {
                width: 100%;
            }
            .page-header h1 {
                font-size: 28px;
            }
            .experts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: #d4edda;
            color: #28a745;
        }

        .status-busy {
            background: #fff3cd;
            color: #ffc107;
        }

        .rating {
            color: #ffa500;
        }

        .loading {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .experts-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .nav-buttons {
                justify-content: center;
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #333;
        }

        .close-btn {
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="mainhome.php" class="logo">
                <i class="fas fa-balance-scale"></i>
                Law Connectors
            </a>
            <div class="nav-right">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="sessions.php" class="btn btn-secondary"><i class="fas fa-calendar"></i> Sessions</a>
                <a href="forum.php" class="btn btn-secondary"><i class="fas fa-comments"></i> Forum</a>
                <a href="wallet.php" class="btn btn-secondary"><i class="fas fa-wallet"></i> Wallet</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Find Legal Experts</h1>
            <p>Browse and connect with verified legal professionals across all practice areas</p>
        </div>

        <!-- Search & Filter -->
        <div class="search-filter">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name or expertise...">
                <select id="specializationFilter">
                    <option value="">All Specializations</option>
                    <option value="Family Law">Family Law</option>
                    <option value="Criminal Law">Criminal Law</option>
                    <option value="Corporate Law">Corporate Law</option>
                    <option value="IP Law">Intellectual Property Law</option>
                    <option value="Cyber Law">Cyber Law</option>
                    <option value="Tax Law">Tax Law</option>
                    <option value="Real Estate Law">Real Estate Law</option>
                </select>
                <button class="btn btn-primary" onclick="searchExperts()"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>

        <!-- Experts Grid -->
        <div id="expertsContainer" class="experts-grid">
            <p style="text-align: center; padding: 40px; color: #6b7280; grid-column: 1 / -1;">Loading experts...</p>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book Consultation</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="bookingForm">
                <input type="hidden" id="expertIdInput">
                <div class="form-group">
                    <label>Expert</label>
                    <input type="text" id="expertNameInput" readonly>
                </div>
                <div class="form-group">
                    <label>Session Date & Time</label>
                    <input type="datetime-local" id="sessionDate" required>
                </div>
                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <select id="duration" required>
                        <option value="30">30 minutes</option>
                        <option value="60" selected>60 minutes</option>
                        <option value="90">90 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Session Type</label>
                    <select id="sessionType" required>
                        <option value="video">Video Call</option>
                        <option value="audio">Audio Call</option>
                        <option value="chat">Chat</option>
                        <option value="in-person">In-Person</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea id="notes" rows="3" placeholder="Brief description of your legal issue..."></textarea>
                </div>
                <div class="form-group">
                    <label>Estimated Cost</label>
                    <input type="text" id="estimatedCost" readonly>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-check"></i> Confirm Booking</button>
            </form>
        </div>
    </div>

    <script>
        let experts = [];
        let selectedExpert = null;

        async function loadExperts() {
            try {
                const response = await fetch('lib/student/get_all_experts.php');
                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                experts = result.data || [];
                displayExperts(experts);
            } catch (error) {
                console.error('Error loading experts:', error);
                document.getElementById('expertsContainer').innerHTML = `
                    <div class="loading">
                        <p style="color: #dc3545;">Error loading experts. Please try again.</p>
                    </div>
                `;
            }
        }

        function displayExperts(expertsToDisplay) {
            const container = document.getElementById('expertsContainer');

            if (!expertsToDisplay || expertsToDisplay.length === 0) {
                container.innerHTML = `
                    <p style="text-align: center; padding: 40px; color: #6b7280; grid-column: 1 / -1;">No experts found matching your criteria.</p>
                `;
                return;
            }

            let html = '';

            expertsToDisplay.forEach(expert => {
                const initials = expert.full_name ? expert.full_name.split(' ').map(n => n[0]).join('').substring(0, 2) : 'EX';
                const statusClass = expert.availability_status === 'available' ? 'status-available' : 'status-busy';
                const statusText = expert.availability_status === 'available' ? 'Available' : 'Busy';

                html += `
                    <div class="expert-card" onclick="openBookingModal(${expert.id}, '${expert.full_name}', ${expert.hourly_rate})">
                        <div class="expert-header">
                            <div class="expert-avatar">${initials}</div>
                            <div class="expert-info">
                                <h3>${expert.full_name || 'Expert'}</h3>
                                <div class="expert-specialization">${expert.specialization || 'Legal Expert'}</div>
                            </div>
                        </div>
                        <div class="expert-stats">
                            <div class="stat">
                                <div class="stat-value">${expert.rating ? expert.rating.toFixed(1) : '0.0'}</div>
                                <div class="stat-label"><i class="fas fa-star" style="color: #fbbf24;"></i> Rating</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">${expert.total_reviews || 0}</div>
                                <div class="stat-label">Reviews</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">${expert.total_sessions || 0}</div>
                                <div class="stat-label">Sessions</div>
                            </div>
                        </div>
                        <div class="expert-details">
                            <div><i class="fas fa-briefcase"></i> ${expert.experience_years || 0} years experience</div>
                            <div><i class="fas fa-language"></i> ${expert.language || 'English'}</div>
                        </div>
                        <div class="expert-footer">
                            <div class="expert-rate">₹${expert.hourly_rate || 0}<span>/hour</span></div>
                            <button class="btn btn-primary" onclick="event.stopPropagation(); openBookingModal(${expert.id}, '${expert.full_name}', ${expert.hourly_rate})">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function searchExperts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const specialization = document.getElementById('specializationFilter').value;

            const filtered = experts.filter(expert => {
                const matchesSearch = !searchTerm || 
                    expert.full_name.toLowerCase().includes(searchTerm) ||
                    (expert.specialization && expert.specialization.toLowerCase().includes(searchTerm));
                
                const matchesSpecialization = !specialization || 
                    (expert.specialization && expert.specialization.includes(specialization));

                return matchesSearch && matchesSpecialization;
            });

            displayExperts(filtered);
        }

        function openBookingModal(expertId, expertName, hourlyRate) {
            selectedExpert = { id: expertId, name: expertName, hourlyRate: hourlyRate };
            document.getElementById('expertIdInput').value = expertId;
            document.getElementById('expertNameInput').value = expertName;
            document.getElementById('bookingModal').classList.add('active');
            calculateCost();
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        function calculateCost() {
            if (!selectedExpert) return;
            
            const duration = parseInt(document.getElementById('duration').value);
            const cost = (selectedExpert.hourlyRate / 60) * duration;
            document.getElementById('estimatedCost').value = `₹${cost.toFixed(2)}`;
        }

        document.getElementById('duration').addEventListener('change', calculateCost);

        document.getElementById('bookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                expert_id: document.getElementById('expertIdInput').value,
                session_date: document.getElementById('sessionDate').value,
                duration: document.getElementById('duration').value,
                session_type: document.getElementById('sessionType').value,
                notes: document.getElementById('notes').value
            };

            try {
                const response = await fetch('lib/student/book_session.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Session booked successfully!');
                    closeModal();
                    window.location.href = 'sessions.php';
                }
            } catch (error) {
                console.error('Error booking session:', error);
                alert('An error occurred. Please try again.');
            }
        });

        // Load experts on page load
        window.addEventListener('load', loadExperts);
    </script>
</body>
</html>
