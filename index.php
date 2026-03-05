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
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f3;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Left panel */
        .left-panel {
            background: #0a0a0a;
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
            border: 80px solid rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 250px; height: 250px;
            border: 60px solid rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .brand { display: flex; align-items: center; gap: 12px; z-index: 1; }
        .brand-icon {
            width: 42px; height: 42px;
            background: #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-icon i { color: #0a0a0a; font-size: 20px; }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 0.3px;
        }
        .hero-text { z-index: 1; }
        .hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 4vw, 52px); font-weight: 700; color: #fff;
            line-height: 1.15; margin-bottom: 20px;
        }
        .hero-text h1 em { font-style: italic; color: #c8c8c8; }
        .hero-text p { font-size: 15px; color: #9a9a9a; line-height: 1.7; max-width: 380px; }
        .features { z-index: 1; }
        .feature-item { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px; }
        .feature-dot {
            width: 8px; height: 8px; background: #fff; border-radius: 50%;
            margin-top: 7px; flex-shrink: 0;
        }
        .feature-item span { font-size: 14px; color: #c0c0c0; line-height: 1.5; }

        /* Right panel */
        .right-panel {
            display: flex; align-items: center; justify-content: center;
            padding: 40px; background: #f5f5f3;
        }
        .form-card {
            background: #fff;
            border: 1px solid #e8e8e4;
            border-radius: 4px;
            padding: 48px 44px;
            width: 100%; max-width: 420px;
        }
        .form-header { margin-bottom: 36px; }
        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px; font-weight: 700; color: #0a0a0a; margin-bottom: 6px;
        }
        .form-header p { font-size: 14px; color: #888; }
        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block; font-size: 12px; font-weight: 600; color: #0a0a0a;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
        }
        .form-group input {
            width: 100%; padding: 13px 16px;
            border: 1.5px solid #ddd; border-radius: 2px;
            font-family: 'Inter', sans-serif; font-size: 14px; color: #111;
            background: #fff; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none; border-color: #0a0a0a;
            box-shadow: 0 0 0 3px rgba(10,10,10,0.06);
        }
        .form-group input::placeholder { color: #bbb; }
        .remember-forgot {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 28px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #555; cursor: pointer;
        }
        .remember-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: #0a0a0a; cursor: pointer; }
        .forgot-link {
            font-size: 13px; color: #555; text-decoration: none;
            border-bottom: 1px solid transparent; transition: border-color 0.2s;
        }
        .forgot-link:hover { border-bottom-color: #555; }
        .btn-primary {
            width: 100%; padding: 14px; background: #0a0a0a; color: #fff;
            border: none; border-radius: 2px;
            font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
            letter-spacing: 0.5px; cursor: pointer; transition: background 0.2s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary:hover { background: #222; }
        .btn-primary:active { transform: scale(0.99); }
        .btn-primary:disabled { background: #999; cursor: not-allowed; }
        .divider {
            display: flex; align-items: center; gap: 12px; margin: 24px 0;
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e8e8e4; }
        .divider span { font-size: 12px; color: #aaa; white-space: nowrap; }
        .signup-link { text-align: center; font-size: 14px; color: #666; }
        .signup-link a {
            color: #0a0a0a; font-weight: 600; text-decoration: none;
            border-bottom: 1.5px solid #0a0a0a;
        }
        .signup-link a:hover { opacity: 0.7; }
        .alert {
            padding: 12px 16px; border-radius: 2px; font-size: 13px;
            margin-bottom: 20px; display: none; border-left: 3px solid;
        }
        .alert.success { background: #f0f0f0; color: #111; border-left-color: #0a0a0a; display: block; }
        .alert.error { background: #fff5f5; color: #c0392b; border-left-color: #c0392b; display: block; }
        .loading { display: none; text-align: center; color: #888; font-size: 13px; margin-bottom: 16px; }
        .spinner {
            display: inline-block; width: 16px; height: 16px;
            border: 2px solid #ddd; border-top-color: #0a0a0a;
            border-radius: 50%; animation: spin 0.8s linear infinite;
            margin-right: 8px; vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 24px 16px; background: #fff; }
            .form-card { border: none; padding: 32px 24px; box-shadow: none; }
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
                <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            <div class="remember-forgot">
                <label class="remember-label">
                    <input type="checkbox" id="remember" name="remember"> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>
            <button type="submit" class="btn-primary" id="loginBtn">
                <i class="fas fa-arrow-right"></i> Sign In
            </button>
        </form>

        <div class="divider"><span>New to Law Connectors?</span></div>
        <div class="signup-link"><a href="signup.php">Create an account &rarr;</a></div>
    </div>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const alertBox  = document.getElementById('alertBox');
    const loadingDiv = document.getElementById('loading');
    const loginBtn  = document.getElementById('loginBtn');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'alert ' + type;
        if (type === 'error') setTimeout(() => { alertBox.className = 'alert'; }, 5000);
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('remember').checked;

        if (!email || !password) { showAlert('Please fill in all fields.', 'error'); return; }

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
            if (!res.ok || result.error) throw new Error(result.error || 'Login failed');

            if (rememberMe) localStorage.setItem('rememberEmail', email);
            else localStorage.removeItem('rememberEmail');

            showAlert('Login successful! Redirecting...', 'success');
            const role = parseInt(result.data.user.role);
            let redirect = 'student/mainhome.php';
            if (role === 2) redirect = 'expert/newpage.php';
            else if (role === 3) redirect = 'admin/1newpage.php';
            setTimeout(() => { window.location.href = redirect; }, 900);
        } catch (err) {
            showAlert(err.message || 'An error occurred', 'error');
            loadingDiv.style.display = 'none';
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fas fa-arrow-right"></i> Sign In';
        }
    });

    window.addEventListener('load', () => {
        const saved = localStorage.getItem('rememberEmail');
        if (saved) { document.getElementById('email').value = saved; document.getElementById('remember').checked = true; }
    });
</script>
</body>
</html>
