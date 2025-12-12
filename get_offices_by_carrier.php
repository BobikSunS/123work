<?php
require_once 'db.php';

header('Content-Type: application/json');

if (isset($_GET['carrier_id'])) {
    $carrier_id = (int)$_GET['carrier_id'];
    
    try {
        $stmt = $db->prepare("
            SELECT id, city, address, lat, lng 
            FROM offices 
            WHERE carrier_id = ? AND lat IS NOT NULL AND lng IS NOT NULL
            ORDER BY city, address
        ");
        $stmt->execute([$carrier_id]);
        $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'offices' => $offices]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Carrier ID not provided']);
}
?>