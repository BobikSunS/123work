<?php
require_once 'db.php';

header('Content-Type: application/json');

function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Earth's radius in kilometers
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    $distance = $earthRadius * $c;
    
    return $distance;
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
        
        // Calculate distance using haversine formula
        $distance = haversineDistance(
            $from_office['lat'], 
            $from_office['lng'], 
            $to_office['lat'], 
            $to_office['lng']
        );
        
        // Estimate time based on distance (assuming average speed of 60 km/h)
        $time_hours = $distance / 60; // This is a rough estimate
        
        // For a real implementation, you would call an actual routing service here
        // like OSRM or OpenRouteService to get real road distances and times
        
        echo json_encode([
            'success' => true,
            'distance_km' => round($distance, 2),
            'time_hours' => round($time_hours, 2),
            'from_office' => $from_office,
            'to_office' => $to_office,
            'route_coordinates' => [
                [$from_office['lat'], $from_office['lng']],
                [$to_office['lat'], $to_office['lng']]
            ] // This is just a straight line; real implementation would have actual route points
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Office IDs not provided']);
}
?>