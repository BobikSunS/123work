<?php
require 'db.php';
require 'cost_calculator.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$from_office_id = (int)($_POST['from_office_id'] ?? 0);
$to_office_id = (int)($_POST['to_office_id'] ?? 0);
$weight = (float)($_POST['weight'] ?? 1);
$package_type = $_POST['package_type'] ?? 'parcel';
$insurance = isset($_POST['insurance']) && $_POST['insurance'] == 1;
$letter_count = (int)($_POST['letter_count'] ?? 1);
$packaging = isset($_POST['packaging']) && $_POST['packaging'] == 1;
$fragile = isset($_POST['fragile']) && $_POST['fragile'] == 1;

if ($from_office_id <= 0 || $to_office_id <= 0) {
    echo json_encode(['error' => 'Invalid office IDs']);
    exit;
}

try {
    // First check if route exists for the original route
    $stmt = $db->prepare("SELECT distance_km, duration_min, route_data FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
    $stmt->execute([$from_office_id, $to_office_id, $to_office_id, $from_office_id]);
    $existing_route = $stmt->fetch();

    if (!$existing_route) {
        echo json_encode(['error' => 'Route not found']);
        exit;
    }

    $distance = floatval($existing_route['distance_km']);
    $duration_min = intval($existing_route['duration_min']);

    // Get origin and destination city names efficiently
    $stmt = $db->prepare("SELECT city FROM offices WHERE id = ?");
    $stmt->execute([$from_office_id]);
    $from_city = $stmt->fetchColumn();
    
    $stmt->execute([$to_office_id]);
    $to_city = $stmt->fetchColumn();

    if (!$from_city || !$to_city) {
        echo json_encode(['error' => 'Could not determine origin or destination city']);
        exit;
    }

    // Get all carriers
    $carriers = $db->query("SELECT * FROM carriers")->fetchAll();

    // Pre-fetch all offices grouped by carrier and city for better performance
    $stmt = $db->query("SELECT id, carrier_id, city FROM offices");
    $all_offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $offices_by_carrier_and_city = [];
    foreach ($all_offices as $office) {
        $carrier_id = $office['carrier_id'];
        $city = $office['city'];
        if (!isset($offices_by_carrier_and_city[$carrier_id])) {
            $offices_by_carrier_and_city[$carrier_id] = [];
        }
        if (!isset($offices_by_carrier_and_city[$carrier_id][$city])) {
            $offices_by_carrier_and_city[$carrier_id][$city] = [];
        }
        $offices_by_carrier_and_city[$carrier_id][$city][] = $office['id'];
    }

    $comparison_results = [];

    foreach ($carriers as $carrier) {
        $carrier_id = $carrier['id'];
        
        // Check if this carrier has offices in both origin and destination cities
        $has_from_office = isset($offices_by_carrier_and_city[$carrier_id][$from_city]) && !empty($offices_by_carrier_and_city[$carrier_id][$from_city]);
        $has_to_office = isset($offices_by_carrier_and_city[$carrier_id][$to_city]) && !empty($offices_by_carrier_and_city[$carrier_id][$to_city]);

        if ($has_from_office && $has_to_office) {
            // Get an example office ID from each city for this carrier
            $from_carrier_office_id = $offices_by_carrier_and_city[$carrier_id][$from_city][0];
            $to_carrier_office_id = $offices_by_carrier_and_city[$carrier_id][$to_city][0];

            // Check if there's a route between these specific offices for this carrier
            $carrier_route = $db->prepare("SELECT distance_km, duration_min FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
            $carrier_route->execute([$from_carrier_office_id, $to_carrier_office_id, $to_carrier_office_id, $from_carrier_office_id]);
            $carrier_route_data = $carrier_route->fetch();

            if ($carrier_route_data) {
                // Use the actual route data for this carrier
                $carrier_distance = floatval($carrier_route_data['distance_km']);
                $carrier_duration_min = intval($carrier_route_data['duration_min']);
            } else {
                // Use the same distance and duration if no specific route exists
                $carrier_distance = $distance;
                $carrier_duration_min = $duration_min;
            }
            
            // Calculate cost for this carrier using the cost_calculator function
            $weight_for_calc = $package_type === 'letter' ? $letter_count * 0.02 : $weight;
            
            if ($weight_for_calc > $carrier['max_weight']) {
                continue; // Skip this carrier if weight exceeds limit
            }

            // Using the proper cost calculation function
            $result = calculateDeliveryCost(
                $db, 
                $carrier_id, 
                $from_carrier_office_id, 
                $to_carrier_office_id, 
                $weight_for_calc, 
                $package_type, 
                $insurance, 
                $letter_count, 
                $packaging, 
                $fragile
            );
            
            $cost = $result['cost'];
            // Use the duration from the cost calculation function if available, otherwise use the route duration
            $hours = round($result['duration_min'] / 60, 1);

            $comparison_results[] = [
                'carrier' => $carrier,
                'cost' => $cost,
                'hours' => $hours,
                'distance' => $result['distance'],
                'from_office_id' => (int)$from_carrier_office_id,
                'to_office_id' => (int)$to_carrier_office_id
            ];
        }
    }

    // Sort results by cost
    usort($comparison_results, function($a, $b) {
        return $a['cost'] <=> $b['cost'];
    });

    echo json_encode([
        'success' => true,
        'results' => $comparison_results,
        'original_route' => [
            'from_office_id' => $from_office_id,
            'to_office_id' => $to_office_id,
            'distance' => $distance,
            'duration_min' => $duration_min
        ]
    ]);

} catch (Exception $e) {
    error_log("Operator comparison error: " . $e->getMessage() . " in file " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode(['error' => 'Internal server error occurred while comparing operators']);
    exit;
}
?>