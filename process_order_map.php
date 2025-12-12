<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $carrier_id = (int)$_POST['carrier_id'];
    $from_office_id = (int)$_POST['from_office'];
    $to_office_id = (int)$_POST['to_office'];
    $weight = (float)$_POST['weight'];
    $full_name = trim($_POST['full_name']);
    $home_address = trim($_POST['home_address']);
    $pickup_city = trim($_POST['pickup_city']);
    $pickup_address = trim($_POST['pickup_address']);
    $delivery_city = trim($_POST['delivery_city']);
    $delivery_address = trim($_POST['delivery_address']);
    $desired_date = $_POST['desired_date'] ?? null;
    $comment = trim($_POST['comment']);
    
    $insurance = isset($_POST['insurance']) ? 1 : 0;
    $packaging = isset($_POST['packaging']) ? 1 : 0;
    $fragile = isset($_POST['fragile']) ? 1 : 0;
    
    try {
        // Get carrier info
        $stmt = $db->prepare("SELECT * FROM carriers WHERE id = ?");
        $stmt->execute([$carrier_id]);
        $carrier = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$carrier) {
            throw new Exception('Invalid carrier');
        }
        
        // Calculate route using the new function
        $route_data = calculateRoute($from_office_id, $to_office_id);
        
        if (!$route_data['success']) {
            throw new Exception('Could not calculate route: ' . $route_data['error']);
        }
        
        $distance_km = $route_data['distance_km'];
        $time_hours = $route_data['time_hours'];
        
        // Calculate cost based on route and carrier settings
        $base_cost = $carrier['base_cost'];
        $distance_cost = $distance_km * $carrier['cost_per_km'];
        $weight_cost = $weight * $carrier['cost_per_kg'];
        $total_cost = $base_cost + $distance_cost + $weight_cost;
        
        // Add service costs if selected
        if ($insurance) {
            $total_cost += $total_cost * 0.05; // 5% insurance fee
        }
        
        if ($packaging) {
            $total_cost += 5; // 5 BYN packaging fee
        }
        
        if ($fragile) {
            $total_cost += 3; // 3 BYN fragile handling fee
        }
        
        // Start transaction
        $db->beginTransaction();
        
        // Insert order
        $stmt = $db->prepare("
            INSERT INTO orders (
                user_id, carrier_id, from_office_id, to_office_id, 
                weight, full_name, home_address, pickup_city, pickup_address,
                delivery_city, delivery_address, desired_date, comment,
                insurance, packaging, fragile, distance_km, time_hours, cost,
                status, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW()
            )
        ");
        
        $stmt->execute([
            $user_id, $carrier_id, $from_office_id, $to_office_id,
            $weight, $full_name, $home_address, $pickup_city, $pickup_address,
            $delivery_city, $delivery_address, $desired_date, $comment,
            $insurance, $packaging, $fragile, $distance_km, $time_hours, $total_cost
        ]);
        
        $order_id = $db->lastInsertId();
        
        // Commit transaction
        $db->commit();
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php?id=$order_id");
        exit;
        
    } catch (Exception $e) {
        $db->rollback();
        $error = $e->getMessage();
    }
}

// Function to calculate route (in a real implementation, this would call the external service)
function calculateRoute($from_office_id, $to_office_id) {
    global $db;
    
    // Get coordinates for both offices
    $stmt = $db->prepare("SELECT id, city, address, lat, lng FROM offices WHERE id = ? OR id = ?");
    $stmt->execute([$from_office_id, $to_office_id]);
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($offices) < 2) {
        return ['success' => false, 'error' => 'One or both offices not found'];
    }
    
    $from_office = null;
    $to_office = null;
    
    foreach ($offices as $office) {
        if ($office['id'] == $from_office_id) {
            $from_office = $office;
        } else {
            $to_office = $office;
        }
    }
    
    if (!$from_office || !$to_office) {
        return ['success' => false, 'error' => 'Office data incomplete'];
    }
    
    // Calculate realistic distance and duration
    $earthRadius = 6371; // Earth's radius in kilometers
    $dLat = deg2rad($to_office['lat'] - $from_office['lat']);
    $dLon = deg2rad($to_office['lng'] - $from_office['lng']);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($from_office['lat'])) * cos(deg2rad($to_office['lat'])) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    $straightLineDistance = $earthRadius * $c;
    $roadDistanceFactor = 1.2; // Apply factor for road routing
    $distance = $straightLineDistance * $roadDistanceFactor;
    
    $routeInfo = [
        'distance_km' => round($distance, 2),
        'time_hours' => round($distance / 60, 2) // Assuming 60 km/h average speed
    ];
    
    return [
        'success' => true,
        'distance_km' => $routeInfo['distance_km'],
        'time_hours' => $routeInfo['time_hours']
    ];
}

// Helper function for degree to radian conversion
function deg2rad($deg) {
    return $deg * (M_PI/180);
}

// Helper function for sin
function sin($val) {
    return \sin($val);
}

// Helper function for cos
function cos($val) {
    return \cos($val);
}

// Helper function for atan2
function atan2($y, $x) {
    return \atan2($y, $x);
}

// Helper function for sqrt
function sqrt($val) {
    return \sqrt($val);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обработка заказа</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Ошибка при оформлении заказа</h1>
        <p><?= htmlspecialchars($error ?? 'Неизвестная ошибка') ?></p>
        <a href="order_form_map.php" class="btn">Вернуться к форме заказа</a>
    </div>
</body>
</html>