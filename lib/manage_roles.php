<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    die("Database connection failed");
}

try {
    echo "=== User Role Management (Numeric Roles) ===\n\n";
    
    // 1. Update existing expert profiles - set role to 2 (expert)
    echo "1. Updating roles for users with expert profiles...\n";
    $stmt = $pdo->prepare("
        UPDATE users u
        INNER JOIN expert_profiles ep ON u.id = ep.user_id
        SET u.role = 2
        WHERE u.role != 2
    ");
    $stmt->execute();
    $updatedCount = $stmt->rowCount();
    echo "   ✓ Updated $updatedCount users to role=2 (expert)\n\n";
    
    // 2. Set default role for users without specific role
    echo "2. Ensuring default role for non-expert users...\n";
    $stmt = $pdo->prepare("
        UPDATE users 
        SET role = 1
        WHERE role IS NULL OR role = '1' OR role = '0' OR role = 'user'
    ");
    $stmt->execute();
    $updatedCount = $stmt->rowCount();
    echo "   ✓ Updated $updatedCount users to role=1 (student)\n\n";
    
    // 3. Show current role distribution
    echo "3. Current role distribution:\n";
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role ORDER BY role");
    $results = $stmt->fetchAll();
    $roleNames = [1 => 'Student', 2 => 'Expert', 3 => 'Admin'];
    foreach ($results as $row) {
        $roleName = $roleNames[$row['role']] ?? 'Unknown';
        echo "   • Role {$row['role']} ($roleName): {$row['count']} users\n";
    }
    
    // 4. List all expert users
    echo "\n4. Expert Users (Role = 2):\n";
    $stmt = $pdo->query("
        SELECT u.id, u.email, u.full_name, u.role, ep.specialization, ep.hourly_rate
        FROM users u
        LEFT JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE u.role = 2
        ORDER BY u.id
    ");
    $experts = $stmt->fetchAll();
    if ($experts) {
        foreach ($experts as $expert) {
            echo "   • {$expert['full_name']} ({$expert['email']}) - {$expert['specialization']} @ ₹{$expert['hourly_rate']}\n";
        }
    } else {
        echo "   → No expert users found\n";
    }
    
    // 5. Show redirect information
    echo "\n5. Login Redirect Rules:\n";
    echo "   • Role 1 (Student)  → student/mainhome.php\n";
    echo "   • Role 2 (Expert)   → expert/newpage.php\n";
    echo "   • Role 3 (Admin)    → admin/1newpage.php\n";
    
    echo "\n✓ Role management completed!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>

