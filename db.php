<?php
session_start();

try {
    $db = new PDO("mysql:host=localhost;dbname=delivery_by;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch(PDOException $e) {
    die("Подключение к MySQL не удалось: " . $e->getMessage());
}
?>