<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    die("Database connection failed");
}

try {
    echo "=== Converting Roles to Numeric Format ===\n\n";
    
    // 1. Convert string roles to numeric
    echo "1. Converting text roles to numeric...\n";
    
    $conversions = [
        'user' => 1,
        'expert' => 2,
        'admin' => 3
    ];
    
    foreach ($conversions as $textRole => $numRole) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE role = ?");
        $stmt->execute([$numRole, $textRole]);
        $count = $stmt->rowCount();
        if ($count > 0) {
            echo "   ✓ Converted $count users from '$textRole' to '$numRole'\n";
        }
    }
    
    // 2. Convert numeric strings to actual numbers
    echo "\n2. Converting string numbers to numeric type...\n";
    $stmt = $pdo->prepare("UPDATE users SET role = CAST(role AS UNSIGNED) WHERE role IN ('1', '2', '3')");
    $stmt->execute();
    
    // 3. Set default role for any NULL values
    echo "\n3. Setting default role for NULL values...\n";
    $stmt = $pdo->prepare("UPDATE users SET role = 1 WHERE role IS NULL");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count > 0) {
        echo "   ✓ Set $count users to default role (1 = student)\n";
    }
    
    // 4. Show current role distribution
    echo "\n4. Current role distribution:\n";
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role ORDER BY role");
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        $roleNames = [1 => 'Student', 2 => 'Expert', 3 => 'Admin'];
        $roleName = $roleNames[$row['role']] ?? 'Unknown';
        echo "   • Role {$row['role']} ($roleName): {$row['count']} users\n";
    }
    
    // 5. List users by role
    echo "\n5. Users by Role:\n";
    echo "\n   EXPERTS (Role 2):\n";
    $stmt = $pdo->query("SELECT id, email, full_name FROM users WHERE role = 2 ORDER BY id");
    $experts = $stmt->fetchAll();
    if ($experts) {
        foreach ($experts as $expert) {
            echo "   • {$expert['full_name']} ({$expert['email']})\n";
        }
    } else {
        echo "   → No experts found\n";
    }
    
    echo "\n   ADMINS (Role 3):\n";
    $stmt = $pdo->query("SELECT id, email, full_name FROM users WHERE role = 3 ORDER BY id");
    $admins = $stmt->fetchAll();
    if ($admins) {
        foreach ($admins as $admin) {
            echo "   • {$admin['full_name']} ({$admin['email']})\n";
        }
    } else {
        echo "   → No admins found\n";
    }
    
    echo "\n   STUDENTS (Role 1):\n";
    $stmt = $pdo->query("SELECT id, email, full_name FROM users WHERE role = 1 ORDER BY id");
    $students = $stmt->fetchAll();
    if (count($students) <= 5) {
        foreach ($students as $student) {
            echo "   • {$student['full_name']} ({$student['email']})\n";
        }
    } else {
        echo "   • (Total: " . count($students) . " students)\n";
    }
    
    echo "\n✓ Role conversion completed successfully!\n";
    echo "\nRole Reference:\n";
    echo "   1 = Student (default) → student/mainhome.php\n";
    echo "   2 = Expert           → expert/newpage.php\n";
    echo "   3 = Admin            → admin/1newpage.php\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
