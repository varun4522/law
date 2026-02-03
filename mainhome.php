<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Law Application</title>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Header */
        header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            font-size: 14px;
        }

        .logout-btn {
            padding: 8px 16px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #004085;
        }

        /* Navigation */
        nav {
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
            padding: 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            gap: 30px;
        }

        nav a {
            padding: 15px 0;
            color: #333;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            transition: border-color 0.3s, color 0.3s;
            display: block;
            font-size: 14px;
        }

        nav a:hover,
        nav a.active {
            border-bottom-color: #007bff;
            color: #007bff;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome-section {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-section h1 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: #666;
            line-height: 1.6;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .card-action {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Content Section */
        .content-section {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .content-section h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th {
            background-color: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
            color: #333;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .data-table tr:hover {
            background-color: #f9f9f9;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        /* Alert */
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            display: none;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* User Role Badge */
        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .role-badge.user {
            background-color: #cfe2ff;
            color: #084298;
        }

        .role-badge.expert {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .role-badge.admin {
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">⚖️ Law Application</div>
            <div class="user-info">
                <div class="user-name">
                    <span id="userFullName">Loading...</span>
                    <span id="roleB adge" class="role-badge"></span>
                </div>
                <button class="logout-btn" onclick="logout()">Logout</button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="#dashboard" class="nav-link active" onclick="switchTab('dashboard')">Dashboard</a>
            <a href="#mydata" class="nav-link" onclick="switchTab('mydata')">My Data</a>
            <a href="#create" class="nav-link" onclick="switchTab('create')">Create</a>
            <a href="#experts" class="nav-link" id="expertsNav" onclick="switchTab('experts')" style="display:none;">Experts</a>
            <a href="#settings" class="nav-link" onclick="switchTab('settings')">Settings</a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <div id="alertBox" class="alert"></div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="welcome-section">
                <h1>Welcome to Law Application! 👋</h1>
                <p>This is your personal dashboard. Here you can manage your data, explore legal resources, and connect with experts in the field.</p>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>📊 My Data</h3>
                    <p>View and manage all your stored data in one place.</p>
                    <div class="card-action">
                        <button class="btn btn-primary" onclick="switchTab('mydata')">View Data</button>
                    </div>
                </div>

                <div class="card">
                    <h3>➕ Create New</h3>
                    <p>Add new data to your collection with our easy-to-use form.</p>
                    <div class="card-action">
                        <button class="btn btn-primary" onclick="switchTab('create')">Create</button>
                    </div>
                </div>

                <div class="card" id="expertsCard" style="display:none;">
                    <h3>👨‍⚖️ Find Experts</h3>
                    <p>Connect with legal experts and professionals in your field.</p>
                    <div class="card-action">
                        <button class="btn btn-primary" onclick="switchTab('experts')">View Experts</button>
                    </div>
                </div>

                <div class="card">
                    <h3>⚙️ Settings</h3>
                    <p>Manage your account preferences and security settings.</p>
                    <div class="card-action">
                        <button class="btn btn-primary" onclick="switchTab('settings')">Go to Settings</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Data Tab -->
        <div id="mydata" class="tab-content" style="display:none;">
            <div class="content-section">
                <h2>My Data</h2>
                <div id="dataContainer">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Loading your data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Tab -->
        <div id="create" class="tab-content" style="display:none;">
            <div class="content-section">
                <h2>Create New Record</h2>
                <form id="createForm" style="margin-top: 20px;">
                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom: 5px; font-weight: bold;">Title</label>
                        <input type="text" id="recordTitle" placeholder="Enter title" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial; font-size: 14px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom: 5px; font-weight: bold;">Type</label>
                        <select id="recordType" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial; font-size: 14px;">
                            <option value="">-- Select Type --</option>
                            <option value="legal_document">Legal Document</option>
                            <option value="case_study">Case Study</option>
                            <option value="research">Research</option>
                            <option value="article">Article</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom: 5px; font-weight: bold;">Description</label>
                        <textarea id="recordDescription" placeholder="Enter description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial; font-size: 14px; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom: 5px; font-weight: bold;">Content</label>
                        <textarea id="recordContent" placeholder="Enter content" rows="6" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial; font-size: 14px; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" id="recordPublic">
                            <span style="font-weight: bold;">Make this record public</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Create Record</button>
                </form>
            </div>
        </div>

        <!-- Experts Tab (Admin/Expert only) -->
        <div id="experts" class="tab-content" style="display:none;">
            <div class="content-section">
                <h2>Experts Directory</h2>
                <div id="expertsContainer">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Loading experts...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings" class="tab-content" style="display:none;">
            <div class="content-section">
                <h2>Account Settings</h2>
                <div style="margin-top: 20px;">
                    <h3 style="color: #007bff; margin-bottom: 15px;">Profile Information</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                        <input type="email" id="settingsEmail" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f5f5f5;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                        <input type="text" id="settingsName" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Role</label>
                        <input type="text" id="settingsRole" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f5f5f5;">
                    </div>

                    <button class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
                </div>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

                <div style="margin-top: 20px;">
                    <h3 style="color: #007bff; margin-bottom: 15px;">Change Password</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                        <input type="password" id="newPassword" placeholder="Enter new password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <button class="btn btn-primary" onclick="changePassword()">Change Password</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Supabase Configuration
        const SUPABASE_URL = 'https://zcuadqnwnradhwgytspb.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InpjdWFkcW53bnJhZGh3Z3l0c3BiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzAwNTY1NDMsImV4cCI6MjA4NTYzMjU0M30.btn0Ag5Zeri27QG2NQxFIiQoaLTzSA7RMlOG3ggF9tg';

        // Initialize Supabase
        const { createClient } = window.supabase;
        const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // Global Variables
        let currentUser = null;
        let userRole = null;

        // Initialize on page load
        window.addEventListener('load', async () => {
            await checkAuthAndLoadUser();
        });

        // Check authentication status
        async function checkAuthAndLoadUser() {
            const { data: { user } } = await supabaseClient.auth.getUser();

            if (!user) {
                window.location.href = 'index.php';
                return;
            }

            currentUser = user;
            await loadUserProfile();
        }

        // Load user profile
        async function loadUserProfile() {
            try {
                const { data, error } = await supabaseClient
                    .from('profiles')
                    .select('*')
                    .eq('id', currentUser.id)
                    .single();

                if (error) throw error;

                userRole = data.role;
                document.getElementById('userFullName').textContent = data.full_name || 'User';
                document.getElementById('settingsEmail').value = data.email;
                document.getElementById('settingsName').value = data.full_name;
                document.getElementById('settingsRole').value = data.role.toUpperCase();

                // Set role badge
                const roleBadge = document.getElementById('roleBadge');
                roleBadge.textContent = data.role.toUpperCase();
                roleBadge.className = `role-badge ${data.role}`;

                // Show/hide expert features for admin and experts
                if (data.role === 'admin' || data.role === 'expert') {
                    document.getElementById('expertsNav').style.display = 'block';
                    document.getElementById('expertsCard').style.display = 'block';
                }

                // Load initial data
                await loadUserData();

            } catch (error) {
                console.error('Error loading profile:', error);
                showAlert('Error loading profile', 'error');
            }
        }

        // Load user data
        async function loadUserData() {
            try {
                const { data, error } = await supabaseClient
                    .from('data_records')
                    .select('*')
                    .eq('user_id', currentUser.id)
                    .order('created_at', { ascending: false });

                if (error) throw error;

                displayUserData(data || []);
            } catch (error) {
                console.error('Error loading data:', error);
            }
        }

        // Display user data in table
        function displayUserData(records) {
            const container = document.getElementById('dataContainer');

            if (!records || records.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No data found. Create your first record!</p></div>';
                return;
            }

            let html = '<table class="data-table"><thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Public</th><th>Created</th></tr></thead><tbody>';

            records.forEach(record => {
                const date = new Date(record.created_at).toLocaleDateString();
                const isPublic = record.is_public ? '✓' : '✗';
                html += `<tr>
                    <td>${record.title}</td>
                    <td>${record.types}</td>
                    <td>${record.status}</td>
                    <td>${isPublic}</td>
                    <td>${date}</td>
                </tr>`;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        }

        // Create form submission
        document.getElementById('createForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const title = document.getElementById('recordTitle').value.trim();
            const types = document.getElementById('recordType').value;
            const description = document.getElementById('recordDescription').value.trim();
            const content = document.getElementById('recordContent').value.trim();
            const isPublic = document.getElementById('recordPublic').checked;

            if (!title || !types) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }

            try {
                const { error } = await supabaseClient
                    .from('data_records')
                    .insert([{
                        user_id: currentUser.id,
                        title: title,
                        types: types,
                        description: description,
                        content: content,
                        is_public: isPublic,
                        status: 'draft',
                        created_by_role: userRole
                    }]);

                if (error) throw error;

                showAlert('Record created successfully!', 'success');
                document.getElementById('createForm').reset();
                await loadUserData();
                switchTab('mydata');

            } catch (error) {
                console.error('Error creating record:', error);
                showAlert('Error creating record: ' + error.message, 'error');
            }
        });

        // Update profile
        async function updateProfile() {
            const name = document.getElementById('settingsName').value.trim();

            if (!name) {
                showAlert('Please enter your full name', 'error');
                return;
            }

            try {
                const { error } = await supabaseClient
                    .from('profiles')
                    .update({ full_name: name })
                    .eq('id', currentUser.id);

                if (error) throw error;

                showAlert('Profile updated successfully!', 'success');

            } catch (error) {
                console.error('Error updating profile:', error);
                showAlert('Error updating profile: ' + error.message, 'error');
            }
        }

        // Change password
        async function changePassword() {
            const newPassword = document.getElementById('newPassword').value;

            if (!newPassword || newPassword.length < 6) {
                showAlert('Password must be at least 6 characters', 'error');
                return;
            }

            try {
                const { error } = await supabaseClient.auth.updateUser({
                    password: newPassword
                });

                if (error) throw error;

                showAlert('Password changed successfully!', 'success');
                document.getElementById('newPassword').value = '';

            } catch (error) {
                console.error('Error changing password:', error);
                showAlert('Error changing password: ' + error.message, 'error');
            }
        }

        // Switch tabs
        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.style.display = 'none');

            // Show selected tab
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }

            // Update nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => link.classList.remove('active'));
            event.target.classList.add('active');

            // Load experts if needed
            if (tabName === 'experts') {
                loadExperts();
            }
        }

        // Load experts
        async function loadExperts() {
            try {
                const { data, error } = await supabaseClient
                    .from('profiles')
                    .select('*')
                    .in('role', ['expert', 'admin'])
                    .order('full_name', { ascending: true });

                if (error) throw error;

                displayExperts(data || []);
            } catch (error) {
                console.error('Error loading experts:', error);
            }
        }

        // Display experts
        function displayExperts(experts) {
            const container = document.getElementById('expertsContainer');

            if (!experts || experts.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No experts found</p></div>';
                return;
            }

            let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">';

            experts.forEach(expert => {
                html += `<div class="card">
                    <h3>${expert.full_name || 'Expert'}</h3>
                    <p><strong>Email:</strong> ${expert.email}</p>
                    <p><strong>Role:</strong> <span class="role-badge ${expert.role}">${expert.role.toUpperCase()}</span></p>
                    <div class="card-action">
                        <a href="mailto:${expert.email}" class="btn btn-primary" style="text-align: center;">Contact</a>
                    </div>
                </div>`;
            });

            html += '</div>';
            container.innerHTML = html;
        }

        // Show alert
        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = `alert ${type}`;
            setTimeout(() => {
                alertBox.className = 'alert';
            }, 4000);
        }

        // Logout
        async function logout() {
            try {
                const { error } = await supabaseClient.auth.signOut();
                if (error) throw error;

                window.location.href = 'index.php';
            } catch (error) {
                console.error('Error logging out:', error);
                showAlert('Error logging out', 'error');
            }
        }

        // Check auth state
        supabaseClient.auth.onAuthStateChange((event, session) => {
            if (event === 'SIGNED_OUT') {
                window.location.href = 'index.php';
            }
        });
    </script>
</body>
</html>
