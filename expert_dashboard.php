<?php require_once 'lib/db.php'; 
$user = requireAuth(); 
if ($user['role'] !== 'expert' && $user['role'] !== 'admin') {
    header('Location: mainhome.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Dashboard - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: #0a0a0a;
            min-height: 100vh;
            padding-bottom: 90px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }
        
        .header {
            background: #0a0a0a;
            padding: 24px 40px;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .logo {
            font-size: 26px;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo i {
            font-size: 28px;
        }
        
        .nav-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: #0a0a0a;
            color: #fff;
            border: 2px solid #0a0a0a;
        }
        
        .btn-primary:hover {
            background: #fff;
            color: #0a0a0a;
        }
        
        .btn-secondary {
            background: #fff;
            color: #0a0a0a;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #0a0a0a;
            color: #fff;
            border-color: #0a0a0a;
        }
        
        .btn-success {
            background: #0a0a0a;
            color: #fff;
            border: 2px solid #0a0a0a;
        }
        
        .btn-success:hover {
            background: #2ecc71;
            border-color: #2ecc71;
        }
        
        .btn-danger {
            background: #fff;
            color: #e74c3c;
            border: 2px solid #e74c3c;
        }
        
        .btn-danger:hover {
            background: #e74c3c;
            color: #fff;
        }
        
        .content-box {
            background: #fff;
            padding: 32px;
            margin-bottom: 32px;
            border: 1px solid #e8e8e4;
        }
        
        .content-box h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #0a0a0a;
            margin-bottom: 24px;
        }
        
        .session-card {
            background: #fafafa;
            padding: 24px;
            margin-bottom: 20px;
            border-left: 4px solid #0a0a0a;
            transition: all 0.3s ease;
        }
        
        .session-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .session-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 16px;
        }
        
        .session-header h3 {
            font-size: 19px;
            color: #0a0a0a;
            font-weight: 600;
        }
        
        .session-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 14px;
        }
        
        .detail-item i {
            color: #0a0a0a;
        }
        
        .badge {
            padding: 6px 14px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-warning {
            background: #fff;
            color: #f39c12;
            border: 1px solid #f39c12;
        }
        
        .badge-success {
            background: #fff;
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }
        
        .badge-danger {
            background: #fff;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        
        .badge-info {
            background: #0a0a0a;
            color: #fff;
            border: 1px solid #0a0a0a;
        }
        
        .session-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .profile-form {
            max-width: 600px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #0a0a0a;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0a0a0a;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .header {
                padding: 16px 20px;
                margin-bottom: 24px;
            }
            
            .logo {
                font-size: 20px;
            }
            
            .logo i {
                font-size: 22px;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: flex-start;
            }
            
            .btn {
                font-size: 13px;
                padding: 10px 16px;
            }
            
            .content-box {
                padding: 20px 16px;
                margin-bottom: 20px;
            }
            
            .content-box h2 {
                font-size: 22px;
            }
            
            .session-card {
                padding: 16px;
            }
            
            .session-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .session-header h3 {
                font-size: 16px;
            }
            
            .session-details {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .session-actions {
                flex-direction: column;
            }
            
            .session-actions .btn {
                width: 100%;
                justify-content: center;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-user-tie"></i> Expert Dashboard</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-secondary"><i class="fas fa-users"></i> View Experts</a>
                <button class="btn btn-primary" onclick="showTab('profile')"><i class="fas fa-user-edit"></i> Edit Profile</button>
            </div>
        </div>

        <!-- Session Requests -->
        <div id="sessionsTab" class="content-box">
            <h2 style="margin-bottom: 20px; color: #333;">Session Requests & Bookings</h2>
            <div id="sessionsContainer">
                <p style="text-align: center; padding: 40px; color: #999;">Loading sessions...</p>
            </div>
        </div>

        <!-- Profile Settings -->
        <div id="profileTab" class="content-box" style="display: none;">
            <h2 style="margin-bottom: 20px; color: #333;">Update Expert Profile</h2>
            <form id="profileForm" class="profile-form">
                <div class="form-group">
                    <label>Specialization</label>
                    <select id="specialization">
                        <option value="General Law">General Law</option>
                        <option value="Family Law">Family Law</option>
                        <option value="Criminal Law">Criminal Law</option>
                        <option value="Corporate Law">Corporate Law</option>
                        <option value="Property Law">Property Law</option>
                        <option value="Tax Law">Tax Law</option>
                        <option value="Cyber Law">Cyber Law</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Experience (Years)</label>
                    <input type="number" id="experience" min="0" required>
                </div>
                <div class="form-group">
                    <label>Hourly Rate (₹)</label>
                    <input type="number" id="hourlyRate" min="100" step="50" required>
                </div>
                <div class="form-group">
                    <label>Languages</label>
                    <input type="text" id="languages" placeholder="English, Hindi, etc.">
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea id="bio" rows="4" placeholder="Tell clients about your expertise and experience..."></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="isAvailable" checked>
                    <label for="isAvailable" style="margin: 0;">Available for consultations</label>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%;"><i class="fas fa-save"></i> Save Profile</button>
            </form>
        </div>
    </div>

    <script>
        let currentTab = 'sessions';

        function showTab(tab) {
            currentTab = tab;
            document.getElementById('sessionsTab').style.display = tab === 'sessions' ? 'block' : 'none';
            document.getElementById('profileTab').style.display = tab === 'profile' ? 'block' : 'none';
        }

        async function loadSessions() {
            try {
                const response = await fetch('lib/expert/expert_get_session_requests.php');
                const result = await response.json();
                
                if (result.error) throw new Error(result.error);
                
                displaySessions(result.data || []);
            } catch (error) {
                document.getElementById('sessionsContainer').innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading sessions</p>';
            }
        }

        function displaySessions(sessions) {
            const container = document.getElementById('sessionsContainer');
            
            if (sessions.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #999;">No session requests yet</p>';
                return;
            }

            let html = '';
            sessions.forEach(s => {
                let statusBadge = 'badge-warning';
                if (s.status === 'completed') statusBadge = 'badge-success';
                else if (s.status === 'cancelled') statusBadge = 'badge-danger';
                else if (s.status === 'confirmed') statusBadge = 'badge-info';

                const date = new Date(s.session_date).toLocaleString();

                html += `
                    <div class="session-card">
                        <div class="session-header">
                            <div>
                                <h3 style="color: #333; margin-bottom: 5px;">Session with ${s.client_name}</h3>
                                <p style="color: #999; font-size: 14px;">${s.client_email}</p>
                            </div>
                            <span class="badge ${statusBadge}">${s.status.toUpperCase()}</span>
                        </div>
                        <div class="session-details">
                            <div class="detail-item"><i class="fas fa-calendar"></i> ${date}</div>
                            <div class="detail-item"><i class="fas fa-clock"></i> ${s.duration} minutes</div>
                            <div class="detail-item"><i class="fas fa-video"></i> ${s.session_type}</div>
                            <div class="detail-item"><i class="fas fa-rupee-sign"></i> ₹${parseFloat(s.amount).toFixed(2)}</div>
                        </div>
                        ${s.notes ? `<p style="margin-top: 15px; padding: 10px; background: white; border-radius: 5px; color: #666;"><strong>Notes:</strong> ${s.notes}</p>` : ''}
                        ${s.status === 'pending' ? `
                            <div class="session-actions">
                                <button class="btn btn-success" onclick="updateSessionStatus(${s.id}, 'confirmed')"><i class="fas fa-check"></i> Accept</button>
                                <button class="btn btn-danger" onclick="updateSessionStatus(${s.id}, 'cancelled')"><i class="fas fa-times"></i> Decline</button>
                            </div>
                        ` : ''}
                        ${s.status === 'confirmed' ? `
                            <div class="session-actions">
                                <button class="btn btn-success" onclick="updateSessionStatus(${s.id}, 'completed')"><i class="fas fa-check-double"></i> Mark Completed</button>
                                <button class="btn btn-danger" onclick="updateSessionStatus(${s.id}, 'cancelled')"><i class="fas fa-times"></i> Cancel</button>
                            </div>
                        ` : ''}
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        async function updateSessionStatus(sessionId, status) {
            const confirmMsg = status === 'confirmed' ? 'accept' : (status === 'cancelled' ? 'cancel' : 'mark as completed');
            if (!confirm(`Are you sure you want to ${confirmMsg} this session?`)) return;

            try {
                const response = await fetch('lib/expert/expert_update_session_status.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({session_id: sessionId, status: status})
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Session updated successfully!');
                    loadSessions();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                specialization: document.getElementById('specialization').value,
                experience_years: parseInt(document.getElementById('experience').value),
                hourly_rate: parseFloat(document.getElementById('hourlyRate').value),
                languages: document.getElementById('languages').value,
                bio: document.getElementById('bio').value,
                is_available: document.getElementById('isAvailable').checked ? 1 : 0
            };

            try {
                const response = await fetch('lib/expert/expert_update_profile.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Profile updated successfully!');
                    showTab('sessions');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        window.addEventListener('load', loadSessions);
    </script>
</body>
</html>
