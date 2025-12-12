<?php
require 'db.php';

echo "Начинаю геокодирование офисов...\n";

// Получаем все офисы без координат
$stmt = $db->query("SELECT id, city, address FROM offices WHERE lat IS NULL OR lng IS NULL");
$offices = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Найдено " . count($offices) . " офисов для геокодирования.\n";

foreach ($offices as $office) {
    $address = $office['city'] . ', ' . $office['address'];
    $encoded_address = urlencode($address);
    
    // Используем Nominatim для геокодирования
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . $encoded_address . "&countrycodes=BY&limit=1";
    
    echo "Геокодирую: " . $address . "\n";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => 'User-Agent: DeliveryApp/1.0'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response) {
        $data = json_decode($response, true);
        
        if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
            $lat = floatval($data[0]['lat']);
            $lon = floatval($data[0]['lon']);
            
            // Обновляем координаты в базе данных
            $update_stmt = $db->prepare("UPDATE offices SET lat = ?, lng = ? WHERE id = ?");
            $update_stmt->execute([$lat, $lon, $office['id']]);
            
            echo "  -> Успешно: " . $lat . ", " . $lon . "\n";
        } else {
            echo "  -> Не найдено: " . $address . "\n";
        }
    } else {
        echo "  -> Ошибка подключения: " . $address . "\n";
    }
    
    // Задержка, чтобы не перегружать API
    sleep(1);
}

echo "Геокодирование завершено.\n";
?>