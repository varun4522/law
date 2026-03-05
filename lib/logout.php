<?php
require_once 'db.php';

header('Content-Type: application/json');

startSession();

// Destroy the session
$_SESSION = [];
session_destroy();

sendSuccessResponse(null, 'Logged out successfully');
?>
