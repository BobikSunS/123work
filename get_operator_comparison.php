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
    // Get the original route information (cities)
    $stmt = $db->prepare("SELECT o1.city as from_city, o2.city as to_city FROM offices o1, offices o2 WHERE o1.id = ? AND o2.id = ?");
    $stmt->execute([$from_office_id, $to_office_id]);
    $route_cities = $stmt->fetch();

    if (!$route_cities) {
        echo json_encode(['error' => 'Route cities not found']);
        exit;
    }

    $from_city = $route_cities['from_city'];
    $to_city = $route_cities['to_city'];

    // Get all carriers
    $carriers = $db->query("SELECT * FROM carriers")->fetchAll();

    $comparison_results = [];

    foreach ($carriers as $carrier) {
        // Find offices of this carrier in both cities
        $from_carrier_office_stmt = $db->prepare("SELECT id FROM offices WHERE carrier_id = ? AND city = ? LIMIT 1");
        $from_carrier_office_stmt->execute([$carrier['id'], $from_city]);
        $from_carrier_office_id = $from_carrier_office_stmt->fetchColumn();

        $to_carrier_office_stmt = $db->prepare("SELECT id FROM offices WHERE carrier_id = ? AND city = ? LIMIT 1");
        $to_carrier_office_stmt->execute([$carrier['id'], $to_city]);
        $to_carrier_office_id = $to_carrier_office_stmt->fetchColumn();

        // If both offices exist for this carrier in the same cities, calculate the cost
        if ($from_carrier_office_id && $to_carrier_office_id) {
            // Check if there's a route between these specific offices for this carrier
            $carrier_route = $db->prepare("SELECT distance_km, duration_min FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
            $carrier_route->execute([$from_carrier_office_id, $to_carrier_office_id, $to_carrier_office_id, $from_carrier_office_id]);
            $carrier_route_data = $carrier_route->fetch();

            if ($carrier_route_data) {
                // Use the actual route data for this carrier
                $carrier_distance = floatval($carrier_route_data['distance_km']);
                $carrier_duration_min = intval($carrier_route_data['duration_min']);
            } else {
                // If no specific route exists for this carrier, use the original route's distance/duration as fallback
                // We need to get the original route's distance
                $original_route = $db->prepare("SELECT distance_km, duration_min FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
                $original_route->execute([$from_office_id, $to_office_id, $to_office_id, $from_office_id]);
                $original_route_data = $original_route->fetch();
                
                if ($original_route_data) {
                    $carrier_distance = floatval($original_route_data['distance_km']);
                    $carrier_duration_min = intval($original_route_data['duration_min']);
                } else {
                    // If no route exists at all, calculate straight-line distance
                    $from_office = $db->prepare("SELECT lat, lng FROM offices WHERE id = ?");
                    $from_office->execute([$from_carrier_office_id]);
                    $from_office_data = $from_office->fetch();
                    
                    $to_office = $db->prepare("SELECT lat, lng FROM offices WHERE id = ?");
                    $to_office->execute([$to_carrier_office_id]);
                    $to_office_data = $to_office->fetch();
                    
                    if ($from_office_data && $to_office_data) {
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
                        
                        $carrier_distance = 6371 * $c; // Earth's radius in km
                        $carrier_duration_min = (int)($carrier_distance * 1.2); // Estimate duration
                    } else {
                        continue; // Skip if we can't get coordinates
                    }
                }
            }
            
            // Calculate cost for this carrier
            $weight_for_calc = $package_type === 'letter' ? $letter_count * 0.02 : $weight;
            
            if ($weight_for_calc > $carrier['max_weight']) {
                continue; // Skip this carrier if weight exceeds limit
            }

            $cost = $carrier['base_cost'] 
                  + $weight_for_calc * $carrier['cost_per_kg'] 
                  + $carrier_distance * $carrier['cost_per_km'];

            if ($insurance) $cost *= 1.02;
            if ($package_type === 'letter') $cost = max($cost, 2.5);
            if ($packaging) $cost += 3.00;
            if ($fragile) $cost *= 1.01;

            $cost = round($cost, 2);
            $hours = round($carrier_duration_min / 60, 1);

            $comparison_results[] = [
                'carrier' => $carrier,
                'cost' => $cost,
                'hours' => $hours,
                'distance' => $carrier_distance,
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
        'results' => $comparison_results
    ]);

} catch (Exception $e) {
    error_log("Operator comparison error: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>