<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Law Application</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .signup-container {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .signup-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .signup-container p {
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

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: Arial, sans-serif;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
        }

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            line-height: 1.6;
        }

        .requirement {
            margin: 3px 0;
        }

        .requirement.met {
            color: #28a745;
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
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
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

        .alert.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            display: block;
        }

        .loading {
            display: none;
            text-align: center;
            color: #666;
            font-size: 14px;
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
    <div class="signup-container">
        <h1>Create Account</h1>
        <p>Join Law Application Today</p>

        <div id="alertBox" class="alert"></div>
        <div id="loading" class="loading"><span class="spinner"></span>Creating account...</div>

        <form id="signupForm">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <div class="password-requirements">
                    <div class="requirement" id="req-length">✓ At least 8 characters</div>
                    <div class="requirement" id="req-uppercase">✓ At least one uppercase letter</div>
                    <div class="requirement" id="req-lowercase">✓ At least one lowercase letter</div>
                    <div class="requirement" id="req-number">✓ At least one number</div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
            </div>

            <div class="form-group">
                <label for="role">I am a</label>
                <select id="role" name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="user">User</option>
                    <option value="expert">Expert/Professional</option>
                </select>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="index.php">Login here</a>
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
            const role = document.getElementById('role').value;

            // Validation
            if (!fullName || !email || !password || !confirmPassword || !role) {
                showAlert('Please fill in all fields.', 'error');
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
                // Step 1: Create auth user
                const { data: { user }, error: authError } = await supabaseClient.auth.signUp({
                    email: email,
                    password: password
                });

                if (authError) {
                    throw new Error(authError.message);
                }

                if (!user) {
                    throw new Error('Failed to create user account.');
                }

                // Step 2: Insert into profiles table
                const { error: profileError } = await supabaseClient
                    .from('profiles')
                    .insert([
                        {
                            id: user.id,
                            email: email,
                            full_name: fullName,
                            role: role,
                            created_at: new Date().toISOString()
                        }
                    ]);

                if (profileError) {
                    throw new Error('Failed to create profile: ' + profileError.message);
                }

                // Success
                showAlert('Account created successfully! Please check your email to verify your account. Redirecting to login...', 'success');
                loadingDiv.style.display = 'none';
                
                // Redirect to login after 3 seconds
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 3000);

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
