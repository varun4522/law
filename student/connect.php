<?php 
require_once __DIR__ . '/../lib/db.php'; 
requireAuth();

// Fetch experts from database
$pdo = getDBConnection();
$experts = [];
$dbError = null;

if ($pdo) {
    try {
        // Query to get all verified experts with their user information
        $query = "SELECT 
            ep.id,
            u.id as user_id,
            u.full_name as name,
            u.full_name as username,
            u.email,
            u.role,
            ep.specialization,
            ep.experience_years,
            ep.hourly_rate,
            ep.rating,
            ep.total_reviews,
            ep.total_sessions,
            ep.availability_status,
            ep.language,
            ep.probono_participation,
            u.bio,
            u.phone,
            u.profile_image
        FROM expert_profiles ep
        INNER JOIN users u ON ep.user_id = u.id
        WHERE ep.verification_status = 'verified' 
        AND u.role = 2
        AND u.status = 'active'
        ORDER BY ep.rating DESC, ep.total_sessions DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $dbExperts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format data for JavaScript
        if ($dbExperts && count($dbExperts) > 0) {
            foreach ($dbExperts as $expert) {
                $experts[] = [
                    'id' => (int)$expert['id'],
                    'name' => $expert['name'] ?: $expert['username'] ?: 'Legal Expert',
                    'spec' => $expert['specialization'] ?: 'General Law',
                    'exp' => (int)($expert['experience_years'] ?: 0),
                    'rating' => (float)($expert['rating'] ?: 0),
                    'reviews' => (int)($expert['total_reviews'] ?: 0),
                    'sessions' => (int)($expert['total_sessions'] ?: 0),
                    'fee' => (int)($expert['hourly_rate'] ?: 500),
                    'avail' => ($expert['availability_status'] === 'available') ? 'available' : 'busy',
                    'probono' => (bool)$expert['probono_participation'],
                    'bio' => $expert['bio'] ?: 'Expert in legal matters.',
                    'tags' => [$expert['specialization']] ?: ['Law']
                ];
            }
        }
    } catch(Exception $e) {
        $dbError = $e->getMessage();
        error_log("Error fetching experts: " . $dbError);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect with Experts - Law Connectors</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#fafafa;min-height:100vh;color:#0a0a0a;padding-bottom:90px;}
        .navbar{background:#fff;border-bottom:1px solid #e8e8e4;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .navbar-container{max-width:1400px;margin:0 auto;padding:0 24px;display:flex;justify-content:space-between;align-items:center;height:70px;}
        .logo{font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#0a0a0a;display:flex;align-items:center;gap:10px;text-decoration:none;}
        .logout-btn{padding:8px 16px;background:#fff;color:#666;border:1.5px solid #ddd;border-radius:2px;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;}
        .logout-btn:hover{background:#0a0a0a;color:white;border-color:#0a0a0a;}
        .nav-right{display:flex;align-items:center;gap:16px;}

        .page-container{max-width:1100px;margin:0 auto;padding:32px 24px;}
        .page-header{background:#0a0a0a;border-radius:4px;padding:40px;margin-bottom:28px;color:#fff;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,0.04);border-radius:50%;}
        .page-header h1{font-family:'Playfair Display',serif;font-size:30px;font-weight:700;margin-bottom:8px;position:relative;}
        .page-header p{font-size:15px;opacity:0.7;position:relative;}
        .page-header .header-icon{font-size:40px;margin-bottom:16px;display:block;position:relative;}

        /* Search + Filter bar */
        .toolbar{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px;align-items:center;}
        .search-box{flex:1;min-width:220px;position:relative;}
        .search-box i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;}
        .search-box input{width:100%;padding:11px 14px 11px 40px;border:1.5px solid #e8e8e4;border-radius:4px;font-size:14px;outline:none;font-family:'Inter',sans-serif;}
        .search-box input:focus{border-color:#0a0a0a;}
        .filter-select{padding:11px 14px;border:1.5px solid #e8e8e4;border-radius:4px;font-size:14px;font-family:'Inter',sans-serif;outline:none;background:#fff;cursor:pointer;}
        .filter-select:focus{border-color:#0a0a0a;}

        /* Expert grid */
        .experts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;}
        .expert-card{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:28px;transition:all 0.25s;position:relative;overflow:hidden;}
        .expert-card::before{content:'';position:absolute;left:0;top:0;width:4px;height:100%;background:#0a0a0a;transform:scaleY(0);transition:transform 0.3s;}
        .expert-card:hover{border-color:#0a0a0a;box-shadow:0 6px 20px rgba(0,0,0,0.08);transform:translateY(-3px);}
        .expert-card:hover::before{transform:scaleY(1);}
        .expert-top{display:flex;align-items:center;gap:16px;margin-bottom:16px;}
        .expert-avatar{width:60px;height:60px;border-radius:50%;background:#0a0a0a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0;}
        .expert-name{font-size:17px;font-weight:600;color:#0a0a0a;margin-bottom:3px;}
        .expert-spec{font-size:13px;color:#888;margin-bottom:4px;}
        .stars{color:#f59e0b;font-size:13px;}
        .star-count{font-size:12px;color:#aaa;margin-left:4px;}
        .tag-list{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;}
        .tag{padding:4px 10px;background:#f5f5f3;border-radius:12px;font-size:12px;color:#555;font-weight:500;}
        .expert-stats{display:flex;gap:16px;margin-bottom:20px;padding:12px;background:#fafafa;border-radius:4px;}
        .e-stat{text-align:center;flex:1;}
        .e-stat-val{font-size:17px;font-weight:700;color:#0a0a0a;display:block;}
        .e-stat-lbl{font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:0.4px;}
        .expert-fee{font-size:14px;color:#666;margin-bottom:16px;display:flex;align-items:center;gap:6px;}
        .expert-fee strong{color:#0a0a0a;}
        .avail-badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:3px 10px;border-radius:10px;font-weight:600;margin-bottom:16px;}
        .avail-available{background:#dcfce7;color:#166534;}
        .avail-busy{background:#fef3c7;color:#92400e;}
        .book-btn{width:100%;padding:12px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.2s;}
        .book-btn:hover{background:#333;}
        .probono{display:inline-block;background:#ede9fe;color:#5b21b6;font-size:11px;font-weight:600;padding:3px 8px;border-radius:10px;margin-left:8px;}

        /* Modal */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;}
        .modal-overlay.show{display:flex;}
        .modal{background:#fff;border-radius:4px;width:90%;max-width:520px;padding:36px;}
        .modal h2{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:20px;}
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;font-size:14px;font-weight:500;margin-bottom:6px;}
        .form-group input,.form-group textarea,.form-group select{width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:4px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.2s;}
        .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:#0a0a0a;}
        .modal-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:8px;}
        .btn-cancel{padding:10px 20px;background:#fff;border:1.5px solid #ddd;border-radius:4px;cursor:pointer;font-size:14px;}
        .btn-submit{padding:10px 24px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600;}
        .success-msg{background:#dcfce7;color:#166534;padding:14px 18px;border-radius:4px;font-size:14px;border-left:3px solid #22c55e;display:none;margin-bottom:16px;}

        /* Bottom Nav */
        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e8e8e4;padding:12px 0;z-index:1000;box-shadow:0 -4px 12px rgba(0,0,0,0.08);}
        .bottom-nav-container{max-width:600px;margin:0 auto;display:flex;justify-content:space-around;align-items:center;position:relative;padding:0 20px;}
        .nav-item{display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;color:#888;transition:all 0.2s;padding:8px 12px;border-radius:4px;min-width:70px;}
        .nav-item i{font-size:22px;}
        .nav-item span{font-size:11px;font-weight:500;}
        .nav-item:hover,.nav-item.active{color:#0a0a0a;}
        .nav-item.center-ai{position:relative;top:-20px;background:#0a0a0a;color:#fff;width:70px;height:70px;border-radius:50%;box-shadow:0 8px 20px rgba(0,0,0,0.2);padding:0;min-width:unset;border:4px solid #fff;}
        .nav-item.center-ai i{font-size:28px;}
        .nav-item.center-ai span{position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);font-size:10px;white-space:nowrap;color:#0a0a0a;font-weight:600;}
        @media(min-width:769px){.bottom-nav{display:none;}body{padding-bottom:0;}}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="mainhome.php" class="logo"><i class="fas fa-balance-scale"></i> Law Connectors</a>
            <div class="nav-right">
                <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </nav>

    <div class="page-container">
        <div class="page-header">
            <span class="header-icon">👔</span>
            <h1>Connect with Legal Experts</h1>
            <p>Browse verified legal professionals and book a one-on-one consultation.</p>
        </div>

        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by name or specialization..." oninput="filterExperts()">
            </div>
            <select class="filter-select" id="specFilter" onchange="filterExperts()">
                <option value="">All Specializations</option>
                <option value="Family Law">Family Law</option>
                <option value="Criminal Law">Criminal Law</option>
                <option value="Civil Law">Civil Law</option>
                <option value="Property Law">Property Law</option>
                <option value="Consumer Law">Consumer Law</option>
                <option value="Corporate Law">Corporate Law</option>
                <option value="Labour Law">Labour Law</option>
            </select>
            <select class="filter-select" id="availFilter" onchange="filterExperts()">
                <option value="">All Availability</option>
                <option value="available">Available Now</option>
                <option value="busy">Busy</option>
            </select>
        </div>

        <?php if ($dbError): ?>
            <div style="background:#fee;border:1px solid #faa;border-radius:4px;padding:16px;margin-bottom:20px;color:#c00;">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($experts)): ?>
            <div style="background:#f0f9ff;border:1px solid #7dd3fc;border-radius:4px;padding:32px;text-align:center;margin-bottom:20px;">
                <i class="fas fa-database" style="font-size:40px;color:#0284c7;margin-bottom:16px;display:block;"></i>
                <strong style="display:block;margin-bottom:8px;font-size:16px;color:#0c2340;">No Experts Available</strong>
                <p style="color:#666;margin-bottom:16px;font-size:14px;">To populate the database with expert profiles, run:</p>
                <code style="background:#fff;border:1px solid #cbd5e1;padding:12px;border-radius:4px;display:inline-block;font-family:monospace;">php lib/seed_experts.php</code>
            </div>
        <?php endif; ?>

        <div class="experts-grid" id="expertsGrid"></div>
    </div>

    <!-- Book Session Modal -->
    <div class="modal-overlay" id="bookModal">
        <div class="modal">
            <h2>Book a Session</h2>
            <div class="success-msg" id="bookSuccess">Session request sent! The expert will confirm shortly.</div>
            <div id="bookForm">
                <p style="font-size:14px;color:#666;margin-bottom:20px;">Booking session with <strong id="bookExpertName"></strong></p>
                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" id="bookDate">
                </div>
                <div class="form-group">
                    <label>Preferred Time</label>
                    <select id="bookTime">
                        <option>09:00 AM</option><option>10:00 AM</option><option>11:00 AM</option>
                        <option>12:00 PM</option><option>02:00 PM</option><option>03:00 PM</option>
                        <option>04:00 PM</option><option>05:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Describe your legal issue</label>
                    <textarea id="bookIssue" placeholder="Briefly describe why you need this consultation..." rows="3"></textarea>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeBookModal()">Cancel</button>
                    <button class="btn-submit" onclick="submitBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="mainhome.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="connect.php" class="nav-item active"><i class="fas fa-user-tie"></i><span>Connect</span></a>
            <a href="aitool.php" class="nav-item center-ai"><i class="fas fa-robot"></i><span>AI Tool</span></a>
            <a href="community.php" class="nav-item"><i class="fas fa-comments"></i><span>Community</span></a>
            <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i><span>Profile</span></a>
        </div>
    </nav>

    <script>
        // Experts data from database
        const experts = <?php echo json_encode($experts); ?>;

        let filtered = [...experts];

        function renderStars(r) {
            const full = Math.floor(r); const half = r % 1 >= 0.5 ? 1 : 0;
            return '<i class="fas fa-star"></i>'.repeat(full) + (half ? '<i class="fas fa-star-half-alt"></i>' : '');
        }

        function renderExperts(list) {
            const grid = document.getElementById('expertsGrid');
            if (!list.length) { grid.innerHTML = '<p style="color:#888;text-align:center;padding:40px;">No experts found matching your filters.</p>'; return; }
            grid.innerHTML = list.map(e => `
                <div class="expert-card">
                    <div class="expert-top">
                        <div class="expert-avatar">${e.name.split(' ').pop()[0]}</div>
                        <div>
                            <div class="expert-name">${e.name}</div>
                            <div class="expert-spec">${e.spec} · ${e.exp} yrs exp</div>
                            <div class="stars">${renderStars(e.rating)}<span class="star-count">${e.rating} (${e.reviews})</span></div>
                        </div>
                    </div>
                    <span class="avail-badge avail-${e.avail}">
                        <i class="fas fa-circle" style="font-size:8px;"></i> ${e.avail === 'available' ? 'Available Now' : 'Currently Busy'}
                    </span>
                    ${e.probono ? '<span class="probono">Pro Bono Available</span>' : ''}
                    <div class="tag-list">${e.tags.map(t=>`<span class="tag">${t}</span>`).join('')}</div>
                    <div class="expert-stats">
                        <div class="e-stat"><span class="e-stat-val">${e.sessions}+</span><span class="e-stat-lbl">Sessions</span></div>
                        <div class="e-stat"><span class="e-stat-val">${e.reviews}</span><span class="e-stat-lbl">Reviews</span></div>
                        <div class="e-stat"><span class="e-stat-val">${e.exp}yr</span><span class="e-stat-lbl">Experience</span></div>
                    </div>
                    <div class="expert-fee"><i class="fas fa-rupee-sign"></i> <strong>₹${e.fee}</strong> / session (60 min)</div>
                    <button class="book-btn" onclick="openBookModal('${e.name}')">
                        <i class="fas fa-calendar-plus"></i> Book Session
                    </button>
                </div>`).join('');
        }

        function filterExperts() {
            const q   = document.getElementById('searchInput').value.toLowerCase();
            const sp  = document.getElementById('specFilter').value;
            const av  = document.getElementById('availFilter').value;
            filtered = experts.filter(e =>
                (!q  || e.name.toLowerCase().includes(q) || e.spec.toLowerCase().includes(q) || e.tags.some(t=>t.toLowerCase().includes(q))) &&
                (!sp || e.spec === sp) &&
                (!av || e.avail === av)
            );
            renderExperts(filtered);
        }

        let currentExpert = '';
        function openBookModal(name) {
            currentExpert = name;
            document.getElementById('bookExpertName').textContent = name;
            document.getElementById('bookSuccess').style.display = 'none';
            document.getElementById('bookForm').style.display = 'block';
            // Set min date to today
            document.getElementById('bookDate').min = new Date().toISOString().split('T')[0];
            document.getElementById('bookModal').classList.add('show');
        }

        function closeBookModal() { document.getElementById('bookModal').classList.remove('show'); }

        function submitBooking() {
            const date  = document.getElementById('bookDate').value;
            const time  = document.getElementById('bookTime').value;
            const issue = document.getElementById('bookIssue').value.trim();
            if (!date || !issue) { alert('Please fill in the date and describe your issue.'); return; }
            
            // Find expert ID by name
            const expert = experts.find(e => e.name === currentExpert);
            if (!expert) { alert('Expert not found.'); return; }
            
            // Send booking to server
            fetch('../lib/book_session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    expert_id: expert.id,
                    session_date: date + ' ' + time,
                    duration: 60,
                    notes: issue
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('bookSuccess').style.display = 'block';
                    document.getElementById('bookForm').querySelector('.modal-actions').style.display = 'none';
                    setTimeout(closeBookModal, 2500);
                } else {
                    alert('Error: ' + (data.message || 'Booking failed'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Booking error. Please try again.');
            });
        }

        async function logout() {
            try { await fetch('../lib/logout.php'); } catch(e){}
            window.location.href = '../index.php';
        }

        renderExperts(experts);
    </script>
</body>
</html>
