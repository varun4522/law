<?php
require_once '../db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendErrorResponse('POST required', 405); }
$user = requireAuth();
if ($user['role'] !== 'admin') { sendErrorResponse('Admin only', 403); }
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['rate'])) { sendErrorResponse('rate required'); }
$rate = floatval($input['rate']);
if ($rate < 0 || $rate > 100) { sendErrorResponse('Rate must be 0-100'); }
try {
    $pdo = getDBConnection();
    $pdo->prepare("INSERT INTO system_settings (`key`,`value`) VALUES ('commission_rate',?) ON DUPLICATE KEY UPDATE `value`=?")->execute([$rate, $rate]);
    sendSuccessResponse('Commission rate updated', ['rate' => $rate]);
} catch (Exception $e) { sendErrorResponse($e->getMessage()); }
