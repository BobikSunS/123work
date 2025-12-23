<?php 
require 'db.php';
if (!isset($_SESSION['user'])) header('Location: index.php');

// Обработка назначения курьера на заказ
if (isset($_POST['action']) && $_POST['action'] === 'assign_courier' && $_SESSION['user']['role'] === 'admin') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $courier_id = (int)($_POST['courier_id'] ?? 0);
    
    $stmt = $db->prepare("UPDATE orders SET courier_id=? WHERE id=?");
    $stmt->execute([$courier_id, $order_id]);
    
    // Обновляем статус заказа на "у курьера" если он был в другом статусе
    $stmt = $db->prepare("UPDATE orders SET tracking_status='out_for_delivery' WHERE id=? AND tracking_status NOT IN ('delivered', 'cancelled', 'returned')");
    $stmt->execute([$order_id]);
    
    // Добавляем в историю статусов
    $stmt = $db->prepare("INSERT INTO tracking_status_history (order_id, status, description, created_at) VALUES (?, 'out_for_delivery', 'Заказ назначен курьеру', NOW())");
    $stmt->execute([$order_id]);
    
    // Перезагружаем страницу для обновления данных
    header("Location: track.php?track=" . urlencode($_GET['track']));
    exit;
}

// Обработка изменения статуса заказа
if (isset($_POST['action']) && $_POST['action'] === 'update_status' && $_SESSION['user']['role'] === 'admin') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? 'created';
    $status_reason = trim($_POST['status_reason'] ?? '');
    
    // Check if tracking_status column exists
    $columns_query = $db->query("SHOW COLUMNS FROM orders");
    $existing_columns = [];
    while ($row = $columns_query->fetch()) {
        $existing_columns[] = $row['Field'];
    }
    
    // Update the tracking_status in the orders table if column exists
    if (in_array('tracking_status', $existing_columns)) {
        try {
            // Get the current status to compare with new status
            $current_order = $db->prepare("SELECT tracking_status FROM orders WHERE id = ?");
            $current_order->execute([$order_id]);
            $current_order_data = $current_order->fetch();
            $current_status = $current_order_data['tracking_status'] ?? 'created';
            
            // Update the tracking_status in the orders table
            $stmt = $db->prepare("UPDATE orders SET tracking_status=? WHERE id=?");
            $stmt->execute([$new_status, $order_id]);
            
            // If the new status is 'delivered', update the delivery date
            if ($new_status === 'delivered') {
                $delivery_stmt = $db->prepare("UPDATE orders SET delivery_date = CURDATE() WHERE id = ?");
                $delivery_stmt->execute([$order_id]);
            }
            
            // Check if tracking_status_history table exists
            $tables_query = $db->query("SHOW TABLES LIKE 'tracking_status_history'");
            if ($tables_query->rowCount() > 0) {
                // Add to status history if table exists and status actually changed
                if ($current_status !== $new_status) {
                    $description = "Статус изменен с '{$current_status}' на '{$new_status}'";
                    if ($status_reason) {
                        $description .= ". Причина: {$status_reason}";
                    }
                    $stmt = $db->prepare("INSERT INTO tracking_status_history (order_id, status, description, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$order_id, $new_status, $description]);
                }
            }
        } catch (PDOException $e) {
            // Handle error silently or log it
            error_log("Error updating order status: " . $e->getMessage());
        }
    }
    
    // Перезагружаем страницу для обновления данных
    header("Location: track.php?track=" . urlencode($_GET['track']));
    exit;
}

// Get couriers for assignment
$couriers = $db->query("SELECT id, login, name FROM users WHERE role='courier'")->fetchAll();

$track = $_GET['track'] ?? '';

if (!$track) {
    die('Трек-номер не указан');
}

$order = $db->prepare("SELECT o.*, c.name as carrier_name FROM orders o LEFT JOIN carriers c ON o.carrier_id=c.id WHERE o.track_number=?");
$order->execute([$track]);
$order = $order->fetch();

if (!$order) {
    die('Заказ не найден');
}

// Define order status stages for timeline (normal delivery flow)
$status_stages = [
    'created' => ['name' => 'Создан', 'description' => 'Заказ создан'],
    'paid' => ['name' => 'Оплачен', 'description' => 'Заказ оплачен'], // Changed from 'Обработан' to 'Оплачен'
    'in_transit' => ['name' => 'В пути', 'description' => 'Посылка в пути'],
    'sort_center' => ['name' => 'Сорт. центр', 'description' => 'Посылка в сортировочном центре'],
    'out_for_delivery' => ['name' => 'У курьера', 'description' => 'Посылка у курьера в пути'],
    'delivered' => ['name' => 'Доставлен', 'description' => 'Заказ доставлен'] // Will show delivery date when status is delivered
];

// Define special status that are not part of normal delivery flow
$special_status = [
    'delayed' => ['name' => 'Задерживается', 'description' => 'Возможна задержка доставки'],
    'returned' => ['name' => 'Возвращен', 'description' => 'Заказ возвращен отправителю'],
    'cancelled' => ['name' => 'Отменен', 'description' => 'Заказ отменен']
];

// Get current status from the order (if exists) or default to 'created'
$current_status = $order['tracking_status'] ?? 'created';

// Check if this is a special status
$is_special_status = isset($special_status[$current_status]);

// Calculate progress percentage based on status if not special status
if (!$is_special_status) {
    $status_keys = array_keys($status_stages);
    $current_index = array_search($current_status, $status_keys);
    if ($current_index === false) {
        $current_index = 0; // Default to 'created' if status not found in normal flow
    }
    $progress_percentage = ($current_index / (count($status_keys) - 1)) * 100;
} else {
    $progress_percentage = 0; // No progress for special statuses
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отслеживание посылки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .status-bar {
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .status-progress {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            width: 0%;
            transition: width 0.5s ease;
        }
        .status-indicators {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .status-indicator {
            text-align: center;
            flex: 1;
        }
        .status-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ddd;
            margin: 0 auto 5px;
        }
        .status-dot.active {
            background: #4CAF50;
            border: 2px solid #45a049;
        }
        .status-dot.completed {
            background: #4CAF50;
            border: 2px solid #45a049;
        }
        .status-label {
            font-size: 12px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary shadow-lg">
    <div class="container-fluid">
        <a class="navbar-brand">Отслеживание посылки</a>
        <div>
            <a href="calculator.php" class="btn btn-light me-2">Калькулятор</a>
            <a href="order_form.php" class="btn btn-success me-2">Оформить заказ</a>
            <a href="history.php" class="btn btn-warning me-2">История</a>
            <?php if($_SESSION['user']['role']==='admin'): ?><a href="admin/index.php" class="btn btn-danger me-2">Админка</a><?php endif; ?>
            <a href="logout.php" class="btn btn-outline-light">Выйти</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h3 class="text-center">Статус посылки</h3>
            
            <!-- Status progress bar -->
            <div class="mt-4">
                <h5>Прогресс доставки:</h5>
                <?php if ($current_status === 'out_for_delivery'): ?>
                    <div class="alert alert-info">
                        <strong>Ожидание подтверждения курьера:</strong> 
                        Заказ находится у курьера. Ожидается подтверждение доставки или получения в сортировочном центре.
                    </div>
                <?php elseif ($is_special_status): ?>
                    <div class="alert alert-warning">
                        <strong><?= htmlspecialchars($special_status[$current_status]['name']) ?>:</strong> 
                        <?= htmlspecialchars($special_status[$current_status]['description']) ?>
                    </div>
                <?php else: ?>
                    <div class="status-bar">
                        <div class="status-progress" style="width: <?= $progress_percentage ?>%;"></div>
                    </div>
                    
                    <div class="status-indicators">
                        <?php foreach($status_keys as $index => $status_key): ?>
                            <div class="status-indicator">
                                <div class="status-dot 
                                    <?php 
                                    if ($index < $current_index) echo 'completed'; 
                                    elseif ($index == $current_index) echo 'active'; 
                                    ?>">
                                </div>
                                <div class="status-label"><?= htmlspecialchars($status_stages[$status_key]['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <p><strong>Трек-номер:</strong> <?= htmlspecialchars($order['track_number']) ?></p>
                    <p><strong>Оператор:</strong> <?= htmlspecialchars($order['carrier_name'] ?? 'Н/Д') ?></p>
                    <p><strong>Вес:</strong> <?= $order['weight'] ?> кг</p>
                    <p><strong>Стоимость:</strong> <?= $order['cost'] ?> BYN</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    <?php if(isset($order['delivery_date']) && $order['delivery_date']): ?>
                        <p><strong>Дата доставки:</strong> <?= date('d.m.Y', strtotime($order['delivery_date'])) ?></p>
                    <?php elseif($current_status === 'delivered'): ?>
                        <p><strong>Дата доставки:</strong> <?= date('d.m.Y') ?></p>
                    <?php endif; ?>
                    <p><strong>Текущий статус:</strong> 
                        <span class="badge bg-info"><?= htmlspecialchars($is_special_status ? $special_status[$current_status]['name'] : $status_stages[$current_status]['name']) ?></span>
                    </p>
                </div>
            </div>
            
            <?php if(isset($order['full_name']) && $order['full_name'] || isset($order['pickup_city']) && $order['pickup_city'] || isset($order['delivery_city']) && $order['delivery_city']): ?>
            <div class="mt-4">
                <h4>Детали заказа</h4>
                <div class="row">
                    <?php if(isset($order['full_name']) && $order['full_name']): ?>
                    <div class="col-md-6">
                        <p><strong>ФИО:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if(isset($order['home_address']) && $order['home_address']): ?>
                    <div class="col-md-6">
                        <p><strong>Домашний адрес:</strong> <?= htmlspecialchars($order['home_address']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if(isset($order['pickup_city']) && $order['pickup_city'] && isset($order['pickup_address']) && $order['pickup_address']): ?>
                    <div class="col-md-6">
                        <p><strong>Адрес получения:</strong> <?= htmlspecialchars($order['pickup_city']) ?>, <?= htmlspecialchars($order['pickup_address']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if(isset($order['delivery_city']) && $order['delivery_city'] && isset($order['delivery_address']) && $order['delivery_address']): ?>
                    <div class="col-md-6">
                        <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['delivery_city']) ?>, <?= htmlspecialchars($order['delivery_address']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if(isset($order['insurance']) && $order['insurance'] || isset($order['packaging']) && $order['packaging'] || isset($order['fragile']) && $order['fragile']): ?>
                    <div class="col-md-12">
                        <p><strong>Дополнительно:</strong> 
                            <?php if(isset($order['insurance']) && $order['insurance']): ?><span class="badge bg-warning me-1">Страховка</span><?php endif; ?>
                            <?php if(isset($order['packaging']) && $order['packaging']): ?><span class="badge bg-info me-1">Упаковка</span><?php endif; ?>
                            <?php if(isset($order['fragile']) && $order['fragile']): ?><span class="badge bg-danger me-1">Хрупкое</span><?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <!-- Admin functionality for courier assignment and status updates -->
            <?php if($_SESSION["user"]["role"] === "admin"): ?>
            <div class="mt-4 p-3 bg-light rounded">
                <h4>Административные действия</h4>
                
                <!-- Courier assignment -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Назначение курьера</h5>
                        <form method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="action" value="assign_courier">
                            <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
                            <select name="courier_id" class="form-select me-2" style="width: auto;">
                                <option value="">Выберите курьера</option>
                                <?php foreach($couriers as $courier): ?>
                                <option value="<?= $courier["id"] ?>" <?= ($order["courier_id"] == $courier["id"]) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($courier["name"] ?: $courier["login"]) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-warning">Назначить курьера</button>
                        </form>
                        
                        <?php if($order["courier_id"]): ?>
                        <?php 
                        $assigned_courier = $db->prepare("SELECT name, login FROM users WHERE id = ?");
                        $assigned_courier->execute([$order["courier_id"]]);
                        $assigned_courier_data = $assigned_courier->fetch();
                        ?>
                        <p class="mt-2 mb-0">
                            <strong>Назначенный курьер:</strong> 
                            <?= htmlspecialchars($assigned_courier_data["name"] ?: $assigned_courier_data["login"]) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Изменение статуса</h5>
                        <form method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
                            <select name="new_status" class="form-select me-2" style="width: auto;">
                                <?php 
                                $status_options = [
                                    "created" => "Создан",
                                    "paid" => "Оплачен",
                                    "in_transit" => "В пути",
                                    "sort_center" => "Сорт. центр",
                                    "out_for_delivery" => "У курьера",
                                    "delivered" => "Доставлен",
                                    "delayed" => "Задерживается",
                                    "cancelled" => "Отменен",
                                    "returned" => "Возвращен"
                                ];
                                foreach($status_options as $status_key => $status_name): ?>
                                <option value="<?= $status_key ?>" <?= ($order["tracking_status"] == $status_key) ? "selected" : "" ?>>
                                    <?= $status_name ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="status_reason" class="form-control me-2" placeholder="Причина (опционально)" style="width: 200px;">
                            <button type="submit" class="btn btn-success">Изменить статус</button>
                        </form>
                    </div>
                </div>
                
                <!-- Print links -->
                <div class="row">
                    <div class="col-md-12">
                        <h5>Печать документов</h5>
                        <a href="delivery_receipt.php?order_id=<?= $order["id"] ?>" class="btn btn-primary me-2" target="_blank">Печать накладной</a>
                        <a href="delivery_info.php?order_id=<?= $order["id"] ?>" class="btn btn-secondary" target="_blank">Инфо для получателя</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="history.php" class="btn btn-primary">Вернуться к истории заказов</a>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>