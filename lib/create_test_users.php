<?php
/**
 * Generate Test Users with Hashed Passwords
 * Run this once to insert test users into the database
 * 
 * Test Accounts:
 * Student: email: student@test.com, password: 1-Student1
 * Expert: email: expert@test.com, password: 2-Expert2
 * Admin: email: admin@test.com, password: 3-Admin3
 */

require_once 'db.php';

$pdo = getDBConnection();
if (!$pdo) {
    die("Database connection failed\n");
}

// Test user data
$testUsers = [
    [
        'email' => 'student@test.com',
        'password' => '1-Student1',
        'full_name' => 'Test Student',
        'role' => 1
    ],
    [
        'email' => 'expert@test.com',
        'password' => '2-Expert2',
        'full_name' => 'Test Expert',
        'role' => 2
    ],
    [
        'email' => 'admin@test.com',
        'password' => '3-Admin3',
        'full_name' => 'Test Admin',
        'role' => 3
    ]
];

try {
    foreach ($testUsers as $user) {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$user['email']]);
        
        if ($stmt->fetch()) {
            echo "⚠️  User {$user['email']} already exists, skipping...\n";
            continue;
        }
        
        // Hash password securely
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, full_name, role, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $user['email'],
            $hashedPassword,
            $user['full_name'],
            $user['role']
        ]);
        
        $roleNames = [1 => 'Student', 2 => 'Expert', 3 => 'Admin'];
        echo "✅ Created {$roleNames[$user['role']]}: {$user['email']}\n";
        echo "   Password: {$user['password']}\n";
    }
    
    echo "\n✅ All test users created successfully!\n";
    echo "\nYou can now login with:\n";
    echo "  - Student: student@test.com / 1-Student1\n";
    echo "  - Expert: expert@test.com / 2-Expert2\n";
    echo "  - Admin: admin@test.com / 3-Admin3\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
