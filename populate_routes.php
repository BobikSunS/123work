<?php
// populate_routes.php - Script to pre-calculate routes between all office pairs
require 'db.php';

echo "Starting route population...\n";

// Get all offices
$offices = $db->query("SELECT id, lat, lng FROM offices WHERE lat IS NOT NULL AND lng IS NOT NULL")->fetchAll();

echo "Found " . count($offices) . " offices to process.\n";

$processed = 0;
$skipped = 0;
$calculated = 0;

foreach ($offices as $from_office) {
    foreach ($offices as $to_office) {
        // Skip if same office
        if ($from_office['id'] == $to_office['id']) {
            continue;
        }
        
        // Check if route already exists in database
        $stmt = $db->prepare("SELECT id FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
        $stmt->execute([$from_office['id'], $to_office['id'], $to_office['id'], $from_office['id']]);
        $existing_route = $stmt->fetch();
        
        if ($existing_route) {
            $skipped++;
            continue;
        }
        
        // Calculate straight-line distance as fallback
        $distance = calculateStraightLineDistance(
            $from_office['lat'], 
            $from_office['lng'], 
            $to_office['lat'], 
            $to_office['lng']
        );
        
        // Save the straight-line distance as a fallback
        $stmt = $db->prepare("INSERT INTO calculated_routes (from_office_id, to_office_id, distance_km, duration_min, route_data) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $from_office['id'], 
            $to_office['id'], 
            $distance, 
            (int)($distance * 1.2), 
            null
        ]);
        
        $processed++;
        $calculated++;
        
        // Show progress every 100 calculations
        if (($processed + $skipped) % 100 == 0) {
            echo "Processed: " . ($processed + $skipped) . ", Routes calculated: $calculated, Skipped (existing): $skipped\n";
        }
    }
}

echo "\nRoute population completed!\n";
echo "Total processed: " . ($processed + $skipped) . "\n";
echo "Routes calculated: $calculated\n";
echo "Routes skipped (already exist): $skipped\n";

// Helper function to calculate straight-line distance (fallback)
function calculateStraightLineDistance($lat1, $lng1, $lat2, $lng2) {
    $R = 6371; // Earth's radius in kilometers
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}
?>