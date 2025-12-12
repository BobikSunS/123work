<?php
require 'db.php';

header('Content-Type: application/json');

$carrier_id = $_GET['id'] ?? 0;

if ($carrier_id) {
    $stmt = $db->prepare("SELECT * FROM carriers WHERE id = ?");
    $stmt->execute([$carrier_id]);
    $carrier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($carrier) {
        echo json_encode($carrier);
    } else {
        echo json_encode(['error' => 'Carrier not found']);
    }
} else {
    echo json_encode(['error' => 'No carrier ID provided']);
}
?>