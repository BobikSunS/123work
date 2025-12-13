<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST method allowed']);
    exit;
}

$carrier_id = (int)($_POST['carrier_id'] ?? 0);
$from_office = (int)($_POST['from_office'] ?? 0);
$to_office = (int)($_POST['to_office'] ?? 0);
$weight = floatval($_POST['weight'] ?? 0);
$package_type = $_POST['package_type'] ?? 'parcel';
$insurance = isset($_POST['insurance']) && $_POST['insurance'] == 1;
$letter_count = (int)($_POST['letter_count'] ?? 1);

// Validate required fields
if ($carrier_id <= 0 || $from_office <= 0 || $to_office <= 0) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Get carrier info
$carrier = $db->prepare("SELECT * FROM carriers WHERE id = ?");
$carrier->execute([$carrier_id]);
$carrier = $carrier->fetch();

if (!$carrier) {
    echo json_encode(['error' => 'Invalid carrier']);
    exit;
}

// Get route distance
$routeData = $db->prepare("SELECT distance_km FROM calculated_routes WHERE from_office_id = ? AND to_office_id = ?");
$routeData->execute([$from_office, $to_office]);
$routeData = $routeData->fetch();

if (!$routeData) {
    // Calculate approximate distance if route not found
    $offices = $db->prepare("SELECT lat, lng FROM offices WHERE id IN (?, ?)");
    $offices->execute([$from_office, $to_office]);
    $office_rows = $offices->fetchAll();
    
    if (count($office_rows) < 2) {
        echo json_encode(['error' => 'Invalid office IDs']);
        exit;
    }
    
    $from_office_data = $office_rows[0];
    $to_office_data = $office_rows[1];
    
    // Calculate approximate distance using Haversine formula
    $lat1 = deg2rad($from_office_data['lat']);
    $lon1 = deg2rad($from_office_data['lng']);
    $lat2 = deg2rad($to_office_data['lat']);
    $lon2 = deg2rad($to_office_data['lng']);
    
    $delta_lat = $lat2 - $lat1;
    $delta_lon = $lon2 - $lon1;
    
    $a = sin($delta_lat / 2) * sin($delta_lat / 2) + 
         cos($lat1) * cos($lat2) * 
         sin($delta_lon / 2) * sin($delta_lon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    $distance = 6371 * $c; // Earth's radius in km
} else {
    $distance = floatval($routeData['distance_km']);
}

// Adjust weight for letters
if ($package_type === 'letter') {
    $weight = $letter_count * 0.02; // weight per letter is 0.02 kg
}

// Check weight limit
if ($weight > $carrier['max_weight']) {
    echo json_encode(['error' => 'Weight exceeds carrier limit']);
    exit;
}

// Calculate cost
$cost = $carrier['base_cost'] + 
        $weight * $carrier['cost_per_kg'] + 
        $distance * $carrier['cost_per_km'];

// Apply insurance
if ($insurance) {
    $cost *= 1.02;
}

// Apply minimum cost for letters
if ($package_type === 'letter') {
    $cost = max($cost, 2.5);
}

$cost = round($cost, 2);

echo json_encode([
    'success' => true,
    'cost' => $cost,
    'distance' => $distance
]);
?>