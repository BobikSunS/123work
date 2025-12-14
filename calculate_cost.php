<?php
require 'db.php';
require 'cost_calculator.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST method allowed']);
    exit;
}

$carrier_id = (int)($_POST['carrier_id'] ?? 0);
$from_office = (int)($_POST['from_office'] ?? 0);
$to_office = (int)($_POST['to_office'] ?? 0);
$weight = floatval($_POST['weight'] ?? 0);
$package_type = $_POST['package_type'] ?? 'parcel';
$insurance = isset($_POST['insurance']) && $_POST['insurance'] == 1;
$packaging = isset($_POST['packaging']) && $_POST['packaging'] == 1;
$fragile = isset($_POST['fragile']) && $_POST['fragile'] == 1;
$letter_count = (int)($_POST['letter_count'] ?? 1);
$cod_amount = floatval($_POST['cod_amount'] ?? 0);

// Validate required fields
if ($carrier_id <= 0 || $from_office <= 0 || $to_office <= 0) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $result = calculateDeliveryCost($db, $carrier_id, $from_office, $to_office, $weight, $package_type, $insurance, $letter_count, $packaging, $fragile, false, $cod_amount);
    
    echo json_encode([
        'success' => true,
        'cost' => $result['cost'],
        'distance' => $result['distance']
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>