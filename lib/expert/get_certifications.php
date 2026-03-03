<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT * FROM expert_certifications 
        WHERE expert_id = ?
        ORDER BY issue_date DESC
    ");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $certifications = [];
    while ($row = $result->fetch_assoc()) {
        $certifications[] = $row;
    }
    
    sendSuccessResponse('Certifications retrieved', [
        'certifications' => $certifications,
        'count' => count($certifications),
        'verified_count' => count(array_filter($certifications, function($c) { return $c['verification_status'] === 'verified'; }))
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching certifications: ' . $e->getMessage());
}
