<?php
require 'db.php';

try {
    // Test connection
    echo "Database connection successful!\n";
    
    // Test if required tables exist
    $tables = [
        'carriers' => ['id', 'name', 'color'],
        'offices' => ['id', 'carrier_id', 'city', 'address', 'lat', 'lng'],
        'calculated_routes' => ['id', 'from_office_id', 'to_office_id', 'distance_km', 'duration_min']
    ];
    
    foreach ($tables as $table => $columns) {
        try {
            $result = $db->query("SELECT " . implode(', ', $columns) . " FROM $table LIMIT 1");
            if ($result) {
                echo "✓ Table '$table' exists with required columns\n";
            }
        } catch (PDOException $e) {
            echo "✗ Error with table '$table': " . $e->getMessage() . "\n";
        }
    }
    
    // Test carriers query
    $carriers = $db->query("SELECT * FROM carriers")->fetchAll();
    echo "Found " . count($carriers) . " carriers\n";
    
    // Test offices query for a carrier
    if (count($carriers) > 0) {
        $carrier_id = $carriers[0]['id'];
        $offices = $db->prepare("SELECT id, city, address, lat, lng FROM offices WHERE carrier_id = ?");
        $offices->execute([$carrier_id]);
        $offices_data = $offices->fetchAll();
        echo "Found " . count($offices_data) . " offices for carrier ID $carrier_id\n";
    }
    
    // Test calculated_routes query
    $routes = $db->query("SELECT * FROM calculated_routes LIMIT 1")->fetchAll();
    echo "Found " . count($routes) . " route entries\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>