<?php
/**
 * Law Connectors - Database Setup Script
 * Run this once to create the database and all tables.
 * Access via: http://localhost/law/setup.php
 */

$host = 'localhost';
$port = '3306';
$dbName = 'law';
$user = 'law';
$pass = 'law';

$errors = [];
$success = [];

try {
    // Connect without selecting a DB first
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $success[] = "Database '$dbName' created/verified.";

    // Select the database
    $pdo->exec("USE `$dbName`");

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255),
        phone VARCHAR(20),
        role VARCHAR(50) NOT NULL DEFAULT 'user',
        profile_image VARCHAR(500),
        bio TEXT,
        wallet_balance DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    $success[] = "Table 'users' created/verified.";

    // Create expert_profiles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expert_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        specialization VARCHAR(255),
        experience_years INT,
        language VARCHAR(255),
        availability_status VARCHAR(50) DEFAULT 'available',
        hourly_rate DECIMAL(10,2),
        rating DECIMAL(3,2) DEFAULT 0.00,
        total_reviews INT DEFAULT 0,
        total_sessions INT DEFAULT 0,
        verification_status VARCHAR(50) DEFAULT 'pending',
        probono_participation TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'expert_profiles' created/verified.";

    // Create consultation_sessions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS consultation_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        expert_id INT NOT NULL,
        session_date DATETIME NOT NULL,
        duration INT DEFAULT 60,
        session_type VARCHAR(50) DEFAULT 'video',
        status VARCHAR(50) DEFAULT 'pending',
        amount DECIMAL(10,2),
        commission DECIMAL(10,2),
        notes TEXT,
        rating INT,
        review TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'consultation_sessions' created/verified.";

    // Create notifications table
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'system',
        is_read TINYINT(1) DEFAULT 0,
        link VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'notifications' created/verified.";

    // Create wallet_transactions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS wallet_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        transaction_type VARCHAR(50) DEFAULT 'credit',
        amount DECIMAL(10,2) NOT NULL,
        description TEXT,
        reference_id VARCHAR(255),
        status VARCHAR(50) DEFAULT 'completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'wallet_transactions' created/verified.";

    // Create forum_questions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS forum_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(500) NOT NULL,
        question TEXT NOT NULL,
        category VARCHAR(100),
        is_anonymous TINYINT(1) DEFAULT 0,
        views INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    $success[] = "Table 'forum_questions' created/verified.";

    // Create forum_answers table
    $pdo->exec("CREATE TABLE IF NOT EXISTS forum_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        user_id INT NOT NULL,
        answer TEXT NOT NULL,
        is_helpful INT DEFAULT 0,
        is_best_answer TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (question_id) REFERENCES forum_questions(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'forum_answers' created/verified.";

    // Create reviews table
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        expert_id INT NOT NULL,
        user_id INT NOT NULL,
        session_id INT,
        rating INT NOT NULL,
        review TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'reviews' created/verified.";

    // Create data_records table
    $pdo->exec("CREATE TABLE IF NOT EXISTS data_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        types VARCHAR(100) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        content TEXT,
        file_path VARCHAR(500),
        status VARCHAR(50) DEFAULT 'draft',
        is_public TINYINT(1) DEFAULT 0,
        created_by_role VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $success[] = "Table 'data_records' created/verified.";

    // Create a default admin user (password: Admin@123)
    $adminEmail = 'admin@lawconnectors.com';
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$adminEmail]);
    if (!$check->fetch()) {
        $adminPass = password_hash('Admin@123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)")
            ->execute([$adminEmail, $adminPass, 'Admin User', 'admin']);
        $success[] = "Default admin created: <strong>admin@lawconnectors.com</strong> / <strong>Admin@123</strong>";
    } else {
        $success[] = "Admin user already exists.";
    }

} catch (PDOException $e) {
    $errors[] = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Law Connectors - Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 60px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
        h1 { color: #667eea; margin-bottom: 5px; }
        h2 { margin: 20px 0 10px; color: #333; font-size: 16px; }
        .ok  { color: #155724; background: #d4edda; padding: 8px 14px; border-radius: 5px; margin: 5px 0; }
        .err { color: #721c24; background: #f8d7da; padding: 8px 14px; border-radius: 5px; margin: 5px 0; }
        a.btn { display: inline-block; margin-top: 20px; padding: 12px 25px; background: #667eea; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; }
        a.btn:hover { background: #5a6fd6; }
        .note { background: #fff3cd; padding: 10px 14px; border-radius: 5px; margin-top: 15px; font-size: 13px; color: #856404; }
    </style>
</head>
<body>
<div class="card">
    <h1>⚙️ Law Connectors - Database Setup</h1>
    <p style="color:#666">Setting up your local database...</p>

    <?php if (!empty($errors)): ?>
        <h2>❌ Errors</h2>
        <?php foreach ($errors as $e): ?>
            <div class="err"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <div class="note">
            Make sure <strong>MySQL / XAMPP</strong> is running on your machine.<br>
            If you use a password for root, edit <code>setup.php</code> and <code>lib/db.php</code> accordingly.
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <h2>✅ Setup Log</h2>
        <?php foreach ($success as $s): ?>
            <div class="ok"><?= $s ?></div>
        <?php endforeach; ?>
        <a class="btn" href="index.php">→ Go to Login</a>
        <div class="note">✅ Setup complete! <strong>Delete or rename this file</strong> after first run for security.</div>
    <?php endif; ?>
</div>
</body>
</html>
