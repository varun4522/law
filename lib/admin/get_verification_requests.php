<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.phone,
            u.profile_image,
            u.created_at as registered_at,
            ep.*,
            (SELECT COUNT(*) FROM consultation_sessions WHERE expert_id = u.id) as total_sessions,
            (SELECT COUNT(*) FROM expert_certifications WHERE expert_id = u.id) as total_certifications
        FROM users u
        INNER JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE u.role = 'expert'
        AND ep.verification_status = ?
        ORDER BY u.created_at DESC
    ");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $experts = [];
    while ($row = $result->fetch_assoc()) {
        $experts[] = $row;
    }
    
    // Get verification stats
    $stmt = $conn->prepare("
        SELECT 
            verification_status,
            COUNT(*) as count
        FROM expert_profiles
        GROUP BY verification_status
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stats = [];
    while ($row = $result->fetch_assoc()) {
        $stats[$row['verification_status']] = $row['count'];
    }
    
    sendSuccessResponse('Verification requests retrieved', [
        'experts' => $experts,
        'count' => count($experts),
        'stats' => $stats
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching requests: ' . $e->getMessage());
}
