<?php
// Database connection configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'law');
define('DB_USER', 'law');
define('DB_PASS', 'law');

// Role Constants (Numeric)
define('ROLE_STUDENT', 1);  // Student/User (default)
define('ROLE_EXPERT', 2);   // Legal Expert
define('ROLE_ADMIN', 3);    // Administrator

// Create database connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        return null;
    }
}

// Start session if not already started
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current logged in user
function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, full_name, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        } else {
            // Regular request
            header('Location: index.php');
            exit;
        }
    }
    return getCurrentUser();
}

// Require specific role
function requireRole($requiredRole) {
    $user = requireAuth();
    $userRole = (int)($user['role'] ?? ROLE_STUDENT);
    
    if ($userRole !== $requiredRole) {
        http_response_code(403);
        die('Access Denied: Insufficient Permissions');
    }
    
    return $user;
}

// Get human-readable role name
function getRoleName($role) {
    $role = (int)$role;
    $roles = [
        ROLE_STUDENT => 'Student',
        ROLE_EXPERT => 'Expert',
        ROLE_ADMIN => 'Administrator'
    ];
    return $roles[$role] ?? 'Unknown';
}

// Get redirect URL based on role
function getRedirectUrlByRole($role) {
    $role = (int)$role;
    switch ($role) {
        case ROLE_EXPERT:
            return 'expert/newpage.php';
        case ROLE_ADMIN:
            return 'admin/1newpage.php';
        case ROLE_STUDENT:
        default:
            return 'student/mainhome.php';
    }
}

// Set JSON header
function setJsonHeader() {
    header('Content-Type: application/json');
}

// Send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    setJsonHeader();
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Send error response
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse(['error' => $message], $statusCode);
}

// Send success response
function sendSuccessResponse($data = null, $message = 'Success') {
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    sendJsonResponse($response, 200);
}
?>
