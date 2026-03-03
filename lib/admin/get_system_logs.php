<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

try {
    $conn = getDBConnection();
    
    $query = "SELECT * FROM system_logs";
    $params = [];
    $types = '';
    
    if ($action) {
        $query .= " WHERE action = ?";
        $params[] = $action;
        $types .= 's';
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM system_logs";
    if ($action) {
        $count_query .= " WHERE action = ?";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param("s", $action);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare($count_query);
        $stmt->execute();
    }
    $total = $stmt->get_result()->fetch_assoc()['total'];
    
    // Get action types
    $stmt = $conn->prepare("
        SELECT action, COUNT(*) as count 
        FROM system_logs 
        GROUP BY action 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $action_stats = [];
    while ($row = $result->fetch_assoc()) {
        $action_stats[] = $row;
    }
    
    sendSuccessResponse('System logs retrieved', [
        'logs' => $logs,
        'count' => count($logs),
        'total_records' => $total,
        'action_stats' => $action_stats,
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + count($logs)) < $total
        ]
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching logs: ' . $e->getMessage());
}
