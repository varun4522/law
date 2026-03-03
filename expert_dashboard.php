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
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1400px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-primary {background: #667eea; color: white;}
        .btn-secondary {background: #f0f0f0; color: #333;}
        .btn-success {background: #28a745; color: white;}
        .btn-danger {background: #dc3545; color: white;}
        
        .content-box {background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 25px;}
        .session-card {background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #667eea;}
        .session-header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;}
        .session-details {display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;}
        .detail-item {display: flex; align-items: center; gap: 10px; color: #666;}
        .badge {padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;}
        .badge-warning {background: #fff3cd; color: #856404;}
        .badge-success {background: #d4edda; color: #155724;}
        .badge-danger {background: #f8d7da; color: #721c24;}
        .badge-info {background: #d1ecf1; color: #0c5460;}
        .session-actions {display: flex; gap: 10px; margin-top: 15px;}
        
        .profile-form {max-width: 600px;}
        .form-group {margin-bottom: 20px;}
        .form-group label {display: block; margin-bottom: 8px; font-weight: 600; color: #333;}
        .form-group input, .form-group select, .form-group textarea {width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;}
        .checkbox-group {display: flex; align-items: center; gap: 10px;}
        .checkbox-group input[type="checkbox"] {width: auto;}
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
