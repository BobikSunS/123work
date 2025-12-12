<?php
// Geocoding utility to get coordinates from addresses
header('Content-Type: application/json');

// Function to get coordinates from Nominatim (OpenStreetMap)
function getCoordinates($address, $city) {
    $query = urlencode($address . ', ' . $city . ', Belarus');
    $url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=1&countrycodes=BY";
    
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: BelarusDeliverySystem/1.0"
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (isset($data[0]) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon']
        ];
    }
    
    return null;
}

// If this file is called directly with address parameters
if (isset($_GET['address']) && isset($_GET['city'])) {
    $address = $_GET['address'];
    $city = $_GET['city'];
    
    $coordinates = getCoordinates($address, $city);
    
    if ($coordinates) {
        echo json_encode([
            'success' => true,
            'lat' => $coordinates['lat'],
            'lng' => $coordinates['lng']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Could not geocode address'
        ]);
    }
    exit;
}
?>