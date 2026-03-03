<?php
require_once 'lib/db.php';
if (isLoggedIn()) {
    header('Location: mainhome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Law Connectors</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Panel - Black */
        .left-panel {
            flex: 1;
            background: #0a0a0a;
            color: #fff;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            top: -100px;
            right: -100px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.05);
            bottom: -50px;
            left: -50px;
        }

        .brand {
            position: relative;
            z-index: 1;
            margin-bottom: 60px;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .brand p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 300;
        }

        .features {
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 18px;
            flex-shrink: 0;
            font-size: 18px;
        }

        .feature-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .feature-content p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.5;
        }

        /* Right Panel - White */
        .right-panel {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .form-container {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: #0a0a0a;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-header p {
            color: #666;
            font-size: 15px;
        }

        .alert {
            padding: 14px 18px;
            margin-bottom: 24px;
            border-radius: 6px;
            font-size: 14px;
            display: none;
            border-left: 3px solid;
        }

        .alert.success {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #22c55e;
        }

        .alert.error {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .alert.info {
            background-color: #f0f9ff;
            color: #075985;
            border-color: #3b82f6;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 24px;
            color: #666;
            font-size: 14px;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0a0a0a;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #0a0a0a;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e5e5e5;
            border-radius: 6px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: #fff;
        }

        input:focus {
            outline: none;
            border-color: #0a0a0a;
            box-shadow: 0 0 0 3px rgba(10, 10, 10, 0.05);
        }

        .password-requirements {
            margin-top: 10px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .requirement {
            font-size: 12px;
            color: #6b7280;
            margin: 5px 0;
            padding-left: 20px;
            position: relative;
        }

        .requirement::before {
            content: '○';
            position: absolute;
            left: 0;
            color: #d1d5db;
        }

        .requirement.met {
            color: #059669;
        }

        .requirement.met::before {
            content: '✓';
            color: #059669;
        }

        .type-helper {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 4px;
            border-left: 3px solid #0a0a0a;
        }

        .type-helper strong {
            color: #0a0a0a;
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #0a0a0a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
            font-family: 'Inter', sans-serif;
        }

        button[type="submit"]:hover {
            background: #1a1a1a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 10, 10, 0.15);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        button:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            margin-top: 28px;
            font-size: 14px;
            color: #6b7280;
        }

        .login-link a {
            color: #0a0a0a;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s ease;
        }

        .login-link a:hover {
            opacity: 0.7;
        }

        @media (max-width: 968px) {
            .left-panel {
                display: none;
            }
            
            .right-panel {
                flex: 1;
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .right-panel {
                padding: 30px 20px;
            }
            
            .form-header h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="brand">
                <h1>Law Connectors</h1>
                <p>Join Our Legal Network Today</p>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">⚖️</div>
                    <div class="feature-content">
                        <h3>Expert Consultations</h3>
                        <p>Connect with verified legal experts for professional advice</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">📚</div>
                    <div class="feature-content">
                        <h3>Legal Resources</h3>
                        <p>Access comprehensive legal knowledge and community support</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-content">
                        <h3>Secure Platform</h3>
                        <p>Your data and consultations are protected with enterprise security</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-content">
                        <h3>Instant Access</h3>
                        <p>Book sessions, chat with AI, and get answers immediately</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Start your legal journey with us</p>
                </div>

                <div id="alertBox" class="alert"></div>
                <div id="loading" class="loading"><span class="spinner"></span>Creating your account...</div>

                <form id="signupForm">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">At least 8 characters</div>
                            <div class="requirement" id="req-uppercase">At least one uppercase letter</div>
                            <div class="requirement" id="req-lowercase">At least one lowercase letter</div>
                            <div class="requirement" id="req-number">At least one number</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Account Type</label>
                        <input type="number" id="type" name="type" placeholder="Enter 1, 2, or 3" min="1" max="3" required>
                        <div class="type-helper">
                            <strong>1</strong> = User (Client seeking legal help) · 
                            <strong>2</strong> = Expert (Legal professional) · 
                            <strong>3</strong> = Admin (Platform administrator)
                        </div>
                    </div>

                    <button type="submit">Create Account</button>
                </form>

                <div class="login-link">
                    Already have an account? <a href="index.php">Sign in</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const signupForm = document.getElementById('signupForm');
        const alertBox = document.getElementById('alertBox');
        const loadingDiv = document.getElementById('loading');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        // Password validation
        const passwordRequirements = {
            length: /^.{8,}$/,
            uppercase: /[A-Z]/,
            lowercase: /[a-z]/,
            number: /\d/
        };

        passwordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);

        function validatePassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Check length
            const lengthMet = passwordRequirements.length.test(password);
            updateRequirement('length', lengthMet);

            // Check uppercase
            const uppercaseMet = passwordRequirements.uppercase.test(password);
            updateRequirement('uppercase', uppercaseMet);

            // Check lowercase
            const lowercaseMet = passwordRequirements.lowercase.test(password);
            updateRequirement('lowercase', lowercaseMet);

            // Check number
            const numberMet = passwordRequirements.number.test(password);
            updateRequirement('number', numberMet);

            // Check match
            if (password && confirmPassword && password === confirmPassword) {
                showAlert('Passwords match!', 'info');
            }
        }

        function updateRequirement(name, met) {
            const element = document.getElementById(`req-${name}`);
            if (met) {
                element.classList.add('met');
            } else {
                element.classList.remove('met');
            }
        }

        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.className = `alert ${type}`;
            if (type !== 'info') {
                setTimeout(() => {
                    alertBox.className = 'alert';
                }, 5000);
            }
        }

        // Form Submission
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const type = parseInt(document.getElementById('type').value);

            // Validation
            if (!fullName || !email || !password || !confirmPassword || !type) {
                showAlert('Please fill in all fields.', 'error');
                return;
            }

            if (![1, 2, 3].includes(type)) {
                showAlert('Invalid account type. Enter 1, 2, or 3.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Passwords do not match.', 'error');
                return;
            }

            // Check password requirements
            const isPasswordValid = 
                passwordRequirements.length.test(password) &&
                passwordRequirements.uppercase.test(password) &&
                passwordRequirements.lowercase.test(password) &&
                passwordRequirements.number.test(password);

            if (!isPasswordValid) {
                showAlert('Password does not meet requirements.', 'error');
                return;
            }

            // Check email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Please enter a valid email address.', 'error');
                return;
            }

            // Show loading
            loadingDiv.style.display = 'block';
            signupForm.style.display = 'none';

            try {
                // Send signup request to PHP backend
                const response = await fetch('lib/signup_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        fullName: fullName,
                        type: type
                    })
                });

                const result = await response.json();

                if (!response.ok || result.error) {
                    throw new Error(result.error || 'Signup failed');
                }

                // Success
                showAlert('Account created successfully! Redirecting to login...', 'success');
                loadingDiv.style.display = 'none';
                
                // Redirect to login after 2 seconds
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);

            } catch (error) {
                console.error('Signup Error:', error);
                showAlert(error.message || 'An error occurred. Please try again.', 'error');
                loadingDiv.style.display = 'none';
                signupForm.style.display = 'block';
            }
        });
    </script>
</body>
</html>
