<?php
// test_route.php - Simple test script to verify route functionality
require 'db.php';

header('Content-Type: application/json');

// Get two random offices for testing
$offices = $db->query("SELECT id, city, address, lat, lng FROM offices WHERE lat IS NOT NULL AND lng IS NOT NULL LIMIT 2")->fetchAll();

if (count($offices) < 2) {
    echo json_encode(['error' => 'Not enough offices with coordinates in database']);
    exit;
}

$from_office = $offices[0];
$to_office = $offices[1];

echo json_encode([
    'message' => 'Test offices retrieved',
    'from_office' => $from_office,
    'to_office' => $to_office,
    'note' => 'Use these office IDs to test route calculation in the calculator'
]);
?>