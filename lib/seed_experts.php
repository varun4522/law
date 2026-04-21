<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
if (!$pdo) {
    die("Database connection failed");
}

// First, get or create expert users
$expertData = [
    [
        'email' => 'priya.sharma@lawconnectors.in',
        'full_name' => 'Adv. Priya Sharma',
        'name' => 'Adv. Priya Sharma',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Senior advocate with 12 years specializing in family matters and matrimonial disputes. Expertise in divorce, child custody, inheritance and matrimonial disputes.',
        'phone' => '9876543210',
        'specialization' => 'Family Law',
        'experience_years' => 12,
        'hourly_rate' => 800,
        'availability_status' => 'available',
        'probono_participation' => 1,
        'rating' => 4.9,
        'total_reviews' => 214,
        'total_sessions' => 430
    ],
    [
        'email' => 'rahul.verma@lawconnectors.in',
        'full_name' => 'Adv. Rahul Verma',
        'name' => 'Adv. Rahul Verma',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Former public prosecutor turned defense attorney. Expert in criminal trial procedures, bail applications, and FIR quashing.',
        'phone' => '9876543211',
        'specialization' => 'Criminal Law',
        'experience_years' => 8,
        'hourly_rate' => 1200,
        'availability_status' => 'available',
        'probono_participation' => 0,
        'rating' => 4.7,
        'total_reviews' => 132,
        'total_sessions' => 289
    ],
    [
        'email' => 'anjali.nair@lawconnectors.in',
        'full_name' => 'Adv. Anjali Nair',
        'name' => 'Adv. Anjali Nair',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Specialist in real estate law including RERA disputes and property documentation. Expert in property disputes and title verification.',
        'phone' => '9876543212',
        'specialization' => 'Property Law',
        'experience_years' => 15,
        'hourly_rate' => 900,
        'availability_status' => 'busy',
        'probono_participation' => 0,
        'rating' => 4.8,
        'total_reviews' => 298,
        'total_sessions' => 612
    ],
    [
        'email' => 'suresh.patel@lawconnectors.in',
        'full_name' => 'Adv. Suresh Patel',
        'name' => 'Adv. Suresh Patel',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Corporate lawyer helping startups and established companies with legal structures, contracts, and IP rights.',
        'phone' => '9876543213',
        'specialization' => 'Corporate Law',
        'experience_years' => 10,
        'hourly_rate' => 1500,
        'availability_status' => 'available',
        'probono_participation' => 0,
        'rating' => 4.6,
        'total_reviews' => 87,
        'total_sessions' => 194
    ],
    [
        'email' => 'meera.joshi@lawconnectors.in',
        'full_name' => 'Adv. Meera Joshi',
        'name' => 'Adv. Meera Joshi',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Champion of consumer rights with high success rate at National Consumer Forum. Expert in consumer disputes and RERA cases.',
        'phone' => '9876543214',
        'specialization' => 'Consumer Law',
        'experience_years' => 6,
        'hourly_rate' => 600,
        'availability_status' => 'available',
        'probono_participation' => 1,
        'rating' => 4.8,
        'total_reviews' => 167,
        'total_sessions' => 321
    ],
    [
        'email' => 'karthik.rajan@lawconnectors.in',
        'full_name' => 'Adv. Karthik Rajan',
        'name' => 'Adv. Karthik Rajan',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Specializes in employment rights, labour disputes and industrial tribunal cases. Expert in PF, ESIC and wrongful termination.',
        'phone' => '9876543215',
        'specialization' => 'Labour Law',
        'experience_years' => 9,
        'hourly_rate' => 700,
        'availability_status' => 'busy',
        'probono_participation' => 0,
        'rating' => 4.5,
        'total_reviews' => 104,
        'total_sessions' => 215
    ],
    [
        'email' => 'deepa.choudhary@lawconnectors.in',
        'full_name' => 'Adv. Deepa Choudhary',
        'name' => 'Adv. Deepa Choudhary',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'One of the most experienced civil litigators. Has argued cases at High Court level. Expert in civil suits and injunctions.',
        'phone' => '9876543216',
        'specialization' => 'Civil Law',
        'experience_years' => 18,
        'hourly_rate' => 1100,
        'availability_status' => 'available',
        'probono_participation' => 1,
        'rating' => 4.9,
        'total_reviews' => 341,
        'total_sessions' => 820
    ],
    [
        'email' => 'arun.mishra@lawconnectors.in',
        'full_name' => 'Adv. Arun Mishra',
        'name' => 'Adv. Arun Mishra',
        'password' => password_hash('expert123', PASSWORD_BCRYPT),
        'role' => 'expert',
        'bio' => 'Young and dynamic criminal defence lawyer focused on digital and cyber crime cases. Expert in bail and POCSO cases.',
        'phone' => '9876543217',
        'specialization' => 'Criminal Law',
        'experience_years' => 5,
        'hourly_rate' => 650,
        'availability_status' => 'available',
        'probono_participation' => 1,
        'rating' => 4.4,
        'total_reviews' => 56,
        'total_sessions' => 98
    ]
];

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    foreach ($expertData as $expert) {
        // Check if user exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$expert['email']]);
        $user = $checkStmt->fetch();
        
        if (!$user) {
            // Create user if doesn't exist
            $userStmt = $pdo->prepare("
                INSERT INTO users (email, password, full_name, name, role, phone, bio, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $userStmt->execute([
                $expert['email'],
                $expert['password'],
                $expert['full_name'],
                $expert['name'],
                $expert['role'],
                $expert['phone'],
                $expert['bio']
            ]);
            $userId = $pdo->lastInsertId();
            echo "✓ Created user: {$expert['full_name']} (ID: $userId)\n";
        } else {
            $userId = $user['id'];
            echo "→ User exists: {$expert['full_name']} (ID: $userId)\n";
        }
        
        // Check if expert profile exists
        $profileStmt = $pdo->prepare("SELECT id FROM expert_profiles WHERE user_id = ?");
        $profileStmt->execute([$userId]);
        $profile = $profileStmt->fetch();
        
        if (!$profile) {
            // Create expert profile
            $profileInsertStmt = $pdo->prepare("
                INSERT INTO expert_profiles 
                (user_id, specialization, experience_years, hourly_rate, availability_status, 
                 probono_participation, rating, total_reviews, total_sessions, verification_status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'verified', NOW())
            ");
            $profileInsertStmt->execute([
                $userId,
                $expert['specialization'],
                $expert['experience_years'],
                $expert['hourly_rate'],
                $expert['availability_status'],
                $expert['probono_participation'],
                $expert['rating'],
                $expert['total_reviews'],
                $expert['total_sessions']
            ]);
            echo "  ✓ Created expert profile\n";
        } else {
            echo "  → Expert profile exists\n";
        }
    }
    
    $pdo->commit();
    echo "\n✓ Database populated successfully!\n";
    echo "Total experts added: " . count($expertData) . "\n";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
