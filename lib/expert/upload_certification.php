<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

if ($user['role'] !== 'expert') {
    sendErrorResponse('Access denied. Experts only.', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Invalid request method', 405);
}

if (!isset($_FILES['certification'])) {
    sendErrorResponse('Certification file is required');
}

$cert_title = isset($_POST['title']) ? trim($_POST['title']) : '';
$cert_type = isset($_POST['type']) ? trim($_POST['type']) : 'other';
$cert_issuer = isset($_POST['issuer']) ? trim($_POST['issuer']) : '';
$cert_date = isset($_POST['issue_date']) ? $_POST['issue_date'] : date('Y-m-d');

try {
    $conn = getDBConnection();
    
    // Handle file upload
    $file = $_FILES['certification'];
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        sendErrorResponse('Invalid file type. Allowed: ' . implode(', ', $allowed_extensions));
    }
    
    if ($file['size'] > $max_size) {
        sendErrorResponse('File size exceeds 5MB limit');
    }
    
    // Create uploads directory
    $upload_dir = __DIR__ . '/../../uploads/certifications/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $unique_name = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $unique_name;
    
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        sendErrorResponse('Failed to upload file');
    }
    
    // Save to database
    $relative_path = 'uploads/certifications/' . $unique_name;
    $stmt = $conn->prepare("
        INSERT INTO expert_certifications (expert_id, title, type, issuer, issue_date, file_path, verification_status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("isssss", $user['id'], $cert_title, $cert_type, $cert_issuer, $cert_date, $relative_path);
    $stmt->execute();
    
    $cert_id = $conn->insert_id;
    
    sendSuccessResponse('Certification uploaded successfully', [
        'certification_id' => $cert_id,
        'file_path' => $relative_path,
        'status' => 'pending verification'
    ]);
} catch (Exception $e) {
    sendErrorResponse('Upload error: ' . $e->getMessage());
}
