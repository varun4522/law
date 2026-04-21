<?php
require_once __DIR__ . '/../lib/db.php';

// If already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    $user = getCurrentUser();
    if ($user['role'] == ROLE_ADMIN) {
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: ../index.php');
        exit;
    }
}

// If not logged in, redirect to login
header('Location: ../login.php?redirect=admin');
exit;
