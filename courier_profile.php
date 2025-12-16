<?php 
require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') {
    header('Location: index.php');
    exit;
}

$courier = $_SESSION['user'];
$courier_id = $courier['id'];

// Get courier statistics
$stats = $db->prepare("
    SELECT 
        COUNT(ca.id) as total_assigned,
        SUM(CASE WHEN ca.status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN ca.status = 'in_transit' THEN 1 ELSE 0 END) as in_transit
    FROM courier_assignments ca 
    WHERE ca.courier_id = ?
");
$stats->execute([$courier_id]);
$courier_stats = $stats->fetch();

// Get assigned orders
$assigned_orders = $db->prepare("
    SELECT o.*, c.name as carrier_name, ca.status as assignment_status
    FROM orders o
    JOIN courier_assignments ca ON o.id = ca.order_id
    LEFT JOIN carriers c ON o.carrier_id = c.id
    WHERE ca.courier_id = ?
    ORDER BY ca.assigned_at DESC
");
$assigned_orders->execute([$courier_id]);
$assigned_orders = $assigned_orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль курьера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
        }
        .order-card {
            border-left: 4px solid #667eea;
            margin-bottom: 10px;
        }
        .status-badge {
            font-size: 0.8em;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-dark bg-primary shadow-lg">
    <div class="container-fluid">
        <a class="navbar-brand">Профиль курьера</a>
        <div>
            <a href="track.php" class="btn btn-light me-2">Отслеживание</a>
            <a href="logout.php" class="btn btn-outline-light">Выйти</a>
        </div>
    </div>
</nav>

<div class="container mt-5 flex-grow-1 main-content">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-shadow">
                <div class="card-body text-center">
                    <h4 class="card-title">Профиль</h4>
                    <h5><?= htmlspecialchars($courier['name'] ?: $courier['login']) ?></h5>
                    <p class="text-muted">Курьер</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="stat-card">
                    <h3><?= $courier_stats['total_assigned'] ?></h3>
                    <p>Всего заказов</p>
                </div>
                <div class="stat-card">
                    <h3><?= $courier_stats['completed'] ?></h3>
                    <p>Выполнено</p>
                </div>
                <div class="stat-card">
                    <h3><?= $courier_stats['in_transit'] ?></h3>
                    <p>В пути</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card card-shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Назначенные заказы</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($assigned_orders)): ?>
                        <div class="text-center py-5">
                            <p class="lead">У вас пока нет назначенных заказов</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Трек-номер</th>
                                        <th>Адрес отправки</th>
                                        <th>Адрес получения</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assigned_orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['track_number']) ?></td>
                                        <td>
                                            <?php if ($order['pickup_city'] && $order['pickup_address']): ?>
                                                <?= htmlspecialchars($order['pickup_city']) ?>, <?= htmlspecialchars($order['pickup_address']) ?>
                                            <?php else: ?>
                                                Н/Д
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($order['delivery_city'] && $order['delivery_address']): ?>
                                                <?= htmlspecialchars($order['delivery_city']) ?>, <?= htmlspecialchars($order['delivery_address']) ?>
                                            <?php else: ?>
                                                Н/Д
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $order['assignment_status'] === 'completed' ? 'success' : 
                                                $order['assignment_status'] === 'in_transit' ? 'warning' : 
                                                'info' 
                                            ?>">
                                                <?= $order['assignment_status'] === 'assigned' ? 'Назначен' : 
                                                   $order['assignment_status'] === 'in_transit' ? 'В пути' : 
                                                   $order['assignment_status'] === 'delivered_to_center' ? 'Доставлен в центр' : 
                                                   'Выполнен' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="track.php?track=<?= urlencode($order['track_number']) ?>" class="btn btn-primary btn-sm">Отследить</a>
                                                <?php if ($order['assignment_status'] !== 'completed'): ?>
                                                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivered_to_center')">Доставлен в центр</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer mt-auto py-3" style="background-color: rgba(0,0,0,0.05);">
    <div class="container text-center text-muted">
        <p class="mb-1" style="opacity: 0.5; color: #999 !important;">&copy; 2025 Служба доставки. Все права защищены.</p>
        <p class="mb-1" style="opacity: 0.5; color: #999 !important;">Контактный телефон: +375-25-005-50-50</p>
        <p class="mb-0" style="opacity: 0.5; color: #999 !important;">Email: freedeliverya@gmail.com</p>
    </div>
</footer>

<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Вы уверены, что хотите обновить статус заказа?')) {
        fetch('update_courier_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId + '&status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Статус заказа обновлен');
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при обновлении статуса');
        });
    }
}

// Apply saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark');
    }
    
    // Set up a global theme listener for cross-page consistency
    window.addEventListener('storage', function(e) {
        if (e.key === 'theme') {
            if (e.newValue === 'dark') {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>