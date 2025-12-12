<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';
    $address = $_GET['address'] ?? '';
    $lat = floatval($_GET['lat'] ?? 0);
    $lng = floatval($_GET['lng'] ?? 0);
    
    if ($action === 'geocode' && !empty($address)) {
        // First check cache
        $stmt = $db->prepare("SELECT lat, lng FROM geocache WHERE address = ? LIMIT 1");
        $stmt->execute([$address]);
        $cached = $stmt->fetch();
        
        if ($cached) {
            echo json_encode([
                'success' => true, 
                'lat' => floatval($cached['lat']), 
                'lng' => floatval($cached['lng'])
            ]);
            exit;
        }
        
        // Geocode using Nominatim
        $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address) . '&limit=1&addressdetails=1&accept-language=ru';
        
        // Add delay to respect Nominatim usage policy
        usleep(1000000); // 1 second delay
        
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: BelarusDeliveryProject/1.0 (student project)"
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        if ($data && count($data) > 0) {
            $lat = floatval($data[0]['lat']);
            $lng = floatval($data[0]['lon']);
            
            // Cache the result
            $stmt = $db->prepare("INSERT INTO geocache (address, lat, lng) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng)");
            $stmt->execute([$address, $lat, $lng]);
            
            echo json_encode([
                'success' => true, 
                'lat' => $lat, 
                'lng' => $lng
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Address not found']);
        }
    } elseif ($action === 'reverse' && $lat != 0 && $lng != 0) {
        // Reverse geocode
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . $lat . '&lon=' . $lng . '&addressdetails=1&accept-language=ru';
        
        // Add delay to respect Nominatim usage policy
        usleep(1000000); // 1 second delay
        
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: BelarusDeliveryProject/1.0 (student project)"
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        if ($data && isset($data['display_name'])) {
            echo json_encode([
                'success' => true, 
                'address' => $data['display_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>