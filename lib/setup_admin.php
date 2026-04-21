<?php
/**
 * Admin Panel Setup Helper
 * Run this once to set up your first admin account
 * Usage: php lib/setup_admin.php
 */

require_once __DIR__ . '/db.php';

echo "=================================\n";
echo "  LawConnect Admin Setup Helper\n";
echo "=================================\n\n";

// Check if already has admin
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = " . ROLE_ADMIN);
$adminCount = $stmt->fetch()['count'];

if ($adminCount > 0) {
    echo "✓ Admin account(s) already exist in database.\n";
    echo "Current admin count: " . $adminCount . "\n\n";
} else {
    echo "ℹ No admin accounts found. Creating default admin...\n\n";
    
    $adminName = "System Administrator";
    $adminEmail = "admin@lawconnect.in";
    $adminPassword = "admin123";
    $adminPhone = "9876543210";
    
    $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, password, phone, role, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$adminName, $adminEmail, $passwordHash, $adminPhone, ROLE_ADMIN, 'active']);
        
        echo "✓ Admin account created successfully!\n\n";
        echo "Login Credentials:\n";
        echo "  Email: " . $adminEmail . "\n";
        echo "  Password: " . $adminPassword . "\n\n";
    } catch (Exception $e) {
        echo "✗ Error creating admin: " . $e->getMessage() . "\n";
    }
}

// Check database structure
echo "Checking database tables...\n\n";

$tables = [
    'users' => 'User accounts',
    'expert_profiles' => 'Expert profiles',
    'consultation_sessions' => 'Consultation bookings',
    'content_reports' => 'Content reports',
    'data_records' => 'User-generated content'
];

foreach ($tables as $table => $description) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($stmt->rowCount() > 0) {
        echo "✓ $table ($description)\n";
    } else {
        echo "✗ $table ($description) - MISSING\n";
    }
}

echo "\n=================================\n";
echo "  Setup Complete!\n";
echo "=================================\n\n";
echo "Next steps:\n";
echo "1. Go to: http://localhost/law/admin/\n";
echo "2. Or login at: http://localhost/law/login.php\n";
echo "3. Use the credentials above\n";
echo "4. You'll be redirected to admin dashboard\n\n";
