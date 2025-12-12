<?php
require_once 'db.php';

header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 0;
$operatorId = isset($_GET['operator_id']) ? (int)$_GET['operator_id'] : 0;

if ($lat != 0 && $lon != 0) {
    $office = findNearestOffice($lat, $lon, $operatorId);
    if ($office) {
        echo json_encode(['office' => $office]);
    } else {
        echo json_encode(['office' => null]);
    }
} else {
    echo json_encode(['office' => null]);
}
?>