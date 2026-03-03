<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$expert_id = isset($_GET['expert_id']) ? intval($_GET['expert_id']) : 0;
$min_amount = isset($_GET['min_amount']) ? floatval($_GET['min_amount']) : 0;
$max_amount = isset($_GET['max_amount']) ? floatval($_GET['max_amount']) : 999999;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    $conn = getDBConnection();
    
    $query = "
        SELECT 
            cs.*,
            expert.full_name as expert_name,
            expert.email as expert_email,
            expert.profile_image as expert_image,
            ep.specialization,
            ep.rating as expert_rating,
            (SELECT COUNT(*) FROM consultation_sessions WHERE user_id = cs.user_id AND expert_id = cs.expert_id AND status = 'completed') as sessions_with_expert
        FROM consultation_sessions cs
        INNER JOIN users expert ON cs.expert_id = expert.id
        LEFT JOIN expert_profiles ep ON expert.id = ep.user_id
        WHERE cs.user_id = ?
    ";
    
    $params = [$user['id']];
    $types = 'i';
    
    if ($status !== 'all') {
        $query .= " AND cs.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if ($date_from) {
        $query .= " AND cs.session_date >= ?";
        $params[] = $date_from;
        $types .= 's';
    }
    
    if ($date_to) {
        $query .= " AND cs.session_date <= ?";
        $params[] = $date_to;
        $types .= 's';
    }
    
    if ($expert_id > 0) {
        $query .= " AND cs.expert_id = ?";
        $params[] = $expert_id;
        $types .= 'i';
    }
    
    if ($min_amount > 0) {
        $query .= " AND cs.amount >= ?";
        $params[] = $min_amount;
        $types .= 'd';
    }
    
    if ($max_amount < 999999) {
        $query .= " AND cs.amount <= ?";
        $params[] = $max_amount;
        $types .= 'd';
    }
    
    $query .= " ORDER BY cs.session_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sessions = [];
    $total_spent = 0;
    
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
        if (in_array($row['status'], ['completed', 'confirmed'])) {
            $total_spent += floatval($row['amount']);
        }
    }
    
    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM consultation_sessions WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param("i", $user['id']);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
    
    sendSuccessResponse('Session history retrieved', [
        'sessions' => $sessions,
        'count' => count($sessions),
        'total_records' => $total_records,
        'total_spent' => $total_spent,
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + count($sessions)) < $total_records
        ]
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching history: ' . $e->getMessage());
}
