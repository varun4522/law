<?php require_once 'lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'Inter', sans-serif; background: #fafafa; min-height: 100vh;}
        
        /* Navigation Bar */
        .navbar {background: #fff; border-bottom: 1px solid #e8e8e4; padding: 0; position: sticky; top: 0; z-index: 100;}
        .navbar-container {max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; align-items: center; height: 70px;}
        .logo {font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0a0a0a; display: flex; align-items: center; gap: 10px; text-decoration: none;}
        .logo i {font-size: 24px; color: #0a0a0a;}
        .nav-buttons {display: flex; gap: 10px;}
        .btn {padding: 10px 18px; border: none; border-radius: 2px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.3px;}
        .btn-primary {background: #0a0a0a; color: white;}
        .btn-primary:hover {background: #222; transform: translateY(-2px);}
        .btn-secondary {background: #f5f5f3; color: #0a0a0a;}
        .btn-secondary:hover {background: #eaeae6;}
        
        .container {max-width: 1200px; margin: 0 auto; padding: 32px 24px;}
        .page-header {margin-bottom: 32px;}
        .page-header h1 {font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; color: #0a0a0a; margin-bottom: 8px; letter-spacing: -0.5px;}
        .page-header p {font-size: 16px; color: #888;}
        
        .content-box {background: white; padding: 32px; border-radius: 4px; border: 1px solid #e8e8e4; margin-bottom: 24px;}
        .session-card {background: #fafafa; padding: 24px; border-radius: 4px; margin-bottom: 16px; border-left: 3px solid #0a0a0a;}
        .session-header {display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;}
        .session-header h3 {color: #0a0a0a; font-size: 18px; font-weight: 600;}
        .session-details {display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;}
        .detail-item {display: flex; align-items: center; gap: 10px; color: #666; font-size: 14px;}
        .detail-item i {color: #888;}
        .badge {padding: 6px 12px; border-radius: 2px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
        .badge-warning {background: #f5f5f3; color: #666;}
        .badge-success {background: #f0f0f0; color: #0a0a0a;}
        .badge-danger {background: #f0f0f0; color: #666;}
        .badge-info {background: #f0f0f0; color: #0a0a0a;}
        
        @media (max-width: 768px) {
            .navbar-container {flex-wrap: wrap; height: auto; padding: 16px 20px; gap: 12px;}
            .nav-buttons {width: 100%;}
            .session-details {grid-template-columns: 1fr;}
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="mainhome.php" class="logo">
                <i class="fas fa-balance-scale"></i>
                Law Connectors
            </a>
            <div class="nav-buttons">
                <a href="mainhome.php" class="btn btn-secondary"><i class="fas fa-home"></i> Dashboard</a>
                <a href="experts.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book Session</a>
                <a href="wallet.php" class="btn btn-secondary"><i class="fas fa-wallet"></i> Wallet</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>My Consultation Sessions</h1>
            <p>View and manage all your legal consultation bookings</p>
        </div>

        <div class="content-box">
            <div id="sessionsContainer">
                <p style="text-align: center; padding: 40px; color: #888;">Loading sessions...</p>
            </div>
        </div>
    </div>

    <script>
        async function loadSessions() {
            try {
                const response = await fetch('lib/student/get_my_sessions.php');
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
                container.innerHTML = '<p style="text-align: center; color: #888;">No sessions booked yet. <a href="experts.php" style="color: #0a0a0a; font-weight: 600;">Book your first session</a></p>';
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
                                <h3>${expertName}</h3>
                                ${s.specialization ? `<p style="color: #888; font-size: 13px; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">${s.specialization}</p>` : ''}
                            </div>
                            <span class="badge ${statusBadge}">${s.status.toUpperCase()}</span>
                        </div>
                        <div class="session-details">
                            <div class="detail-item"><i class="fas fa-calendar"></i> ${date}</div>
                            <div class="detail-item"><i class="fas fa-clock"></i> ${s.duration} minutes</div>
                            <div class="detail-item"><i class="fas fa-video"></i> ${s.session_type}</div>
                            <div class="detail-item"><i class="fas fa-rupee-sign"></i> ₹${parseFloat(s.amount).toFixed(2)}</div>
                        </div>
                        ${s.notes ? `<p style="margin-top: 16px; padding: 12px; background: white; border-radius: 2px; color: #666; border-left: 2px solid #ddd;"><strong style="color: #0a0a0a;">Notes:</strong> ${s.notes}</p>` : ''}
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        window.addEventListener('load', loadSessions);
    </script>
</body>
</html>
