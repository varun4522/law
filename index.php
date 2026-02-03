<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Law Application</title>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: white;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .login-container p {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .remember-forgot a {
            color: #007bff;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .signup-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            display: none;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .loading {
            display: none;
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <p>Welcome to Law Application</p>

        <div id="alertBox" class="alert"></div>
        <div id="loading" class="loading"><span class="spinner"></span>Logging in...</div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="remember-forgot">
                <label>
                    <input type="checkbox" id="remember" name="remember"> Remember me
                </label>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>

            <button type="submit" id="loginBtn">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>

    <script>
        // Supabase Configuration
        const SUPABASE_URL = 'https://zcuadqnwnradhwgytspb.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InpjdWFkcW53bnJhZGh3Z3l0c3BiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzAwNTY1NDMsImV4cCI6MjA4NTYzMjU0M30.btn0Ag5Zeri27QG2NQxFIiQoaLTzSA7RMlOG3ggF9tg';

        // Initialize Supabase
        const { createClient } = window.supabase;
        const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // DOM Elements
        const loginForm = document.getElementById('loginForm');
        const alertBox = document.getElementById('alertBox');
        const loadingDiv = document.getElementById('loading');
        const loginBtn = document.getElementById('loginBtn');

        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.className = `alert ${type}`;
            if (type === 'error') {
                setTimeout(() => {
                    alertBox.className = 'alert';
                }, 5000);
            }
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('remember').checked;

            // Validation
            if (!email || !password) {
                showAlert('Please fill in all fields.', 'error');
                return;
            }

            // Show loading state
            loadingDiv.style.display = 'block';
            loginBtn.disabled = true;

            try {
                // Sign in with Supabase
                const { data: { user }, error } = await supabaseClient.auth.signInWithPassword({
                    email: email,
                    password: password
                });

                if (error) {
                    throw new Error(error.message);
                }

                if (!user) {
                    throw new Error('Login failed. Please try again.');
                }

                // Store remember me preference
                if (rememberMe) {
                    localStorage.setItem('rememberEmail', email);
                } else {
                    localStorage.removeItem('rememberEmail');
                }

                // Success message
                showAlert('Login successful! Redirecting to home...', 'success');

                // Redirect to mainhome.php after 1 second
                setTimeout(() => {
                    window.location.href = 'mainhome.php';
                }, 1000);

            } catch (error) {
                console.error('Login Error:', error);
                let errorMessage = error.message;

                if (error.message.includes('Invalid login credentials')) {
                    errorMessage = 'Invalid email or password. Please try again.';
                } else if (error.message.includes('Email not confirmed')) {
                    errorMessage = 'Please verify your email before logging in.';
                }

                showAlert(errorMessage, 'error');
                loadingDiv.style.display = 'none';
                loginBtn.disabled = false;
            }
        });

        // Load remembered email if exists
        window.addEventListener('load', () => {
            const rememberEmail = localStorage.getItem('rememberEmail');
            if (rememberEmail) {
                document.getElementById('email').value = rememberEmail;
                document.getElementById('remember').checked = true;
            }
        });

        // Check if user is already logged in
        supabaseClient.auth.onAuthStateChange((event, session) => {
            if (event === 'SIGNED_IN' && session) {
                // User is logged in, redirect to mainhome
                window.location.href = 'mainhome.php';
            }
        });
    </script>
</body>
</html>
