<?php
// get_offices.php — AJAX-запрос для отделений выбранного оператора
require 'db.php';
header('Content-Type: application/json');

$carrier_id = (int)($_GET['carrier'] ?? 0);
$search = trim($_GET['search'] ?? '');

if ($carrier_id > 0) {
    $sql = "SELECT o.id, o.city, o.address, o.lat, o.lng, c.name as carrier_name FROM offices o LEFT JOIN carriers c ON o.carrier_id = c.id WHERE o.carrier_id = ?";
    $params = [$carrier_id];
    
    if (!empty($search)) {
        $sql .= " AND (o.city LIKE ? OR o.address LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY o.city, o.address";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($offices, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([]);
}
?>