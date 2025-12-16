<?php 
require 'db.php';
if (!isset($_SESSION['user'])) header('Location: index.php');

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die('ID заказа не указан');
}

// Get order details
$stmt = $db->prepare("
    SELECT o.*, c.name as carrier_name, u.name as user_name, 
           from_office.city as from_city, from_office.address as from_address,
           to_office.city as to_city, to_office.address as to_address
    FROM orders o
    LEFT JOIN carriers c ON o.carrier_id = c.id
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN offices from_office ON o.from_office = from_office.id
    LEFT JOIN offices to_office ON o.to_office = to_office.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die('Заказ не найден');
}

// Get courier information if assigned
$courier = null;
if ($order['courier_id']) {
    $courier_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $courier_stmt->execute([$order['courier_id']]);
    $courier = $courier_stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о доставке - <?= htmlspecialchars($order['track_number']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .delivery-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-created { background-color: #6c757d; color: white; }
        .status-paid { background-color: #17a2b8; color: white; }
        .status-in_transit { background-color: #007bff; color: white; }
        .status-sort_center { background-color: #17a2b8; color: white; }
        .status-out_for_delivery { background-color: #ffc107; color: black; }
        .status-delivered { background-color: #28a745; color: white; }
        .status-delayed { background-color: #fd7e14; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .status-returned { background-color: #6f42c1; color: white; }
        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .recipient-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h2>Информация о доставке</h2>
            <p class="text-muted">Номер заказа: <?= htmlspecialchars($order['track_number']) ?></p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="delivery-card">
                    <h4>Информация о заказе</h4>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Статус:</strong></td>
                            <td>
                                <span class="status-badge status-<?= $order['tracking_status'] ?>">
                                    <?php 
                                    $status_names = [
                                        'created' => 'Создан',
                                        'paid' => 'Оплачен', 
                                        'in_transit' => 'В пути',
                                        'sort_center' => 'Сорт. центр',
                                        'out_for_delivery' => 'У курьера',
                                        'delivered' => 'Доставлен',
                                        'delayed' => 'Задерживается',
                                        'cancelled' => 'Отменен',
                                        'returned' => 'Возвращен'
                                    ];
                                    echo htmlspecialchars($status_names[$order['tracking_status']] ?? $order['tracking_status']);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Дата создания:</strong></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        </tr>
                        <?php if ($order['delivery_date']): ?>
                        <tr>
                            <td><strong>Дата доставки:</strong></td>
                            <td><?= date('d.m.Y', strtotime($order['delivery_date'])) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Служба доставки:</strong></td>
                            <td><span style="color: <?= $order['carrier_color'] ?? '#333' ?>;"><?= htmlspecialchars($order['carrier_name'] ?? 'Н/Д') ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Вес:</strong></td>
                            <td><?= $order['weight'] ?> кг</td>
                        </tr>
                        <tr>
                            <td><strong>Стоимость:</strong></td>
                            <td><?= number_format($order['cost'], 2) ?> BYN</td>
                        </tr>
                    </table>
                </div>
                
                <div class="delivery-card">
                    <h4>Дополнительные услуги</h4>
                    <ul class="list-unstyled">
                        <li><span class="badge bg-<?= $order['insurance'] ? 'warning text-dark' : 'secondary' ?>">Страховка</span></li>
                        <li><span class="badge bg-<?= $order['packaging'] ? 'info' : 'secondary' ?>">Упаковка</span></li>
                        <li><span class="badge bg-<?= $order['fragile'] ? 'danger' : 'secondary' ?>">Хрупкое</span></li>
                        <li><span class="badge bg-<?= $order['cash_on_delivery'] ? 'success' : 'secondary' ?>">Оплата при получении: <?= $order['cash_on_delivery'] ? number_format($order['cod_amount'], 2) . ' BYN' : 'Нет' ?></span></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="delivery-card">
                    <h4>Отправитель</h4>
                    <p><strong>ФИО:</strong> <?= htmlspecialchars($order['full_name'] ?? 'Н/Д') ?></p>
                    <p><strong>Адрес:</strong> <?= htmlspecialchars($order['home_address'] ?? 'Адрес не указан') ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone'] ?? 'Н/Д') ?></p>
                </div>
                
                <div class="delivery-card">
                    <h4>Получатель</h4>
                    <p><strong>ФИО:</strong> <?= htmlspecialchars($order['recipient_name'] ?? 'Н/Д') ?></p>
                    <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['recipient_address'] ?? 'Адрес не указан') ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($order['recipient_phone'] ?? 'Н/Д') ?></p>
                </div>
                
                <?php if ($courier): ?>
                <div class="delivery-card">
                    <h4>Назначенный курьер</h4>
                    <p><strong>ФИО:</strong> <?= htmlspecialchars($courier['name'] ?? $courier['login']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($courier['phone'] ?? 'Н/Д') ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="delivery-card">
            <h4>Информация для получателя</h4>
            <div class="recipient-info">
                <h5>Проверьте при получении:</h5>
                <ul>
                    <li>Целостность упаковки и отсутствие повреждений</li>
                    <li>Соответствие содержимого заказу</li>
                    <li>Правильность комплектации товара</li>
                </ul>
                
                <h5>Подтверждение получения:</h5>
                <ul>
                    <li>Подпишите накладную только после проверки содержимого</li>
                    <li>При обнаружении повреждений - не подписывайте документ и сообщите курьеру</li>
                    <li>При оплате при получении подготовьте необходимую сумму</li>
                </ul>
                
                <h5>Права получателя:</h5>
                <ul>
                    <li>Проверить товар до подписания документов</li>
                    <li>Отказаться от получения в случае повреждений</li>
                    <li>Запросить копию документов при необходимости</li>
                </ul>
            </div>
        </div>
        
        <div class="delivery-card">
            <h4>Правила доставки</h4>
            <div class="info-section">
                <ul>
                    <li>Доставка осуществляется по адресу, указанному при оформлении заказа</li>
                    <li>Курьер обязан предоставить информацию о содержимом посылки</li>
                    <li>Получатель может проверить содержимое до подписания документов</li>
                    <li>При оплате при получении курьер должен предоставить кассовый чек</li>
                    <li>В случае отсутствия получателя, доставка может быть перенесена на другое время</li>
                </ul>
            </div>
        </div>
        
        <div class="text-center">
            <a href="delivery_receipt.php?order_id=<?= $order['id'] ?>" class="btn btn-primary me-2" target="_blank">Печать накладной</a>
            <button class="btn btn-secondary" onclick="window.print()">Распечатать информацию</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>