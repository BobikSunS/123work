<?php
require 'db.php';

// Create test offices
$stmt = $db->prepare("INSERT INTO offices (carrier_id, city, address) VALUES (?, ?, ?)");
$stmt->execute([1, 'Минск', 'ул. Независимости, 1']);
$office1_id = $db->lastInsertId();
echo "Created office 1 with ID: $office1_id\n";

$stmt = $db->prepare("INSERT INTO offices (carrier_id, city, address) VALUES (?, ?, ?)");
$stmt->execute([1, 'Гомель', 'ул. Советская, 2']);
$office2_id = $db->lastInsertId();
echo "Created office 2 with ID: $office2_id\n";

// Create a regular user
$stmt = $db->prepare("INSERT INTO users (login, password, name, email, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->execute(['testuser', 'testpass', 'Test User', 'test@example.com']);
$user_id = $db->lastInsertId();
echo "Created user with ID: $user_id\n";

// Create a test order
$stmt = $db->prepare("INSERT INTO orders (user_id, track_number, weight, cost, from_office, to_office, carrier_id, full_name, delivery_city, delivery_address, expected_delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, 'TEST001', 2.5, 15.50, $office1_id, $office2_id, 1, 'Test User', 'Гомель', 'ул. Советская, 2', date('Y-m-d', strtotime('+3 days'))]);

echo "Test order created with track number: TEST001\n";

// Create another order to have more data
$stmt = $db->prepare("INSERT INTO orders (user_id, track_number, weight, cost, from_office, to_office, carrier_id, full_name, delivery_city, delivery_address, expected_delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, 'TEST002', 1.0, 8.75, $office2_id, $office1_id, 1, 'Test User', 'Минск', 'ул. Независимости, 1', date('Y-m-d', strtotime('+2 days'))]);

echo "Test order created with track number: TEST002\n";
?>