<?php
require_once '../db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$user = requireAuth();

// Get search parameters
$specialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';
$min_rating = isset($_GET['min_rating']) ? floatval($_GET['min_rating']) : 0;
$max_rate = isset($_GET['max_rate']) ? floatval($_GET['max_rate']) : 999999;
$min_rate = isset($_GET['min_rate']) ? floatval($_GET['min_rate']) : 0;
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';
$verified_only = isset($_GET['verified_only']) ? filter_var($_GET['verified_only'], FILTER_VALIDATE_BOOLEAN) : false;
$language = isset($_GET['language']) ? trim($_GET['language']) : '';
$min_experience = isset($_GET['min_experience']) ? intval($_GET['min_experience']) : 0;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'rating'; // rating, rate, experience, reviews
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

try {
    $conn = getDBConnection();
    
    $query = "
        SELECT 
            u.id,
            u.full_name,
            u.email,
            u.phone,
            u.profile_image,
            u.bio,
            ep.specialization,
            ep.experience_years,
            ep.language,
            ep.availability_status,
            ep.hourly_rate,
            ep.rating,
            ep.total_reviews,
            ep.total_sessions,
            ep.verification_status,
            ep.probono_participation
        FROM users u
        INNER JOIN expert_profiles ep ON u.id = ep.user_id
        WHERE u.role = 'expert'
    ";
    
    $params = [];
    $types = '';
    
    if ($specialization) {
        $query .= " AND ep.specialization LIKE ?";
        $params[] = "%$specialization%";
        $types .= 's';
    }
    
    if ($min_rating > 0) {
        $query .= " AND ep.rating >= ?";
        $params[] = $min_rating;
        $types .= 'd';
    }
    
    if ($max_rate < 999999) {
        $query .= " AND ep.hourly_rate <= ?";
        $params[] = $max_rate;
        $types .= 'd';
    }
    
    if ($min_rate > 0) {
        $query .= " AND ep.hourly_rate >= ?";
        $params[] = $min_rate;
        $types .= 'd';
    }
    
    if ($availability) {
        $query .= " AND ep.availability_status = ?";
        $params[] = $availability;
        $types .= 's';
    }
    
    if ($verified_only) {
        $query .= " AND ep.verification_status = 'verified'";
    }
    
    if ($language) {
        $query .= " AND ep.language LIKE ?";
        $params[] = "%$language%";
        $types .= 's';
    }
    
    if ($min_experience > 0) {
        $query .= " AND ep.experience_years >= ?";
        $params[] = $min_experience;
        $types .= 'i';
    }
    
    // Add sorting
    $allowed_sort = ['rating', 'hourly_rate', 'experience_years', 'total_reviews', 'total_sessions'];
    $sort_column = in_array($sort_by, $allowed_sort) ? $sort_by : 'rating';
    $sort_direction = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
    $query .= " ORDER BY ep.$sort_column $sort_direction";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $experts = [];
    while ($row = $result->fetch_assoc()) {
        $experts[] = $row;
    }
    
    sendSuccessResponse('Search results', [
        'experts' => $experts,
        'count' => count($experts),
        'filters_applied' => [
            'specialization' => $specialization,
            'min_rating' => $min_rating,
            'max_rate' => $max_rate,
            'min_rate' => $min_rate,
            'availability' => $availability,
            'verified_only' => $verified_only,
            'language' => $language,
            'min_experience' => $min_experience,
            'sort_by' => $sort_column,
            'sort_order' => $sort_direction
        ]
    ]);
} catch (Exception $e) {
    sendErrorResponse('Search error: ' . $e->getMessage());
}
