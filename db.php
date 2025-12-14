<?php
session_start();

try {
    // Попробуем подключиться к SQLite, если MySQL недоступен
    if (extension_loaded('pdo_sqlite')) {
        $db = new PDO("sqlite:/workspace/delivery_by.sqlite");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Создаем таблицы, если они не существуют
        $db->exec("CREATE TABLE IF NOT EXISTS calculated_routes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            from_office_id INTEGER,
            to_office_id INTEGER,
            distance_km REAL,
            duration_min INTEGER,
            route_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $db->exec("CREATE INDEX IF NOT EXISTS idx_from_to ON calculated_routes (from_office_id, to_office_id)");
    } else {
        die("Ни один из поддерживаемых драйверов баз данных не доступен");
    }
} catch(PDOException $e) {
    die("Подключение к базе данных не удалось: " . $e->getMessage());
}
?>