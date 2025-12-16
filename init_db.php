<?php
require 'db.php';

// Create users table
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('user', 'admin', 'courier') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

$db->exec($sql);
echo "Users table created successfully\n";

// Create carriers table
$sql = "
CREATE TABLE IF NOT EXISTS carriers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#000000',
    base_cost DECIMAL(10,2) DEFAULT 0,
    cost_per_kg DECIMAL(10,3) DEFAULT 0,
    cost_per_km DECIMAL(10,3) DEFAULT 0,
    max_weight DECIMAL(6,2) DEFAULT 0,
    speed_kmh DECIMAL(6,2) DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

$db->exec($sql);
echo "Carriers table created successfully\n";

// Create offices table
$sql = "
CREATE TABLE IF NOT EXISTS offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carrier_id INT,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    lat DECIMAL(10, 8),
    lng DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (carrier_id) REFERENCES carriers(id)
);
";

$db->exec($sql);
echo "Offices table created successfully\n";

// Create routes table
$sql = "
CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_office INT,
    to_office INT,
    distance_km INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_office) REFERENCES offices(id),
    FOREIGN KEY (to_office) REFERENCES offices(id)
);
";

$db->exec($sql);
echo "Routes table created successfully\n";

// Create orders table
$sql = "
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    track_number VARCHAR(20) UNIQUE NOT NULL,
    weight DECIMAL(6,2) DEFAULT 0,
    cost DECIMAL(10,2) DEFAULT 0,
    from_office INT,
    to_office INT,
    carrier_id INT,
    courier_id INT DEFAULT NULL,
    tracking_status ENUM('created', 'paid', 'in_transit', 'sort_center', 'out_for_delivery', 'delivered', 'delayed', 'cancelled', 'returned') DEFAULT 'created',
    delivery_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (from_office) REFERENCES offices(id),
    FOREIGN KEY (to_office) REFERENCES offices(id),
    FOREIGN KEY (carrier_id) REFERENCES carriers(id),
    FOREIGN KEY (courier_id) REFERENCES users(id)
);
";

$db->exec($sql);
echo "Orders table created successfully\n";

// Create tracking_status_history table
$sql = "
CREATE TABLE IF NOT EXISTS tracking_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    status VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
";

$db->exec($sql);
echo "Tracking status history table created successfully\n";

// Add columns to orders table that might be missing (for sender/receiver info)
$columns = $db->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('full_name', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN full_name VARCHAR(255)");
    echo "Added full_name column to orders table\n";
}
if (!in_array('home_address', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN home_address VARCHAR(255)");
    echo "Added home_address column to orders table\n";
}
if (!in_array('pickup_city', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN pickup_city VARCHAR(100)");
    echo "Added pickup_city column to orders table\n";
}
if (!in_array('pickup_address', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN pickup_address VARCHAR(255)");
    echo "Added pickup_address column to orders table\n";
}
if (!in_array('delivery_city', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN delivery_city VARCHAR(100)");
    echo "Added delivery_city column to orders table\n";
}
if (!in_array('delivery_address', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN delivery_address VARCHAR(255)");
    echo "Added delivery_address column to orders table\n";
}
if (!in_array('expected_delivery_date', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN expected_delivery_date DATE");
    echo "Added expected_delivery_date column to orders table\n";
}
if (!in_array('insurance', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN insurance BOOLEAN DEFAULT 0");
    echo "Added insurance column to orders table\n";
}
if (!in_array('packaging', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN packaging BOOLEAN DEFAULT 0");
    echo "Added packaging column to orders table\n";
}
if (!in_array('fragile', $columns)) {
    $db->exec("ALTER TABLE orders ADD COLUMN fragile BOOLEAN DEFAULT 0");
    echo "Added fragile column to orders table\n";
}

// Create an admin user if not exists
$admin_check = $db->query("SELECT id FROM users WHERE login = 'admin'")->fetch();
if (!$admin_check) {
    $stmt = $db->prepare("INSERT INTO users (login, password, name, email, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute(['admin', 'admin', 'Администратор', 'admin@example.com']);
    echo "Admin user created: login 'admin', password 'admin'\n";
}

// Create a courier user if not exists
$courier_check = $db->query("SELECT id FROM users WHERE login = 'courier'")->fetch();
if (!$courier_check) {
    $stmt = $db->prepare("INSERT INTO users (login, password, name, email, role) VALUES (?, ?, ?, ?, 'courier')");
    $stmt->execute(['courier', 'courier', 'Курьер', 'courier@example.com']);
    echo "Courier user created: login 'courier', password 'courier'\n";
}

// Create a test carrier
$carrier_check = $db->query("SELECT id FROM carriers WHERE name = 'Test Carrier'")->fetch();
if (!$carrier_check) {
    $stmt = $db->prepare("INSERT INTO carriers (name, color, base_cost, cost_per_kg, cost_per_km, max_weight, speed_kmh) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Test Carrier', '#3498db', 5.00, 2.500, 1.200, 50.00, 30.00]);
    echo "Test carrier created\n";
}

echo "Database initialization completed successfully!\n";
?>