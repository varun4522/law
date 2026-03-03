<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experts Directory - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .search-filter {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .search-box {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box input, .search-box select {
            flex: 1;
            min-width: 200px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-box input:focus, .search-box select:focus {
            outline: none;
            border-color: #667eea;
        }

        .experts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .expert-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }

        .expert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .expert-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .expert-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .expert-info h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .expert-specialization {
            color: #667eea;
            font-size: 13px;
            font-weight: 600;
        }

        .expert-stats {
            display: flex;
            gap: 15px;
            margin: 15px 0;
            padding: 15px 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat {
            flex: 1;
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .expert-details {
            margin: 15px 0;
            font-size: 14px;
            color: #666;
        }

        .expert-details div {
            margin: 8px 0;
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
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-scale-balanced"></i>
                Law Connectors - Experts Directory
            </div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="sessions.php" class="btn btn-secondary"><i class="fas fa-calendar"></i> My Sessions</a>
                <a href="forum.php" class="btn btn-secondary"><i class="fas fa-comments"></i> Forum</a>
                <a href="wallet.php" class="btn btn-secondary"><i class="fas fa-wallet"></i> Wallet</a>
            </div>
        </div>

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

        <div id="expertsContainer">
            <div class="loading">
                <div class="spinner"></div>
                <p>Loading experts...</p>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book Consultation</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
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
                    <div class="loading">
                        <p>No experts found.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="experts-grid">';

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
                                <div class="stat-value">${expert.rating ? expert.rating.toFixed(1) : '0.0'} <i class="fas fa-star rating"></i></div>
                                <div class="stat-label">Rating</div>
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
                            <div><i class="fas fa-dollar-sign"></i> ₹${expert.hourly_rate || 0}/hour</div>
                            <div><span class="status-badge ${statusClass}">${statusText}</span></div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
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
