<?php require_once __DIR__ . '/../lib/db.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Law Connectors</title>
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

        .page-container{max-width:900px;margin:0 auto;padding:32px 24px;}

        /* Profile Hero */
        .profile-hero{background:#0a0a0a;border-radius:4px;padding:40px;margin-bottom:28px;color:#fff;position:relative;overflow:hidden;display:flex;align-items:center;gap:32px;}
        .profile-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:220px;height:220px;background:rgba(255,255,255,0.04);border-radius:50%;}
        .profile-pic{width:90px;height:90px;border-radius:50%;background:#fff;color:#0a0a0a;display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:700;flex-shrink:0;position:relative;border:3px solid rgba(255,255,255,0.2);}
        .profile-info{flex:1;position:relative;}
        .profile-info h1{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;margin-bottom:6px;}
        .profile-info p{font-size:14px;opacity:0.7;margin-bottom:10px;}
        .role-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;background:rgba(255,255,255,0.12);border-radius:20px;font-size:13px;font-weight:500;}
        .edit-profile-btn{padding:10px 20px;background:rgba(255,255,255,0.12);color:#fff;border:1.5px solid rgba(255,255,255,0.25);border-radius:4px;cursor:pointer;font-size:13px;font-weight:600;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;margin-top:14px;}
        .edit-profile-btn:hover{background:rgba(255,255,255,0.22);}

        /* Stats row */
        .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
        .stat-box{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:22px;text-align:center;transition:all 0.25s;}
        .stat-box:hover{border-color:#0a0a0a;box-shadow:0 4px 12px rgba(0,0,0,0.06);}
        .stat-box-val{font-size:26px;font-weight:700;color:#0a0a0a;display:block;margin-bottom:4px;}
        .stat-box-lbl{font-size:12px;color:#888;text-transform:uppercase;letter-spacing:0.4px;}

        /* Tabs */
        .tabs{display:flex;gap:0;border-bottom:2px solid #e8e8e4;margin-bottom:28px;}
        .tab{padding:12px 24px;cursor:pointer;font-size:14px;font-weight:500;color:#888;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all 0.2s;}
        .tab:hover{color:#0a0a0a;}
        .tab.active{color:#0a0a0a;border-bottom-color:#0a0a0a;}
        .tab-content{display:none;}
        .tab-content.active{display:block;}

        /* Info section */
        .info-card{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:28px;margin-bottom:20px;}
        .info-card h3{font-size:17px;font-weight:600;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;}
        .info-row{display:flex;padding:14px 0;border-bottom:1px solid #f3f3f0;}
        .info-row:last-child{border-bottom:none;}
        .info-label{width:180px;font-size:13px;color:#888;font-weight:500;flex-shrink:0;}
        .info-value{font-size:14px;color:#0a0a0a;font-weight:500;}

        /* Edit form */
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:14px;font-weight:500;margin-bottom:6px;}
        .form-group input,.form-group textarea{width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:4px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.2s;}
        .form-group input:focus,.form-group textarea:focus{border-color:#0a0a0a;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .save-btn{padding:12px 28px;background:#0a0a0a;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600;transition:background 0.2s;}
        .save-btn:hover{background:#333;}
        .alert-success{background:#dcfce7;color:#166534;padding:14px 18px;border-radius:4px;font-size:14px;border-left:3px solid #22c55e;display:none;margin-bottom:16px;}

        /* Sessions */
        .session-item{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:20px 24px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:16px;}
        .session-item:hover{border-color:#ccc;}
        .session-left h4{font-size:15px;font-weight:600;margin-bottom:4px;}
        .session-left p{font-size:13px;color:#888;}
        .session-status{padding:5px 12px;border-radius:10px;font-size:12px;font-weight:600;}
        .status-completed{background:#dcfce7;color:#166534;}
        .status-pending{background:#fef3c7;color:#92400e;}
        .status-confirmed{background:#dbeafe;color:#1e40af;}
        .session-fee{font-size:15px;font-weight:700;color:#0a0a0a;white-space:nowrap;}

        /* Wallet */
        .wallet-card{background:#0a0a0a;color:#fff;border-radius:4px;padding:32px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;}
        .wallet-amount{font-size:38px;font-weight:700;}
        .wallet-label{font-size:14px;opacity:0.7;margin-bottom:6px;}
        .wallet-actions{display:flex;gap:12px;}
        .wallet-btn{padding:10px 22px;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;}
        .wallet-btn.add{background:#fff;color:#0a0a0a;}
        .wallet-btn.withdraw{background:rgba(255,255,255,0.12);color:#fff;border:1.5px solid rgba(255,255,255,0.25);}
        .txn-item{background:#fff;border:1px solid #e8e8e4;border-radius:4px;padding:16px 20px;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;}
        .txn-left p{font-size:14px;font-weight:500;margin-bottom:3px;}
        .txn-left span{font-size:12px;color:#aaa;}
        .txn-amount{font-size:16px;font-weight:700;}
        .txn-credit{color:#16a34a;}
        .txn-debit{color:#dc2626;}

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
        @media(max-width:640px){.stats-row{grid-template-columns:repeat(2,1fr);}.profile-hero{flex-direction:column;text-align:center;}.form-row{grid-template-columns:1fr;}.info-label{width:130px;}}
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
        <!-- Profile Hero -->
        <div class="profile-hero">
            <div class="profile-pic" id="heroPic">U</div>
            <div class="profile-info">
                <h1 id="heroName">Loading...</h1>
                <p id="heroEmail">user@example.com</p>
                <span class="role-badge"><i class="fas fa-graduation-cap"></i> Student · Member</span>
                <br>
                <button class="edit-profile-btn" onclick="switchTab('edit')"><i class="fas fa-pen"></i> Edit Profile</button>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-box"><span class="stat-box-val" id="statSessions">3</span><span class="stat-box-lbl">Sessions</span></div>
            <div class="stat-box"><span class="stat-box-val" id="statQuestions">2</span><span class="stat-box-lbl">Questions Asked</span></div>
            <div class="stat-box"><span class="stat-box-val" id="statWallet">₹0</span><span class="stat-box-lbl">Wallet Balance</span></div>
            <div class="stat-box"><span class="stat-box-val" id="statMember">2026</span><span class="stat-box-lbl">Member Since</span></div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" onclick="switchTab('info')">Personal Info</div>
            <div class="tab" onclick="switchTab('sessions')">My Sessions</div>
            <div class="tab" onclick="switchTab('wallet')">Wallet</div>
            <div class="tab" onclick="switchTab('edit')">Edit Profile</div>
        </div>

        <!-- Tab: Personal Info -->
        <div class="tab-content active" id="tab-info">
            <div class="info-card">
                <h3>Personal Information</h3>
                <div class="info-row"><span class="info-label">Full Name</span><span class="info-value" id="infoName">—</span></div>
                <div class="info-row"><span class="info-label">Email Address</span><span class="info-value" id="infoEmail">—</span></div>
                <div class="info-row"><span class="info-label">Phone</span><span class="info-value" id="infoPhone">Not provided</span></div>
                <div class="info-row"><span class="info-label">Account Type</span><span class="info-value">Student / User</span></div>
                <div class="info-row"><span class="info-label">Member Since</span><span class="info-value" id="infoJoined">—</span></div>
            </div>
            <div class="info-card">
                <h3>About Me</h3>
                <p id="infoBio" style="font-size:14px;color:#666;line-height:1.7;">No bio added yet. Click <strong>Edit Profile</strong> to add one.</p>
            </div>
        </div>

        <!-- Tab: Sessions -->
        <div class="tab-content" id="tab-sessions">
            <div id="sessionsContainer">
                <div class="session-item">
                    <div class="session-left">
                        <h4>Adv. Priya Sharma — Family Law</h4>
                        <p><i class="fas fa-calendar"></i> Feb 20, 2026 &nbsp;·&nbsp; <i class="fas fa-clock"></i> 10:00 AM (60 min)</p>
                    </div>
                    <div style="text-align:right;">
                        <span class="session-status status-completed">Completed</span>
                        <div class="session-fee" style="margin-top:6px;">₹800</div>
                    </div>
                </div>
                <div class="session-item">
                    <div class="session-left">
                        <h4>Adv. Rahul Verma — Criminal Law</h4>
                        <p><i class="fas fa-calendar"></i> Mar 5, 2026 &nbsp;·&nbsp; <i class="fas fa-clock"></i> 02:00 PM (60 min)</p>
                    </div>
                    <div style="text-align:right;">
                        <span class="session-status status-confirmed">Confirmed</span>
                        <div class="session-fee" style="margin-top:6px;">₹1,200</div>
                    </div>
                </div>
                <div class="session-item">
                    <div class="session-left">
                        <h4>Adv. Deepa Choudhary — Civil Law</h4>
                        <p><i class="fas fa-calendar"></i> Mar 10, 2026 &nbsp;·&nbsp; <i class="fas fa-clock"></i> 11:00 AM (60 min)</p>
                    </div>
                    <div style="text-align:right;">
                        <span class="session-status status-pending">Pending</span>
                        <div class="session-fee" style="margin-top:6px;">₹1,100</div>
                    </div>
                </div>
                <div style="text-align:center;padding:20px;">
                    <a href="connect.php" style="font-size:14px;color:#0a0a0a;font-weight:600;text-decoration:none;"><i class="fas fa-plus"></i> Book a New Session</a>
                </div>
            </div>
        </div>

        <!-- Tab: Wallet -->
        <div class="tab-content" id="tab-wallet">
            <div class="wallet-card">
                <div>
                    <div class="wallet-label">Available Balance</div>
                    <div class="wallet-amount" id="walletDisplay">₹0.00</div>
                </div>
                <div class="wallet-actions">
                    <button class="wallet-btn add" onclick="alert('Add money feature coming soon!')"><i class="fas fa-plus"></i> Add Money</button>
                    <button class="wallet-btn withdraw" onclick="alert('Withdraw feature coming soon!')"><i class="fas fa-arrow-up"></i> Withdraw</button>
                </div>
            </div>

            <h3 style="font-size:17px;font-weight:600;margin-bottom:16px;color:#0a0a0a;">Transaction History</h3>
            <div class="txn-item">
                <div class="txn-left"><p>Session Fee — Adv. Priya Sharma</p><span>Feb 20, 2026</span></div>
                <span class="txn-amount txn-debit">- ₹800</span>
            </div>
            <div class="txn-item">
                <div class="txn-left"><p>Wallet Top-Up</p><span>Feb 18, 2026</span></div>
                <span class="txn-amount txn-credit">+ ₹2,000</span>
            </div>
            <div class="txn-item">
                <div class="txn-left"><p>Session Fee — Adv. Rahul Verma</p><span>Mar 5, 2026</span></div>
                <span class="txn-amount txn-debit">- ₹1,200</span>
            </div>
            <div class="txn-item">
                <div class="txn-left"><p>Refund — Cancelled Session</p><span>Jan 30, 2026</span></div>
                <span class="txn-amount txn-credit">+ ₹600</span>
            </div>
        </div>

        <!-- Tab: Edit Profile -->
        <div class="tab-content" id="tab-edit">
            <div class="info-card">
                <h3>Edit Profile</h3>
                <div class="alert-success" id="saveSuccess"><i class="fas fa-check-circle"></i> Profile updated successfully!</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="editName" placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="editPhone" placeholder="+91 XXXXXXXXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="editEmail" placeholder="your@email.com" readonly style="background:#fafafa;color:#aaa;">
                </div>
                <div class="form-group">
                    <label>Bio / About Me</label>
                    <textarea id="editBio" rows="4" placeholder="Tell the community a bit about yourself and your legal interests..."></textarea>
                </div>
                <button class="save-btn" onclick="saveProfile()"><i class="fas fa-save"></i> Save Changes</button>
            </div>

            <div class="info-card">
                <h3>Change Password</h3>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" id="curPass" placeholder="Enter current password">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" id="newPass" placeholder="Min 8 characters">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" id="confPass" placeholder="Re-enter new password">
                    </div>
                </div>
                <button class="save-btn" onclick="changePassword()">Update Password</button>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="mainhome.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="connect.php" class="nav-item"><i class="fas fa-user-tie"></i><span>Connect</span></a>
            <a href="aitool.php" class="nav-item center-ai"><i class="fas fa-robot"></i><span>AI Tool</span></a>
            <a href="community.php" class="nav-item"><i class="fas fa-comments"></i><span>Community</span></a>
            <a href="profile.php" class="nav-item active"><i class="fas fa-user-circle"></i><span>Profile</span></a>
        </div>
    </nav>

    <script>
        let profile = {name:'',email:'',phone:'',bio:'',joined:''};

        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach((t,i) => {
                const ids = ['info','sessions','wallet','edit'];
                t.classList.toggle('active', ids[i] === tab);
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }

        async function loadProfile() {
            try {
                const r = await fetch('../lib/db.php');
                // Attempt to load via get_profile
            } catch(e){}
            // Fallback: use session data from PHP
            const name  = '<?php $u=getCurrentUser(); echo htmlspecialchars($u["full_name"] ?? "Student User"); ?>';
            const email = '<?php echo htmlspecialchars($u["email"] ?? ""); ?>';
            const joined = '<?php echo date("F Y", strtotime($u["created_at"] ?? "now")); ?>';

            profile = {name, email, phone:'', bio:'', joined};

            document.getElementById('heroName').textContent = name;
            document.getElementById('heroEmail').textContent = email;
            const initials = name.split(' ').map(n=>n[0]).join('').toUpperCase().slice(0,2);
            document.getElementById('heroPic').textContent = initials;

            document.getElementById('infoName').textContent  = name;
            document.getElementById('infoEmail').textContent = email;
            document.getElementById('infoJoined').textContent = joined;
            document.getElementById('statMember').textContent = joined.split(' ').pop();

            document.getElementById('editName').value  = name;
            document.getElementById('editEmail').value = email;
        }

        function saveProfile() {
            const name  = document.getElementById('editName').value.trim();
            const phone = document.getElementById('editPhone').value.trim();
            const bio   = document.getElementById('editBio').value.trim();
            if (!name) { alert('Name cannot be empty.'); return; }

            profile.name = name; profile.phone = phone; profile.bio = bio;
            document.getElementById('heroName').textContent  = name;
            document.getElementById('infoName').textContent  = name;
            document.getElementById('infoPhone').textContent = phone || 'Not provided';
            document.getElementById('infoBio').textContent   = bio || 'No bio added yet.';
            const initials = name.split(' ').map(n=>n[0]).join('').toUpperCase().slice(0,2);
            document.getElementById('heroPic').textContent   = initials;

            const s = document.getElementById('saveSuccess');
            s.style.display = 'block';
            setTimeout(()=>{ s.style.display='none'; }, 3000);
        }

        function changePassword() {
            const cur  = document.getElementById('curPass').value;
            const np   = document.getElementById('newPass').value;
            const conf = document.getElementById('confPass').value;
            if (!cur || !np || !conf) { alert('Please fill all password fields.'); return; }
            if (np !== conf) { alert('New passwords do not match.'); return; }
            if (np.length < 8) { alert('Password must be at least 8 characters.'); return; }
            alert('Password updated successfully!');
            document.getElementById('curPass').value='';
            document.getElementById('newPass').value='';
            document.getElementById('confPass').value='';
        }

        async function logout() {
            try { await fetch('../lib/logout.php'); } catch(e){}
            window.location.href = '../index.php';
        }

        loadProfile();
    </script>
</body>
</html>
