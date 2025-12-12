<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $from_lat = floatval($_GET['from_lat'] ?? 0);
    $from_lng = floatval($_GET['from_lng'] ?? 0);
    $to_lat = floatval($_GET['to_lat'] ?? 0);
    $to_lng = floatval($_GET['to_lng'] ?? 0);
    
    if ($from_lat == 0 || $from_lng == 0 || $to_lat == 0 || $to_lng == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
        exit;
    }
    
    // Check cache first
    $stmt = $db->prepare("
        SELECT distance_km, duration_min, route_data 
        FROM route_cache 
        WHERE (from_lat = ? AND from_lng = ? AND to_lat = ? AND to_lng = ?) 
           OR (from_lat = ? AND from_lng = ? AND to_lat = ? AND to_lng = ?)
        LIMIT 1
    ");
    $stmt->execute([
        $from_lat, $from_lng, $to_lat, $to_lng,
        $to_lat, $to_lng, $from_lat, $from_lng  // Also check reverse route
    ]);
    $cached = $stmt->fetch();
    
    if ($cached) {
        echo json_encode([
            'success' => true,
            'distance_km' => floatval($cached['distance_km']),
            'duration_min' => floatval($cached['duration_min']),
            'route_data' => $cached['route_data']
        ]);
        exit;
    }
    
    // Call OSRM API for route calculation
    $osrm_url = "https://router.project-osrm.org/route/v1/driving/{$from_lng},{$from_lat};{$to_lng},{$to_lat}?overview=full&steps=true";
    
    $response = file_get_contents($osrm_url);
    $data = json_decode($response, true);
    
    if ($data && isset($data['routes']) && count($data['routes']) > 0) {
        $route = $data['routes'][0];
        $distance_km = $route['distance'] / 1000; // Convert meters to km
        $duration_min = $route['duration'] / 60;  // Convert seconds to minutes
        
        // Cache the result
        $stmt = $db->prepare("
            INSERT INTO route_cache (from_lat, from_lng, to_lat, to_lng, distance_km, duration_min, route_data) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                distance_km = VALUES(distance_km), 
                duration_min = VALUES(duration_min), 
                route_data = VALUES(route_data)
        ");
        $stmt->execute([
            $from_lat, $from_lng, $to_lat, $to_lng, 
            $distance_km, $duration_min, json_encode($route)
        ]);
        
        echo json_encode([
            'success' => true,
            'distance_km' => $distance_km,
            'duration_min' => $duration_min,
            'route_data' => $route
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not calculate route']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>