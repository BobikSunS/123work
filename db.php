<?php
/**
 * Database connection configuration for Belarus Delivery Site
 * Connects to MySQL via XAMPP
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Default XAMPP user
define('DB_PASS', '');      // Default XAMPP password is empty
define('DB_NAME', 'delivery_db');

/**
 * Create and return a database connection
 * @return mysqli|null
 */
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Get all operators from database
 * @return array
 */
function getAllOperators() {
    $conn = getDbConnection();
    $sql = "SELECT id, name, tariff_per_km, color, description FROM operators WHERE active = 1 ORDER BY name";
    $result = $conn->query($sql);
    
    $operators = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $operators[] = $row;
        }
    }
    
    $conn->close();
    return $operators;
}

/**
 * Get offices by operator ID
 * @param int $operatorId
 * @return array
 */
function getOfficesByOperator($operatorId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, title, address, lat, lon, city FROM offices WHERE operator_id = ? AND active = 1 ORDER BY city, title");
    $stmt->bind_param("i", $operatorId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $offices = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $offices[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $offices;
}

/**
 * Get office by ID
 * @param int $officeId
 * @return array|null
 */
function getOfficeById($officeId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, title, address, lat, lon, city, operator_id FROM offices WHERE id = ?");
    $stmt->bind_param("i", $officeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $office = null;
    if ($result && $result->num_rows > 0) {
        $office = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $office;
}

/**
 * Get all offices (for geocoding purposes)
 * @return array
 */
function getAllOffices() {
    $conn = getDbConnection();
    $sql = "SELECT id, title, address, lat, lon, city, operator_id FROM offices WHERE active = 1 ORDER BY city, title";
    $result = $conn->query($sql);
    
    $offices = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $offices[] = $row;
        }
    }
    
    $conn->close();
    return $offices;
}

/**
 * Insert a new order into the database
 * @param array $orderData
 * @return int|false - ID of inserted order or false on failure
 */
function insertOrder($orderData) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("INSERT INTO orders (operator_id, from_office_id, to_office_id, sender_name, sender_phone, recipient_name, recipient_phone, recipient_address, weight_kg, distance_km, duration_min, final_price, insurance, fragile, packaging, payment_method, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "iiisssssdddiibbss",
        $orderData['operator_id'],
        $orderData['from_office_id'],
        $orderData['to_office_id'],
        $orderData['sender_name'],
        $orderData['sender_phone'],
        $orderData['recipient_name'],
        $orderData['recipient_phone'],
        $orderData['recipient_address'],
        $orderData['weight_kg'],
        $orderData['distance_km'],
        $orderData['duration_min'],
        $orderData['final_price'],
        $orderData['insurance'],
        $orderData['fragile'],
        $orderData['packaging'],
        $orderData['payment_method'],
        $orderData['comment']
    );
    
    $success = $stmt->execute();
    $orderId = $success ? $stmt->insert_id : false;
    
    $stmt->close();
    $conn->close();
    
    return $orderId;
}

/**
 * Get order by ID
 * @param int $orderId
 * @return array|null
 */
function getOrderById($orderId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT o.*, op.name as operator_name, fo.title as from_office_title, fo.address as from_office_address, fo.lat as from_office_lat, fo.lon as from_office_lon, to_office.title as to_office_title, to_office.address as to_office_address, to_office.lat as to_office_lat, to_office.lon as to_office_lon FROM orders o JOIN operators op ON o.operator_id = op.id JOIN offices fo ON o.from_office_id = fo.id JOIN offices to_office ON o.to_office_id = to_office.id WHERE o.id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $order = null;
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $order;
}

/**
 * Calculate distance between two points using Haversine formula
 * @param float $lat1
 * @param float $lon1
 * @param float $lat2
 * @param float $lon2
 * @return float Distance in kilometers
 */
function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Earth radius in kilometers
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earthRadius * $c;
}

/**
 * Find nearest office to given coordinates
 * @param float $lat
 * @param float $lon
 * @param int $operatorId
 * @return array|null
 */
function findNearestOffice($lat, $lon, $operatorId = null) {
    $conn = getDbConnection();
    
    if ($operatorId) {
        $stmt = $conn->prepare("SELECT id, title, address, lat, lon, city, operator_id, 
            (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance 
            FROM offices 
            WHERE active = 1 AND operator_id = ?
            HAVING distance <= 1000
            ORDER BY distance ASC LIMIT 1");
        $stmt->bind_param("dddd", $lat, $lon, $lat, $operatorId);
    } else {
        $stmt = $conn->prepare("SELECT id, title, address, lat, lon, city, operator_id, 
            (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance 
            FROM offices 
            WHERE active = 1
            HAVING distance <= 1000
            ORDER BY distance ASC LIMIT 1");
        $stmt->bind_param("ddd", $lat, $lon, $lat);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $office = null;
    if ($result && $result->num_rows > 0) {
        $office = $result->fetch_assoc();
        unset($office['distance']); // Remove distance from result since we don't need it outside
    }
    
    $stmt->close();
    $conn->close();
    return $office;
}
?>