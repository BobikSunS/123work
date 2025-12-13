<?php
// get_offices.php — AJAX-запрос для отделений выбранного оператора
require 'db.php';
header('Content-Type: application/json');

$carrier_id = (int)($_GET['carrier_id'] ?? 0);
$search = trim($_GET['search'] ?? '');
$from_office = (int)($_GET['from_office'] ?? 0);
$to_office = (int)($_GET['to_office'] ?? 0);

if ($from_office > 0 && $to_office > 0) {
    // Запрос на получение расстояния между офисами
    $stmt = $db->prepare("SELECT distance_km FROM calculated_routes WHERE from_office_id = ? AND to_office_id = ?");
    $stmt->execute([$from_office, $to_office]);
    $route = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($route) {
        // Если маршрут уже рассчитан, возвращаем его
        echo json_encode(['distance_km' => floatval($route['distance_km'])], JSON_UNESCAPED_UNICODE);
    } else {
        // Если маршрут не рассчитан, возвращаем пустой ответ
        echo json_encode(['distance_km' => null], JSON_UNESCAPED_UNICODE);
    }
} elseif ($carrier_id > 0) {
    $sql = "SELECT id, city, address, lat, lng FROM offices WHERE carrier_id = ?";
    $params = [$carrier_id];
    
    if (!empty($search)) {
        $sql .= " AND (city LIKE ? OR address LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY city, address";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($offices, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([]);
}
?>