<?php
// Test script for operator comparison
require 'db.php';

// Get some office IDs to test with
$stmt = $db->query("SELECT id, city FROM offices LIMIT 2");
$offices = $stmt->fetchAll();

if (count($offices) < 2) {
    echo "Not enough offices to test with\n";
    exit;
}

$from_office_id = $offices[0]['id'];
$to_office_id = $offices[1]['id'];

echo "Testing with offices:\n";
echo "From: {$offices[0]['id']} ({$offices[0]['city']})\n";
echo "To: {$offices[1]['id']} ({$offices[1]['city']})\n";

// Test the comparison script
$_POST['from_office_id'] = $from_office_id;
$_POST['to_office_id'] = $to_office_id;
$_POST['weight'] = 1.0;
$_POST['package_type'] = 'parcel';
$_POST['insurance'] = 0;
$_POST['letter_count'] = 1;
$_POST['packaging'] = 0;
$_POST['fragile'] = 0;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the comparison script
include 'get_operator_comparison.php';
?>