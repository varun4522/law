<?php
require_once __DIR__ . '/db.php';
session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['expert_id']) || !isset($data['session_date']) || !isset($data['duration'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

try {
    // Validate expert exists
    $stmt = $pdo->prepare("SELECT id FROM expert_profiles WHERE id = ?");
    $stmt->execute([$data['expert_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Expert not found');
    }
    
    // Insert consultation session
    $stmt = $pdo->prepare("
        INSERT INTO consultation_sessions 
        (user_id, expert_id, session_date, duration, session_type, status, notes, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $data['expert_id'],
        $data['session_date'],
        $data['duration'] ?? 60,
        'consultation',
        'pending',
        $data['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking request sent successfully',
        'session_id' => $pdo->lastInsertId()
    ]);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
