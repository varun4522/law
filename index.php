<?php
require_once 'lib/db.php';
if (isLoggedIn()) {
    $role = intval($_SESSION['role'] ?? 1);
    if ($role === 2)      header('Location: expert/newpage.php');
    elseif ($role === 3)  header('Location: admin/1newpage.php');
    else                  header('Location: student/mainhome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Law Connectors</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #0a0a0a;
            --primary-light: #1a1a1a;
            --accent: #1f3a5f;
            --accent-hover: #2a4a7f;
            --bg-light: #f8f9fa;
            --text-dark: #0a0a0a;
            --text-muted: #6c757d;
            --border: #dee2e6;
            --success: #27ae60;
            --error: #e74c3c;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            color: var(--text-dark);
        }

        /* Left panel */
        .left-panel {
            background: linear-gradient(135deg, #0f2d5c 0%, #0a0a0a 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 60px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 350px; height: 350px;
            border: 80px solid rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 250px; height: 250px;
            border: 60px solid rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .brand { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            z-index: 1;
            margin-bottom: 20px;
        }
        .brand-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            border-radius: 10px;
            display: flex; 
            align-items: center; 
            justify-content: center;
            box-shadow: 0 4px 12px rgba(255,255,255,0.1);
        }
        .brand-icon i { color: #0f2d5c; font-size: 24px; }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px; 
            font-weight: 700; 
            color: #fff; 
            letter-spacing: 0.5px;
        }
        .hero-text { z-index: 1; }
        .hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 4vw, 56px); 
            font-weight: 700; 
            color: #fff;
            line-height: 1.2; 
            margin-bottom: 20px;
        }
        .hero-text h1 em { 
            font-style: italic; 
            color: #a8d5ff;
        }
        .hero-text p { 
            font-size: 16px; 
            color: #d0d0d0; 
            line-height: 1.8; 
            max-width: 420px;
            font-weight: 300;
        }
        .features { z-index: 1; }
        .feature-item { 
            display: flex; 
            align-items: flex-start; 
            gap: 14px; 
            margin-bottom: 18px;
        }
        .feature-dot {
            width: 6px; 
            height: 6px; 
            background: #a8d5ff; 
            border-radius: 50%;
            margin-top: 8px; 
            flex-shrink: 0;
        }
        .feature-item span { 
            font-size: 14px; 
            color: #d0d0d0; 
            line-height: 1.6;
            font-weight: 300;
        }

        /* Right panel */
        .right-panel {
            display: flex; 
            align-items: center; 
            justify-content: center;
            padding: 40px; 
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
        }
        .form-card {
            background: #fff;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 48px 44px;
            width: 100%; 
            max-width: 440px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-header { margin-bottom: 36px; }
        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px; 
            font-weight: 700; 
            color: var(--text-dark); 
            margin-bottom: 8px;
        }
        .form-header p { 
            font-size: 15px; 
            color: var(--text-muted);
            font-weight: 300;
        }
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block; 
            font-size: 13px; 
            font-weight: 600; 
            color: var(--text-dark);
            text-transform: uppercase; 
            letter-spacing: 0.6px; 
            margin-bottom: 10px;
        }
        .form-group input {
            width: 100%; 
            padding: 14px 16px;
            border: 1.5px solid #e0e0e0; 
            border-radius: 6px;
            font-family: 'Inter', sans-serif; 
            font-size: 15px; 
            color: #111;
            background: #fff; 
            transition: all 0.3s ease;
        }
        .form-group input:hover {
            border-color: #d0d0d0;
        }
        .form-group input:focus {
            outline: none; 
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(31, 58, 95, 0.1);
        }
        .form-group input::placeholder { 
            color: #b0b0b0;
        }
        .remember-forgot {
            display: flex; 
            justify-content: space-between;
            align-items: center; 
            margin-bottom: 28px;
            gap: 16px;
        }
        .remember-label {
            display: flex; 
            align-items: center; 
            gap: 8px;
            font-size: 14px; 
            color: var(--text-muted); 
            cursor: pointer;
            font-weight: 500;
        }
        .remember-label input[type="checkbox"] { 
            width: 16px; 
            height: 16px; 
            accent-color: var(--accent); 
            cursor: pointer;
            border-radius: 3px;
        }
        .forgot-link {
            font-size: 14px; 
            color: var(--accent); 
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .forgot-link:hover { 
            color: var(--accent-hover);
        }
        .btn-primary {
            width: 100%; 
            padding: 14px 20px; 
            background: linear-gradient(135deg, var(--accent) 0%, #1a2d47 100%);
            color: #fff;
            border: none; 
            border-radius: 6px;
            font-family: 'Inter', sans-serif; 
            font-size: 15px; 
            font-weight: 600;
            letter-spacing: 0.3px; 
            cursor: pointer; 
            transition: all 0.3s ease;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px;
            box-shadow: 0 2px 8px rgba(31, 58, 95, 0.2);
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, var(--accent-hover) 0%, #213452 100%);
            box-shadow: 0 4px 12px rgba(31, 58, 95, 0.3);
            transform: translateY(-1px);
        }
        .btn-primary:active { 
            transform: translateY(0);
        }
        .btn-primary:disabled { 
            background: #b0b0b0; 
            cursor: not-allowed;
            box-shadow: none;
        }
        .divider {
            display: flex; 
            align-items: center; 
            gap: 16px; 
            margin: 28px 0;
        }
        .divider::before, .divider::after { 
            content: ''; 
            flex: 1; 
            height: 1px; 
            background: #e8eaed;
        }
        .divider span { 
            font-size: 13px; 
            color: var(--text-muted); 
            white-space: nowrap;
            font-weight: 500;
        }
        .signup-link { 
            text-align: center; 
            font-size: 15px; 
            color: var(--text-muted);
        }
        .signup-link a {
            color: var(--accent); 
            font-weight: 600; 
            text-decoration: none;
            transition: color 0.2s;
        }
        .signup-link a:hover { 
            color: var(--accent-hover);
        }
        .alert {
            padding: 14px 16px; 
            border-radius: 6px; 
            font-size: 14px;
            margin-bottom: 20px; 
            display: none; 
            border-left: 4px solid;
            animation: slideDown 0.3s ease;
        }
        .alert.success { 
            background: #ecf9f3; 
            color: #155e4c; 
            border-left-color: var(--success);
            display: block;
        }
        .alert.error { 
            background: #fadad9; 
            color: #78241c; 
            border-left-color: var(--error);
            display: block;
        }
        .loading { 
            display: none; 
            text-align: center; 
            color: var(--text-muted); 
            font-size: 14px; 
            margin-bottom: 16px;
        }
        .spinner {
            display: inline-block; 
            width: 18px; 
            height: 18px;
            border: 2.5px solid #e0e0e0; 
            border-top-color: var(--accent);
            border-radius: 50%; 
            animation: spin 0.8s linear infinite;
            margin-right: 8px; 
            vertical-align: middle;
        }
        @keyframes spin { 
            to { transform: rotate(360deg); } 
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tablet view */
        @media (max-width: 1024px) {
            body { grid-template-columns: 1fr; }
            .left-panel { padding: 40px 50px; }
            .right-panel { padding: 30px; }
            .form-card { padding: 40px 36px; }
        }

        /* Mobile view */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { 
                display: flex;
                padding: 30px 24px;
                min-height: auto;
                background: linear-gradient(135deg, #0f2d5c 0%, #0a0a0a 100%);
            }
            .left-panel::before,
            .left-panel::after {
                display: none;
            }
            .brand { 
                margin-bottom: 0;
            }
            .brand-icon {
                width: 40px; 
                height: 40px;
            }
            .brand-icon i { 
                font-size: 20px;
            }
            .brand-name {
                font-size: 18px;
            }
            .hero-text { 
                display: none;
            }
            .features { 
                display: none;
            }
            .right-panel { 
                padding: 24px 20px; 
                background: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }
            .form-card { 
                border: none;
                padding: 32px 20px;
                box-shadow: none;
                max-width: 100%;
                background: #fff;
            }
            .form-header h2 {
                font-size: 26px;
            }
            .form-header p {
                font-size: 14px;
            }
            .form-group label {
                font-size: 12px;
            }
            .form-group input {
                padding: 13px 14px;
                font-size: 14px;
            }
            .btn-primary {
                padding: 13px 18px;
                font-size: 14px;
            }
        }

        /* Small mobile view */
        @media (max-width: 480px) {
            .left-panel {
                padding: 24px 16px;
                justify-content: center;
            }
            .brand {
                justify-content: center;
                margin-bottom: 0;
            }
            .form-card {
                padding: 28px 16px;
            }
            .form-header h2 {
                font-size: 24px;
            }
            .form-header { 
                margin-bottom: 28px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 24px;
            }
            .remember-label {
                font-size: 13px;
            }
            .forgot-link {
                font-size: 13px;
                width: 100%;
                text-align: right;
            }
        }
    </style>
</head>
<body>

<div class="left-panel">
    <div class="brand">
        <div class="brand-icon"><i class="fas fa-balance-scale"></i></div>
        <span class="brand-name">Law Connectors</span>
    </div>
    <div class="hero-text">
        <h1>Legal expertise,<br><em>at your fingertips.</em></h1>
        <p>Connect with verified legal professionals, get instant AI-powered legal guidance, and manage all your consultations in one place.</p>
    </div>
    <div class="features">
        <div class="feature-item"><div class="feature-dot"></div><span>Access 100+ verified legal experts across all practice areas</span></div>
        <div class="feature-item"><div class="feature-dot"></div><span>AI-powered legal assistant available 24/7 for instant answers</span></div>
        <div class="feature-item"><div class="feature-dot"></div><span>Anonymous community forum — ask lawyers freely</span></div>
        <div class="feature-item"><div class="feature-dot"></div><span>Secure session management and integrated wallet system</span></div>
    </div>
</div>

<div class="right-panel">
    <div class="form-card">
        <div class="form-header">
            <h2>Welcome back</h2>
            <p>Sign in to your Law Connectors account</p>
        </div>

        <div id="alertBox" class="alert"></div>
        <div id="loading" class="loading"><span class="spinner"></span>Signing in...</div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="you@example.com" 
                    required 
                    autocomplete="email"
                    aria-label="Email Address"
                >
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required 
                    autocomplete="current-password"
                    aria-label="Password"
                >
            </div>
            <div class="remember-forgot">
                <label class="remember-label">
                    <input type="checkbox" id="remember" name="remember" aria-label="Remember me"> 
                    Remember me
                </label>
                <a href="#" class="forgot-link" onclick="event.preventDefault(); alert('Password recovery feature coming soon!');">Forgot password?</a>
            </div>
            <button type="submit" class="btn-primary" id="loginBtn">
                <i class="fas fa-arrow-right"></i> Sign In
            </button>
        </form>

        <div class="divider"><span>New to Law Connectors?</span></div>
        <div class="signup-link"><a href="signup.php">Create an account →</a></div>
    </div>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const alertBox = document.getElementById('alertBox');
    const loadingDiv = document.getElementById('loading');
    const loginBtn = document.getElementById('loginBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'alert ' + type;
        alertBox.setAttribute('role', 'alert');
        
        if (type === 'error') {
            setTimeout(() => { 
                alertBox.className = 'alert'; 
            }, 5000);
        }
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function resetForm() {
        loadingDiv.style.display = 'none';
        loginBtn.disabled = false;
        loginBtn.innerHTML = '<i class="fas fa-arrow-right"></i> Sign In';
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const rememberMe = document.getElementById('remember').checked;

        // Validation
        if (!email || !password) { 
            showAlert('Please fill in all fields.', 'error'); 
            return; 
        }

        if (!validateEmail(email)) {
            showAlert('Please enter a valid email address.', 'error');
            return;
        }

        if (password.length < 6) {
            showAlert('Password must be at least 6 characters.', 'error');
            return;
        }

        loadingDiv.style.display = 'block';
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner"></span> Signing in...';

        try {
            const res = await fetch('lib/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            
            const result = await res.json();
            
            if (!res.ok || result.error) {
                throw new Error(result.error || 'Login failed');
            }

            // Save email if remember me is checked
            if (rememberMe) {
                localStorage.setItem('rememberEmail', email);
            } else {
                localStorage.removeItem('rememberEmail');
            }

            showAlert('Login successful! Redirecting...', 'success');
            
            const role = parseInt(result.data.user.role);
            let redirect = 'student/mainhome.php';
            if (role === 2) redirect = 'expert/newpage.php';
            else if (role === 3) redirect = 'admin/1newpage.php';
            
            setTimeout(() => { 
                window.location.href = redirect; 
            }, 900);
        } catch (err) {
            console.error('Login error:', err);
            showAlert(err.message || 'An error occurred. Please try again.', 'error');
            resetForm();
        }
    });

    // Load saved email on page load
    window.addEventListener('load', () => {
        const saved = localStorage.getItem('rememberEmail');
        if (saved) { 
            emailInput.value = saved;
            document.getElementById('remember').checked = true;
        }
    });

    // Clear alert when user starts typing
    emailInput.addEventListener('focus', () => {
        if (alertBox.className.includes('error')) {
            alertBox.className = 'alert';
        }
    });

    passwordInput.addEventListener('focus', () => {
        if (alertBox.className.includes('error')) {
            alertBox.className = 'alert';
        }
    });
</script>
</body>
</html>
