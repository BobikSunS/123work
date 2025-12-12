<?php
require 'db.php';

// Проверяем структуру таблицы offices
echo "Проверяем структуру таблицы offices:\n";
try {
    $stmt = $db->query("DESCRIBE offices");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasLat = false;
    $hasLng = false;
    
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        if ($column['Field'] === 'lat') $hasLat = true;
        if ($column['Field'] === 'lng') $hasLng = true;
    }
    
    if (!$hasLat) {
        echo "\nДобавляем столбец lat...\n";
        $db->exec("ALTER TABLE offices ADD COLUMN lat DECIMAL(10, 8) NULL");
        echo "Столбец lat добавлен.\n";
    } else {
        echo "\nСтолбец lat уже существует.\n";
    }
    
    if (!$hasLng) {
        echo "\nДобавляем столбец lng...\n";
        $db->exec("ALTER TABLE offices ADD COLUMN lng DECIMAL(11, 8) NULL");
        echo "Столбец lng добавлен.\n";
    } else {
        echo "\nСтолбец lng уже существует.\n";
    }
    
    echo "\nПроверка завершена.\n";
    
} catch (PDOException $e) {
    echo "Ошибка при проверке структуры таблицы: " . $e->getMessage() . "\n";
}

// Проверяем структуру таблицы orders для обновления
echo "\nПроверяем структуру таблицы orders:\n";
try {
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasDeliveryHours = false;
    
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        if ($column['Field'] === 'delivery_hours') $hasDeliveryHours = true;
    }
    
    if (!$hasDeliveryHours) {
        echo "\nДобавляем столбец delivery_hours...\n";
        $db->exec("ALTER TABLE orders ADD COLUMN delivery_hours DECIMAL(8, 2) NULL");
        echo "Столбец delivery_hours добавлен.\n";
    } else {
        echo "\nСтолбец delivery_hours уже существует.\n";
    }
    
    echo "\nПроверка orders завершена.\n";
    
} catch (PDOException $e) {
    echo "Ошибка при проверке структуры таблицы orders: " . $e->getMessage() . "\n";
}
?>