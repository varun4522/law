<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

if (!isset($_POST['session_id']) || !isset($_FILES['document'])) {
    sendErrorResponse('Session ID and document file are required');
}

$session_id = intval($_POST['session_id']);
$document_title = isset($_POST['title']) ? trim($_POST['title']) : '';
$document_description = isset($_POST['description']) ? trim($_POST['description']) : '';

try {
    $conn = getDBConnection();
    
    // Verify session belongs to user
    $stmt = $conn->prepare("SELECT id FROM consultation_sessions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $session_id, $user['id']);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        sendErrorResponse('Session not found or access denied');
    }
    
    // Handle file upload
    $file = $_FILES['document'];
    $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    $max_size = 10 * 1024 * 1024; // 10MB
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        sendErrorResponse('Invalid file type. Allowed: ' . implode(', ', $allowed_extensions));
    }
    
    if ($file['size'] > $max_size) {
        sendErrorResponse('File size exceeds 10MB limit');
    }
    
    // Create uploads directory if not exists
    $upload_dir = __DIR__ . '/../../uploads/session_documents/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $unique_name;
    
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        sendErrorResponse('Failed to upload file');
    }
    
    // Save to database
    $relative_path = 'uploads/session_documents/' . $unique_name;
    $stmt = $conn->prepare("
        INSERT INTO session_documents (session_id, uploaded_by, title, description, file_path, file_size, file_type)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssi", $session_id, $user['id'], $document_title, $document_description, $relative_path, $file['size'], $file_extension);
    $stmt->execute();
    
    $document_id = $conn->insert_id;
    
    sendSuccessResponse('Document uploaded successfully', [
        'document_id' => $document_id,
        'file_name' => $unique_name,
        'file_path' => $relative_path,
        'file_size' => $file['size']
    ]);
} catch (Exception $e) {
    sendErrorResponse('Upload error: ' . $e->getMessage());
}
