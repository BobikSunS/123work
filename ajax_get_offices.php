<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $carrier_id = (int)($_GET['carrier_id'] ?? 0);
    
    if ($carrier_id > 0) {
        $stmt = $db->prepare("
            SELECT id, carrier_id, city, address, lat, lng 
            FROM offices 
            WHERE carrier_id = ? AND lat IS NOT NULL AND lng IS NOT NULL
            ORDER BY city, address
        ");
        $stmt->execute([$carrier_id]);
        $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'offices' => $offices]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid carrier ID']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>