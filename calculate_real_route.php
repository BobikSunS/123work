<?php
require_once 'db.php';

header('Content-Type: application/json');

// Function to calculate real route using OSRM or similar service
function calculateRealRoute($fromLat, $fromLng, $toLat, $toLng) {
    // For this example, we'll simulate using OSRM
    // In a real implementation, you would use a local OSRM server or OpenRouteService
    
    // OSRM demo server URL (for testing only)
    $osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}?overview=full&steps=true";
    
    // In a local implementation, you would use your own OSRM server
    // $osrmUrl = "http://localhost:5000/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}?overview=full&steps=true";
    
    $response = file_get_contents($osrmUrl);
    
    if ($response === false) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['routes']) && count($data['routes']) > 0) {
        $route = $data['routes'][0];
        
        return [
            'distance_km' => round($route['distance'] / 1000, 2), // Convert meters to km
            'duration_hours' => round($route['duration'] / 3600, 2), // Convert seconds to hours
            'geometry' => $route['geometry'] ?? null
        ];
    }
    
    return null;
}

// For now, since we can't use external services in this environment, 
// we'll create a more sophisticated distance calculation function
function calculateRealisticDistance($fromLat, $fromLng, $toLat, $toLng) {
    // Calculate base distance using haversine formula
    $earthRadius = 6371; // Earth's radius in kilometers
    
    $dLat = deg2rad($toLat - $fromLat);
    $dLon = deg2rad($toLng - $fromLng);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    $straightLineDistance = $earthRadius * $c;
    
    // Apply a factor to account for road routing (roads are typically 10-30% longer than straight line)
    $roadDistanceFactor = 1.2; // Adjust this based on real data
    $distance = $straightLineDistance * $roadDistanceFactor;
    
    // Estimate duration based on distance and average speed (e.g., 60 km/h for truck delivery)
    $averageSpeed = 60; // km/h
    $durationHours = $distance / $averageSpeed;
    
    return [
        'distance_km' => round($distance, 2),
        'duration_hours' => round($durationHours, 2)
    ];
}

if (isset($_POST['from_office_id']) && isset($_POST['to_office_id'])) {
    $from_office_id = (int)$_POST['from_office_id'];
    $to_office_id = (int)$_POST['to_office_id'];
    
    try {
        // Get coordinates for both offices
        $stmt = $db->prepare("SELECT id, city, address, lat, lng FROM offices WHERE id = ? OR id = ?");
        $stmt->execute([$from_office_id, $to_office_id]);
        $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($offices) < 2) {
            echo json_encode(['success' => false, 'error' => 'One or both offices not found']);
            exit;
        }
        
        $from_office = null;
        $to_office = null;
        
        foreach ($offices as $office) {
            if ($office['id'] == $from_office_id) {
                $from_office = $office;
            } else {
                $to_office = $office;
            }
        }
        
        if (!$from_office || !$to_office) {
            echo json_encode(['success' => false, 'error' => 'Office data incomplete']);
            exit;
        }
        
        // Calculate realistic distance and duration
        $routeInfo = calculateRealisticDistance(
            $from_office['lat'], 
            $from_office['lng'], 
            $to_office['lat'], 
            $to_office['lng']
        );
        
        // For a real implementation with actual routing, you would use:
        // $routeInfo = calculateRealRoute(
        //     $from_office['lat'], 
        //     $from_office['lng'], 
        //     $to_office['lat'], 
        //     $to_office['lng']
        // );
        
        // If route calculation failed, fall back to haversine
        if (!$routeInfo) {
            $earthRadius = 6371; // Earth's radius in kilometers
            $dLat = deg2rad($to_office['lat'] - $from_office['lat']);
            $dLon = deg2rad($to_office['lng'] - $from_office['lng']);
            
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($from_office['lat'])) * cos(deg2rad($to_office['lat'])) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            
            $straightLineDistance = $earthRadius * $c;
            $roadDistanceFactor = 1.2;
            $distance = $straightLineDistance * $roadDistanceFactor;
            
            $routeInfo = [
                'distance_km' => round($distance, 2),
                'duration_hours' => round($distance / 60, 2) // Assuming 60 km/h average speed
            ];
        }
        
        echo json_encode([
            'success' => true,
            'distance_km' => $routeInfo['distance_km'],
            'time_hours' => $routeInfo['duration_hours'],
            'from_office' => $from_office,
            'to_office' => $to_office,
            'route_coordinates' => [
                [$from_office['lat'], $from_office['lng']],
                [$to_office['lat'], $to_office['lng']]
            ] // In a real implementation, this would contain actual route points
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Office IDs not provided']);
}
?>