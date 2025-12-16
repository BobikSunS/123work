<?php
require 'db.php';

// Create a test order
$stmt = $db->prepare("INSERT INTO orders (user_id, track_number, weight, cost, from_office, to_office, carrier_id, full_name, delivery_city, delivery_address, expected_delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([1, 'TEST001', 2.5, 15.50, 1, 2, 1, 'Test User', 'Минск', 'ул. Тестовая, 1', date('Y-m-d', strtotime('+3 days'))]);

echo "Test order created with track number: TEST001\n";
?>