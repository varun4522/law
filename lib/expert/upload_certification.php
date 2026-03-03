<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
if ($user['role'] !== 'expert') { sendErrorResponse('Experts only', 403); }
if (!isset($_FILES['certificate'])) { sendErrorResponse('No file uploaded'); }
$file = $_FILES['certificate'];
$allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
if (!in_array($file['type'], $allowed_types)) { sendErrorResponse('Invalid file type. PDF, JPG, PNG only'); }
if ($file['size'] > 5 * 1024 * 1024) { sendErrorResponse('File too large. Max 5MB'); }
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'cert_' . $user['id'] . '_' . time() . '.' . $ext;
$upload_dir = '../../uploads/certifications/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
$filepath = $upload_dir . $filename;
if (!move_uploaded_file($file['tmp_name'], $filepath)) { sendErrorResponse('Upload failed'); }
try {
    $pdo = getDBConnection();
    $title = $_POST['title'] ?? $filename;
    $pdo->prepare("INSERT INTO expert_certifications (expert_id, title, file_path, issued_by, issue_date) VALUES (?,?,?,?,?)")
        ->execute([$user['id'], $title, 'uploads/certifications/'.$filename, $_POST['issued_by'] ?? '', $_POST['issue_date'] ?? null]);
    sendSuccessResponse('Certificate uploaded', ['id' => $pdo->lastInsertId(), 'file_path' => 'uploads/certifications/'.$filename]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
