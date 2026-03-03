<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$user = requireAuth();

if ($user['role'] !== 'admin') {
    sendErrorResponse('Access denied. Admins only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['report_id']) || !isset($input['action'])) {
    sendErrorResponse('Report ID and action (approve/delete) are required');
}

$report_id = intval($input['report_id']);
$action = $input['action'];
$admin_notes = isset($input['notes']) ? trim($input['notes']) : '';

if (!in_array($action, ['approve', 'delete'])) {
    sendErrorResponse('Action must be "approve" or "delete"');
}

try {
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Get report details
    $stmt = $conn->prepare("SELECT * FROM content_reports WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();
    
    if (!$report) {
        $conn->rollback();
        sendErrorResponse('Report not found');
    }
    
    if ($action === 'delete') {
        // Delete the reported content
        if ($report['content_type'] === 'question') {
            $stmt = $conn->prepare("UPDATE forum_questions SET status = 'closed' WHERE id = ?");
            $stmt->bind_param("i", $report['content_id']);
            $stmt->execute();
        } elseif ($report['content_type'] === 'answer') {
            $stmt = $conn->prepare("DELETE FROM forum_answers WHERE id = ?");
            $stmt->bind_param("i", $report['content_id']);
            $stmt->execute();
        }
        
        $new_status = 'resolved';
        $resolution = 'Content deleted';
    } else {
        $new_status = 'dismissed';
        $resolution = 'Content approved - no violation found';
    }
    
    // Update report
    $stmt = $conn->prepare("
        UPDATE content_reports 
        SET status = ?, resolution = ?, admin_notes = ?, resolved_at = NOW(), resolved_by = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssii", $new_status, $resolution, $admin_notes, $user['id'], $report_id);
    $stmt->execute();
    
    $conn->commit();
    
    sendSuccessResponse('Content moderated', [
        'report_id' => $report_id,
        'action' => $action,
        'status' => $new_status
    ]);
} catch (Exception $e) {
    $conn->rollback();
    sendErrorResponse('Error moderating content: ' . $e->getMessage());
}
