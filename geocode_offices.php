<?php
require_once 'db.php';

/**
 * Script to geocode offices from addresses using Nominatim
 * This script will populate the lat/lng fields for offices that don't have coordinates
 */

function geocodeAddress($address) {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address . ', Belarus') . '&limit=1';
    
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: BelarusDeliveryApp/1.0\r\n"
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if ($data && isset($data[0])) {
        return [
            'lat' => (float)$data[0]['lat'],
            'lon' => (float)$data[0]['lon']
        ];
    }
    
    return null;
}

// Get all offices without coordinates
$conn = getDbConnection();
$sql = "SELECT id, address, city FROM offices WHERE (lat = 0 OR lon = 0 OR lat IS NULL OR lon IS NULL)";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "Found " . $result->num_rows . " offices without coordinates. Starting geocoding...\n";
    
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        echo "Geocoding: " . $row['address'] . ", " . $row['city'] . "\n";
        
        $coords = geocodeAddress($row['address'] . ', ' . $row['city']);
        
        if ($coords) {
            $stmt = $conn->prepare("UPDATE offices SET lat = ?, lon = ? WHERE id = ?");
            $stmt->bind_param("ddi", $coords['lat'], $coords['lon'], $row['id']);
            
            if ($stmt->execute()) {
                $count++;
                echo "  -> Success: " . $coords['lat'] . ", " . $coords['lon'] . "\n";
            } else {
                echo "  -> Error updating database for ID " . $row['id'] . "\n";
            }
            
            $stmt->close();
        } else {
            echo "  -> Failed to geocode\n";
        }
        
        // Be respectful to the Nominatim service
        sleep(1);
    }
    
    echo "\nGeocoding completed. Updated $count offices.\n";
} else {
    echo "All offices already have coordinates or no offices found.\n";
}

$conn->close();
?>