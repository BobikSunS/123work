<?php
require_once 'db.php';

header('Content-Type: application/json');

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

$fromLat = $input['from_lat'] ?? 0;
$fromLon = $input['from_lon'] ?? 0;
$toLat = $input['to_lat'] ?? 0;
$toLon = $input['to_lon'] ?? 0;
$weight = $input['weight'] ?? 1;
$services = $input['services'] ?? [
    'insurance' => false,
    'fragile' => false,
    'packaging' => false
];

// Get all operators
$allOperators = getAllOperators();

$results = [];

foreach ($allOperators as $operator) {
    // Find nearest from office for this operator
    $fromOffice = findNearestOffice($fromLat, $fromLon, $operator['id']);
    if (!$fromOffice) continue;
    
    // Find nearest to office for this operator
    $toOffice = findNearestOffice($toLat, $toLon, $operator['id']);
    if (!$toOffice) continue;
    
    // Calculate route using OSRM
    $route = calculateRouteOSRM($fromOffice['lat'], $fromOffice['lon'], $toOffice['lat'], $toOffice['lon']);
    if (!$route) continue;
    
    // Calculate price
    $price = calculatePrice($route['distance'], $weight, $services['insurance'], $services['fragile'], $services['packaging'], $operator['tariff_per_km']);
    
    $results[] = [
        'operator_id' => $operator['id'],
        'operator_name' => $operator['name'],
        'from_office_id' => $fromOffice['id'],
        'from_office_title' => $fromOffice['title'],
        'to_office_id' => $toOffice['id'],
        'to_office_title' => $toOffice['title'],
        'distance' => $route['distance'],
        'duration' => $route['duration'],
        'price' => $price
    ];
}

// Sort by price
usort($results, function($a, $b) {
    return $a['price'] <=> $b['price'];
});

echo json_encode(['results' => $results]);

/**
 * Calculate route using OSRM
 */
function calculateRouteOSRM($fromLat, $fromLon, $toLat, $toLon) {
    try {
        $url = "https://router.project-osrm.org/route/v1/driving/{$fromLon},{$fromLat};{$toLon},{$toLat}?overview=full&geometries=geojson";
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data && $data['code'] === 'Ok' && isset($data['routes'][0])) {
            $route = $data['routes'][0];
            return [
                'distance' => $route['distance'] / 1000, // Convert meters to km
                'duration' => $route['duration'] / 60 // Convert seconds to minutes
            ];
        } else {
            return null;
        }
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Calculate price based on distance and other factors
 */
function calculatePrice($distance, $weight, $insurance, $fragile, $packaging, $tariffPerKm) {
    $price = $distance * floatval($tariffPerKm);
    
    // Additional fees
    if ($insurance) $price += $price * 0.02; // 2% of base cost
    if ($fragile) $price += 1; // +1 rub
    if ($packaging) $price += 3; // +3 rub
    
    return round($price * 100) / 100; // Round to 2 decimals
}
?>