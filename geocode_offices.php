<?php
require_once 'db.php';

echo "Starting mass geocoding of offices...\n";

try {
    // Get offices without coordinates
    $stmt = $db->query("
        SELECT id, city, address 
        FROM offices 
        WHERE (lat IS NULL OR lng IS NULL OR lat = 0 OR lng = 0)
    ");
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($offices) . " offices without coordinates\n";
    
    if (empty($offices)) {
        echo "No offices need geocoding\n";
        exit;
    }
    
    $processed = 0;
    $failed = 0;
    
    foreach ($offices as $office) {
        echo "Geocoding: " . $office['city'] . " - " . $office['address'] . "\n";
        
        // Create a full address string
        $full_address = $office['city'] . ', ' . $office['address'];
        
        // Check if we have this address in cache
        $cache_stmt = $db->prepare("SELECT lat, lng FROM geocache WHERE address = ? LIMIT 1");
        $cache_stmt->execute([$full_address]);
        $cached = $cache_stmt->fetch();
        
        if ($cached) {
            // Use cached coordinates
            $lat = $cached['lat'];
            $lng = $cached['lng'];
            echo "  Using cached coordinates: $lat, $lng\n";
        } else {
            // Call Nominatim API
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($full_address) . '&limit=1&addressdetails=1&accept-language=ru';
            
            // Add delay to respect Nominatim usage policy (1 request per second)
            usleep(1000000); // 1 second delay
            
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: BelarusDeliveryProject/1.0 (student project)"
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            $data = json_decode($response, true);
            
            if ($data && count($data) > 0) {
                $lat = floatval($data[0]['lat']);
                $lng = floatval($data[0]['lon']);
                
                // Cache the result
                $cache_insert = $db->prepare("INSERT INTO geocache (address, lat, lng) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng)");
                $cache_insert->execute([$full_address, $lat, $lng]);
                
                echo "  Geocoded: $lat, $lng\n";
            } else {
                echo "  Failed to geocode: " . $full_address . "\n";
                $failed++;
                continue; // Skip this office
            }
        }
        
        // Update the office with coordinates
        $update_stmt = $db->prepare("UPDATE offices SET lat = ?, lng = ? WHERE id = ?");
        $update_stmt->execute([$lat, $lng, $office['id']]);
        
        $processed++;
        
        // Small delay between updates to not overwhelm the database
        usleep(100000); // 0.1 second
    }
    
    echo "\nGeocoding completed!\n";
    echo "Processed: $processed offices\n";
    echo "Failed: $failed offices\n";
    
} catch (Exception $e) {
    echo "Error during geocoding: " . $e->getMessage() . "\n";
}
?>