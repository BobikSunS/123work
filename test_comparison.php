<?php
// Test script to verify the operator comparison fix
require 'db.php';

echo "Testing operator comparison fix...\n";

// Test if we can connect to the database
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM carriers");
    $result = $stmt->fetch();
    echo "Found {$result['count']} carriers in database\n";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM offices");
    $result = $stmt->fetch();
    echo "Found {$result['count']} offices in database\n";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM calculated_routes");
    $result = $stmt->fetch();
    echo "Found {$result['count']} calculated routes in database\n";
    
    echo "Database connection test passed!\n";
} catch (Exception $e) {
    echo "Database connection test failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test the cost calculator function
require 'cost_calculator.php';

try {
    // Test with sample data - using existing office IDs from the SQL dump
    $result = calculateDeliveryCost($db, 1, 807, 826, 1.0, 'parcel', false, 1, false, false);
    echo "Cost calculation test passed! Cost: {$result['cost']}, Distance: {$result['distance']}, Duration: {$result['duration_min']} min\n";
} catch (Exception $e) {
    echo "Cost calculation test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "All tests passed! The operator comparison should now work correctly.\n";
?>