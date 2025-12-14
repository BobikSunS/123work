<?php
// Test OSRM connection
header('Content-Type: application/json');

// Test coordinates - two offices in Belarus
$lat1 = 53.904133; // Minsk
$lng1 = 27.557541;
$lat2 = 53.688751; // Gomel
$lng2 = 27.244818;

echo "Testing OSRM connection...\n";

$osrm_url = "https://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}?overview=full&geometries=polyline&steps=false";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $osrm_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['error' => 'Failed to connect to OSRM: ' . $error]);
    exit;
}

if ($http_code !== 200) {
    echo json_encode(['error' => 'OSRM returned HTTP code: ' . $http_code, 'response' => $response]);
    exit;
}

$route_data = json_decode($response, true);

if (!$route_data || !isset($route_data['routes']) || empty($route_data['routes'])) {
    echo json_encode(['error' => 'No route found', 'response' => $route_data]);
    exit;
}

$route = $route_data['routes'][0];
$distance_km = $route['distance'] / 1000; // Convert meters to kilometers
$duration_min = (int)($route['duration'] / 60); // Convert seconds to minutes
$geometry = $route['geometry'] ?? null;

echo json_encode([
    'success' => true,
    'distance' => $distance_km,
    'duration' => $duration_min,
    'route_data' => $geometry,
    'format' => gettype($geometry),
    'is_string' => is_string($geometry)
], JSON_PRETTY_PRINT);
?>