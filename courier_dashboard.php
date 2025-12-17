<?php 
require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') {
    header('Location: index.php'); 
    exit;
}

$courier = $_SESSION['user'];
$courier_id = $courier['id'];

// Get orders assigned to this courier
$assigned_orders = $db->prepare("
    SELECT o.*, c.name as carrier_name, u.name as user_name, 
           from_office.city as from_city, to_office.city as to_city
    FROM orders o
    LEFT JOIN carriers c ON o.carrier_id = c.id
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN offices from_office ON o.from_office = from_office.id
    LEFT JOIN offices to_office ON o.to_office = to_office.id
    WHERE o.courier_id = ?
    ORDER BY o.created_at DESC
");
$assigned_orders->execute([$courier_id]);
$assigned_orders = $assigned_orders->fetchAll();

// Get courier statistics
$stats = $db->prepare("
    SELECT 
        COUNT(*) as total_assigned,
        SUM(CASE WHEN tracking_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN tracking_status = 'out_for_delivery' THEN 1 ELSE 0 END) as out_for_delivery
    FROM orders 
    WHERE courier_id = ?
");
$stats->execute([$courier_id]);
$stats = $stats->fetch();

// Handle status updates
if (isset($_POST['action']) && $_POST['action'] === 'update_courier_status') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? 'out_for_delivery';
    
    // Check if order belongs to this courier
    $check_stmt = $db->prepare("SELECT id FROM orders WHERE id = ? AND courier_id = ?");
    $check_stmt->execute([$order_id, $courier_id]);
    
    if ($check_stmt->rowCount() > 0) {
        // Update the tracking_status in the orders table
        $stmt = $db->prepare("UPDATE orders SET tracking_status=? WHERE id=?");
        $stmt->execute([$new_status, $order_id]);
        
        // Add to status history
        $stmt = $db->prepare("INSERT INTO tracking_status_history (order_id, status, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$order_id, $new_status, "Статус изменен курьером {$courier['name']} с '" . ($_POST['old_status'] ?? 'unknown') . "' на '{$new_status}'"]);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель курьера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .stat-card { background: rgba(83, 83, 83, 0.1); border-radius: 15px; padding: 20px; text-align: center; }
        body.dark .stat-card { background: rgba(255,255,255,0.08); }
        .status-badge { font-size: 0.8em; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <span class="navbar-brand">Панель курьера</span>
        <div>
            <span class="text-light me-3">Привет, <?= htmlspecialchars($courier['name'] ?: $courier['login']) ?>!</span>
            <a href="logout.php" class="btn btn-danger">Выйти</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Courier Statistics -->
    <div class="row mb-5 text-gray">
        <div class="col-md-4">
            <div class="stat-card">
                <h3><?= $stats['total_assigned'] ?></h3>
                <p>Назначено заказов</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3><?= $stats['delivered'] ?></h3>
                <p>Доставлено</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3><?= $stats['out_for_delivery'] ?></h3>
                <p>В доставке</p>
            </div>
        </div>
    </div>

    <!-- Assigned Orders -->
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-gray">
            <h4 class="mb-0">Назначенные заказы</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Трек-номер</th>
                            <th>Клиент</th>
                            <th>Откуда</th>
                            <th>Куда</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($assigned_orders as $order): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($order['track_number']) ?></strong></td>
                            <td><?= htmlspecialchars($order['user_name'] ?? 'Н/Д') ?></td>
                            <td><?= htmlspecialchars($order['from_city'] ?? 'Н/Д') ?></td>
                            <td><?= htmlspecialchars($order['to_city'] ?? 'Н/Д') ?></td>
                            <td>
                                <span class="badge 
                                    <?php 
                                    switch($order['tracking_status']) {
                                        case 'out_for_delivery': echo 'bg-warning'; break;
                                        case 'delivered': echo 'bg-success'; break;
                                        case 'sort_center': echo 'bg-info'; break;
                                        case 'in_transit': echo 'bg-primary'; break;
                                        default: echo 'bg-secondary'; break;
                                    }
                                    ?>">
                                    <?= htmlspecialchars($order['tracking_status'] ?? 'created') ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if($order['tracking_status'] !== 'delivered' && $order['tracking_status'] !== 'cancelled'): ?>
                                        <?php if($order['tracking_status'] !== 'sort_center'): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Подтвердить получение заказа в сорт. центр?');">
                                                <input type="hidden" name="action" value="update_courier_status">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="old_status" value="<?= $order['tracking_status'] ?>">
                                                <input type="hidden" name="new_status" value="sort_center">
                                                <button type="submit" class="btn btn-info btn-sm">В сорт. центре</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if($order['tracking_status'] === 'out_for_delivery' || $order['tracking_status'] === 'sort_center'): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Подтвердить доставку заказа?');">
                                                <input type="hidden" name="action" value="update_courier_status">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="old_status" value="<?= $order['tracking_status'] ?>">
                                                <input type="hidden" name="new_status" value="delivered">
                                                <button type="submit" class="btn btn-success btn-sm">Доставлен</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Завершено</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-2">
                                    <a href="delivery_receipt.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-primary" target="_blank">Печать накладной</a>
                                    <a href="delivery_info.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-secondary mt-1" target="_blank">Инфо для получателя</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($assigned_orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Нет назначенных заказов</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Function to update status via AJAX
function updateStatus(event, orderId) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Reload the page to show updated status
        location.reload();
    })
    .catch(error => {
        console.error('Error updating status:', error);
        alert('Ошибка при обновлении статуса заказа');
    });
}
</script>
</body>
</html>