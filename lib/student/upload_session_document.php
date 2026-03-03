<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if (!isset($_POST['session_id'])) { sendErrorResponse('session_id required'); }
$session_id = intval($_POST['session_id']);
if (!isset($_FILES['document'])) { sendErrorResponse('No file uploaded'); }
$allowed = ['application/pdf','image/jpeg','image/png','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$file = $_FILES['document'];
if (!in_array($file['type'], $allowed)) { sendErrorResponse('Invalid file type'); }
if ($file['size'] > 10*1024*1024) { sendErrorResponse('File too large (max 10MB)'); }
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM consultation_sessions WHERE id = ? AND (user_id = ? OR expert_id = ?)");
    $stmt->execute([$session_id, $user['id'], $user['id']]);
    if (!$stmt->fetch()) { sendErrorResponse('Session not found or access denied'); }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'doc_' . $session_id . '_' . $user['id'] . '_' . time() . '.' . $ext;
    $upload_dir = '../../uploads/session_documents/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) { sendErrorResponse('Upload failed'); }
    $relative_path = 'uploads/session_documents/' . $filename;
    $title = $_POST['title'] ?? $file['name'];
    $desc  = $_POST['description'] ?? '';
    $pdo->prepare("INSERT INTO session_documents (session_id, uploaded_by, document_title, document_description, file_path, file_size, file_type) VALUES (?,?,?,?,?,?,?)")
        ->execute([$session_id, $user['id'], $title, $desc, $relative_path, $file['size'], $ext]);
    sendSuccessResponse('Document uploaded', ['id' => $pdo->lastInsertId(), 'file_path' => $relative_path]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
