<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - Law Connectors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px;}
        .container {max-width: 1200px; margin: 0 auto;}
        .header {background: white; padding: 20px 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        .logo {font-size: 24px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 10px;}
        .nav-buttons {display: flex; gap: 10px; flex-wrap: wrap;}
        .btn {padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-primary {background: #667eea; color: white;}
        .btn-secondary {background: #f0f0f0; color: #333;}
        .content-box {background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 25px;}
        .session-card {background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px;}
        .session-header {display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;}
        .session-details {display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;}
        .detail-item {display: flex; align-items: center; gap: 10px; color: #666;}
        .badge {padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;}
        .badge-warning {background: #fff3cd; color: #ffc107;}
        .badge-success {background: #d4edda; color: #28a745;}
        .badge-danger {background: #f8d7da; color: #dc3545;}
        .badge-info {background: #d1ecf1; color: #17a2b8;}
        @media (max-width: 768px) {.session-details {grid-template-columns: 1fr;}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-calendar"></i> Law Connectors - My Sessions</div>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book New Session</a>
                <a href="wallet.php" class="btn btn-secondary"><i class="fas fa-wallet"></i> Wallet</a>
            </div>
        </div>

        <div class="content-box">
            <h2 style="margin-bottom: 20px; color: #333;">Your Consultation Sessions</h2>
            <div id="sessionsContainer">
                <p style="text-align: center; padding: 40px; color: #999;">Loading sessions...</p>
            </div>
        </div>
    </div>

    <script>
        async function loadSessions() {
            try {
                const response = await fetch('lib/get_my_sessions.php');
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
                container.innerHTML = '<p style="text-align: center; color: #999;">No sessions booked yet. <a href="experts.php">Book your first session</a></p>';
                return;
            }

            let html = '';
            sessions.forEach(s => {
                let statusBadge = 'badge-warning';
                if (s.status === 'completed') statusBadge = 'badge-success';
                else if (s.status === 'cancelled') statusBadge = 'badge-danger';
                else if (s.status === 'confirmed') statusBadge = 'badge-info';

                const date = new Date(s.session_date).toLocaleString();
                const expertName = s.expert_name || s.client_name || 'Unknown';

                html += `
                    <div class="session-card">
                        <div class="session-header">
                            <div>
                                <h3 style="color: #333; margin-bottom: 5px;">Session with ${expertName}</h3>
                                ${s.specialization ? `<p style="color: #667eea; font-size: 14px;">${s.specialization}</p>` : ''}
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
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        window.addEventListener('load', loadSessions);
    </script>
</body>
</html>
