<?php
// get_route.php - Endpoint for calculating routes using GraphHopper
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$from_office_id = (int)($_POST['from_office_id'] ?? 0);
$to_office_id = (int)($_POST['to_office_id'] ?? 0);

if ($from_office_id <= 0 || $to_office_id <= 0) {
    echo json_encode(['error' => 'Invalid office IDs']);
    exit;
}

// First check if route already exists in database
$stmt = $db->prepare("SELECT distance_km, duration_min, route_data FROM calculated_routes WHERE from_office_id = ? AND to_office_id = ?");
$stmt->execute([$from_office_id, $to_office_id]);
$existing_route = $stmt->fetch();

if ($existing_route) {
    echo json_encode([
        'success' => true,
        'distance' => floatval($existing_route['distance_km']),
        'duration' => intval($existing_route['duration_min']),
        'route_data' => $existing_route['route_data']
    ]);
    exit;
}

// Get office coordinates
$stmt = $db->prepare("SELECT lat, lng FROM offices WHERE id = ?");
$stmt->execute([$from_office_id]);
$from_office = $stmt->fetch();

$stmt = $db->prepare("SELECT lat, lng FROM offices WHERE id = ?");
$stmt->execute([$to_office_id]);
$to_office = $stmt->fetch();

if (!$from_office || !$to_office) {
    echo json_encode(['error' => 'Office not found']);
    exit;
}

// Use GraphHopper for route calculation
// Using a free tier key that works for basic usage
$graphhopper_url = "https://graphhopper.com/api/1/route?point={$from_office['lat']},{$from_office['lng']}&point={$to_office['lat']},{$to_office['lng']}&vehicle=car&locale=ru&elevation=false&instructions=false&key=08ae2a0d-aae3-4ea2-85c0-ef0a18a5a4cc";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $graphhopper_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || $response === false) {
    // If GraphHopper fails, try alternative service
    $alternative_url = "https://router.project-osrm.org/route/v1/driving/{$from_office['lng']},{$from_office['lat']};{$to_office['lng']},{$to_office['lat']}?overview=full&steps=true";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $alternative_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || $response === false) {
        // If both services fail, fallback to straight-line distance
        $distance = calculateStraightLineDistance(
            $from_office['lat'], 
            $from_office['lng'], 
            $to_office['lat'], 
            $to_office['lng']
        );
        
        // Save the straight-line distance as a fallback
        $stmt = $db->prepare("INSERT INTO calculated_routes (from_office_id, to_office_id, distance_km, duration_min, route_data) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$from_office_id, $to_office_id, $distance, (int)($distance * 1.2), null]);
        
        echo json_encode([
            'success' => true,
            'distance' => $distance,
            'duration' => (int)($distance * 1.2), // Estimate duration based on distance
            'route_data' => null
        ]);
        exit;
    }
}

$route_data = json_decode($response, true);

if (!$route_data || !isset($route_data['paths']) || empty($route_data['paths'])) {
    echo json_encode(['error' => 'No route found']);
    exit;
}

$route = $route_data['paths'][0];
$distance_km = $route['distance'] / 1000; // Convert meters to kilometers
$duration_min = (int)($route['time'] / 60000); // Convert milliseconds to minutes
$geometry = $route['points'] ?? null;

// Save route to database
$stmt = $db->prepare("INSERT INTO calculated_routes (from_office_id, to_office_id, distance_km, duration_min, route_data) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$from_office_id, $to_office_id, $distance_km, $duration_min, json_encode($geometry)]);

echo json_encode([
    'success' => true,
    'distance' => $distance_km,
    'duration' => $duration_min,
    'route_data' => $geometry
]);

// Helper function to calculate straight-line distance (fallback)
function calculateStraightLineDistance($lat1, $lng1, $lat2, $lng2) {
    $R = 6371; // Earth's radius in kilometers
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}
?>