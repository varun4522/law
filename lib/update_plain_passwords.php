<?php
/**
 * Setup Plain Password Authentication
 * This script:
 * 1. Adds plain_password column if it doesn't exist
 * 2. Inserts test users with plain passwords
 * 3. Drops the old encrypted password column (optional)
 * 
 * WARNING: This is for testing/development purposes ONLY
 * Never store plain passwords in production!
 */

require_once 'db.php';

$pdo = getDBConnection();
if (!$pdo) {
    die("❌ Database connection failed\n");
}

// Test user data with plain passwords
$testUsers = [
    ['email' => 'student@test.com', 'plain_password' => '1-Student1', 'full_name' => 'Test Student', 'role' => 1],
    ['email' => 'expert@test.com', 'plain_password' => '2-Expert2', 'full_name' => 'Test Expert', 'role' => 2],
    ['email' => 'admin@test.com', 'plain_password' => '3-Admin3', 'full_name' => 'Test Admin', 'role' => 3]
];

try {
    echo "🔧 Setting up plain password authentication...\n\n";
    
    // Check if plain_password column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'plain_password'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo "➕ Adding plain_password column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN plain_password VARCHAR(255) NULL AFTER password");
        echo "✅ Column added!\n\n";
    } else {
        echo "✓ plain_password column already exists\n\n";
    }
    
    // Insert or update test users
    foreach ($testUsers as $user) {
        // Check if user exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$user['email']]);
        $existingUser = $checkStmt->fetch();
        
        if ($existingUser) {
            // Update existing user
            $stmt = $pdo->prepare("UPDATE users SET plain_password = ? WHERE email = ?");
            $stmt->execute([$user['plain_password'], $user['email']]);
            echo "🔄 Updated: {$user['email']}\n";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("
                INSERT INTO users (email, plain_password, full_name, role, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $user['email'],
                $user['plain_password'],
                $user['full_name'],
                $user['role']
            ]);
            echo "✅ Created: {$user['email']}\n";
        }
    }
    
    echo "\n✅ Setup complete!\n";
    echo "\n🔑 Login Credentials:\n";
    echo "   Student: student@test.com / 1-Student1\n";
    echo "   Expert: expert@test.com / 2-Expert2\n";
    echo "   Admin: admin@test.com / 3-Admin3\n";
    
    // Show table structure
    echo "\n📋 Current Users Table Structure:\n";
    $result = $pdo->query("DESCRIBE users");
    $columns = $result->fetchAll();
    foreach ($columns as $col) {
        echo "   - {$col['Field']}: {$col['Type']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>

