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
            cr.*,
            u.full_name as reporter_name,
            u.email as reporter_email,
            fq.title as question_title,
            fa.answer as answer_content,
            au.full_name as author_name
        FROM content_reports cr
        LEFT JOIN users u ON cr.reported_by = u.id
        LEFT JOIN forum_questions fq ON cr.content_type = 'question' AND cr.content_id = fq.id
        LEFT JOIN forum_answers fa ON cr.content_type = 'answer' AND cr.content_id = fa.id
        LEFT JOIN users au ON (
            (cr.content_type = 'question' AND fq.user_id = au.id) OR
            (cr.content_type = 'answer' AND fa.user_id = au.id)
        )
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    if ($status !== 'all') {
        $query .= " AND cr.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $query .= " ORDER BY cr.created_at DESC";
    
    if (empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }
    
    $result = $stmt->get_result();
    
    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    
    // Get stats
    $stmt = $conn->prepare("
        SELECT 
            status,
            COUNT(*) as count
        FROM content_reports
        GROUP BY status
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stats = [];
    while ($row = $result->fetch_assoc()) {
        $stats[$row['status']] = $row['count'];
    }
    
    sendSuccessResponse('Content reports retrieved', [
        'reports' => $reports,
        'count' => count($reports),
        'stats' => $stats
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error fetching reports: ' . $e->getMessage());
}
