<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

$status = isset($_GET['status']) ? $_GET['status'] : 'all';

try {
    $conn = getDBConnection();
    
    $query = "
        SELECT 
            d.*,
            u.full_name as user_name,
            u.email as user_email,
            cs.id as session_id,
            cs.session_date,
            cs.amount as session_amount,
            e.full_name as expert_name
        FROM disputes d
        INNER JOIN users u ON d.user_id = u.id
        LEFT JOIN consultation_sessions cs ON d.session_id = cs.id
        LEFT JOIN users e ON cs.expert_id = e.id
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    if ($status !== 'all') {
        $query .= " AND d.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $query .= " ORDER BY d.created_at DESC";
    
    if (empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }
    
    $result = $stmt->get_result();
    
    $disputes = [];
    while ($row = $result->fetch_assoc()) {
        $disputes[] = $row;
    }
    
    // Get stats
    $stmt = $conn->prepare("
        SELECT 
            status,
            COUNT(*) as count,
            SUM(refund_amount) as total_refund
        FROM disputes
        GROUP BY status
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stats = [];
    while ($row = $result->fetch_assoc()) {
        $stats[] = $row;
    }
    
    sendSuccessResponse('Disputes retrieved', [
        'disputes' => $disputes,
        'count' => count($disputes),
        'stats' => $stats
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching disputes: ' . $e->getMessage());
}
