<?php
require_once '../db.php';
header('Content-Type: application/json');
$user = requireAuth();
$specialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';
$min_rating    = isset($_GET['min_rating']) ? floatval($_GET['min_rating']) : 0;
$max_rate      = isset($_GET['max_rate']) ? floatval($_GET['max_rate']) : 999999;
$min_rate      = isset($_GET['min_rate']) ? floatval($_GET['min_rate']) : 0;
$availability  = isset($_GET['availability']) ? $_GET['availability'] : '';
$verified_only = isset($_GET['verified_only']) ? filter_var($_GET['verified_only'], FILTER_VALIDATE_BOOLEAN) : false;
$language      = isset($_GET['language']) ? trim($_GET['language']) : '';
$min_exp       = isset($_GET['min_experience']) ? intval($_GET['min_experience']) : 0;
$sort_by       = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'rating';
$sort_order    = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
try {
    $pdo = getDBConnection();
    $query  = "SELECT u.id, u.full_name, u.email, u.profile_image, u.bio,
                      ep.specialization, ep.experience_years, ep.language,
                      ep.availability_status, ep.hourly_rate, ep.rating,
                      ep.total_reviews, ep.total_sessions, ep.verification_status, ep.probono_participation
               FROM users u INNER JOIN expert_profiles ep ON u.id = ep.user_id WHERE u.role = 'expert'";
    $params = [];
    if ($specialization) { $query .= " AND ep.specialization LIKE ?"; $params[] = "%$specialization%"; }
    if ($min_rating > 0)  { $query .= " AND ep.rating >= ?";           $params[] = $min_rating; }
    if ($max_rate < 999999){ $query .= " AND ep.hourly_rate <= ?";     $params[] = $max_rate; }
    if ($min_rate > 0)    { $query .= " AND ep.hourly_rate >= ?";      $params[] = $min_rate; }
    if ($availability)    { $query .= " AND ep.availability_status = ?"; $params[] = $availability; }
    if ($verified_only)   { $query .= " AND ep.verification_status = 'verified'"; }
    if ($language)        { $query .= " AND ep.language LIKE ?";       $params[] = "%$language%"; }
    if ($min_exp > 0)     { $query .= " AND ep.experience_years >= ?"; $params[] = $min_exp; }
    $allowed = ['rating','hourly_rate','experience_years','total_reviews','total_sessions'];
    $col = in_array($sort_by, $allowed) ? $sort_by : 'rating';
    $query .= " ORDER BY ep.$col $sort_order";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $experts = $stmt->fetchAll();
    sendSuccessResponse('Search results', ['experts' => $experts, 'count' => count($experts)]);
} catch (Exception $e) { sendErrorResponse('Search error: ' . $e->getMessage()); }
