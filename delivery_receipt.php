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
    <title>Накладная на доставку - <?= htmlspecialchars($order['track_number']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .order-details {
            margin: 15px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .signature-section {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        .signature-line {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-field {
            width: 45%;
            text-align: center;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            z-index: 1000;
        }
        .print-only {
            display: none;
        }
        @media print {
            .print-btn {
                display: none;
            }
            .print-only {
                display: block;
                text-align: center;
                font-weight: bold;
                margin: 10px 0;
            }
        }
        .recipient-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Печать накладной</button>
    
    <div class="print-only">Документ для печати</div>
    
    <div class="header">
        <h2>НАКЛАДНАЯ НА ДОСТАВКУ</h2>
        <h3>Служба доставки "Express Delivery"</h3>
    </div>
    
    <div class="company-info">
        <p><strong>Адрес:</strong> г. Минск, ул. Примерная, 123</p>
        <p><strong>Телефон:</strong> +375-25-005-50-50</p>
        <p><strong>Email:</strong> freedeliverya@gmail.com</p>
    </div>
    
    <div class="order-details">
        <p><strong>Номер накладной:</strong> <?= htmlspecialchars($order['track_number']) ?></p>
        <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
        <?php if ($order['delivery_date']): ?>
        <p><strong>Дата доставки:</strong> <?= date('d.m.Y', strtotime($order['delivery_date'])) ?></p>
        <?php endif; ?>
        <p><strong>Статус:</strong> 
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
        </p>
    </div>
    
    <div style="display: flex; margin: 20px 0;">
        <div style="flex: 1; padding-right: 10px;">
            <h4>Отправитель:</h4>
            <p><strong>ФИО:</strong> <?= htmlspecialchars($order['full_name'] ?? 'Н/Д') ?></p>
            <p><strong>Адрес:</strong> <?= htmlspecialchars($order['home_address'] ?? 'Адрес не указан') ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone'] ?? 'Н/Д') ?></p>
        </div>
        <div style="flex: 1; padding-left: 10px;">
            <h4>Получатель:</h4>
            <p><strong>ФИО:</strong> <?= htmlspecialchars($order['recipient_name'] ?? 'Н/Д') ?></p>
            <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['recipient_address'] ?? 'Адрес не указан') ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($order['recipient_phone'] ?? 'Н/Д') ?></p>
        </div>
    </div>
    
    <div style="margin: 20px 0;">
        <h4>Детали доставки:</h4>
        <div class="detail-row">
            <span>Служба доставки:</span>
            <span style="color: <?= $order['carrier_color'] ?? '#333' ?>;"><?= htmlspecialchars($order['carrier_name'] ?? 'Н/Д') ?></span>
        </div>
        <div class="detail-row">
            <span>Отделение отправления:</span>
            <span><?= htmlspecialchars($order['from_city'] ?? 'Н/Д') ?>, <?= htmlspecialchars($order['from_address'] ?? 'Н/Д') ?></span>
        </div>
        <div class="detail-row">
            <span>Отделение получения:</span>
            <span><?= htmlspecialchars($order['to_city'] ?? 'Н/Д') ?>, <?= htmlspecialchars($order['to_address'] ?? 'Н/Д') ?></span>
        </div>
        <div class="detail-row">
            <span>Вес посылки:</span>
            <span><?= $order['weight'] ?> кг</span>
        </div>
        <div class="detail-row">
            <span>Стоимость доставки:</span>
            <span><?= number_format($order['cost'], 2) ?> BYN</span>
        </div>
        <div class="detail-row">
            <span>Оплата при получении:</span>
            <span><?= $order['cash_on_delivery'] ? 'Да (' . number_format($order['cod_amount'], 2) . ' BYN)' : 'Нет' ?></span>
        </div>
    </div>
    
    <div style="margin: 20px 0;">
        <h4>Дополнительные услуги:</h4>
        <div class="detail-row">
            <span>Страховка:</span>
            <span><?= $order['insurance'] ? 'Да' : 'Нет' ?></span>
        </div>
        <div class="detail-row">
            <span>Упаковка:</span>
            <span><?= $order['packaging'] ? 'Да' : 'Нет' ?></span>
        </div>
        <div class="detail-row">
            <span>Хрупкое:</span>
            <span><?= $order['fragile'] ? 'Да' : 'Нет' ?></span>
        </div>
    </div>
    
    <?php if ($courier): ?>
    <div style="margin: 20px 0;">
        <h4>Назначенный курьер:</h4>
        <div class="detail-row">
            <span>ФИО курьера:</span>
            <span><?= htmlspecialchars($courier['name'] ?? $courier['login']) ?></span>
        </div>
        <div class="detail-row">
            <span>Телефон курьера:</span>
            <span><?= htmlspecialchars($courier['phone'] ?? 'Н/Д') ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="recipient-info">
        <h4>Информация для получателя:</h4>
        <ul>
            <li>При получении проверьте целостность упаковки и комплектацию товара</li>
            <li>В случае обнаружения повреждений или недостачи - не подписывайте накладную и немедленно сообщите курьеру</li>
            <li>Подпись в накладной означает согласие с условиями доставки и получение товара в надлежащем виде</li>
            <li>При оплате при получении подготовьте необходимую сумму наличными или по карте</li>
            <li>Если вы не можете принять посылку - сообщите курьеру о необходимости переноса доставки</li>
        </ul>
    </div>
    
    <div class="signature-section">
        <p>Получатель подтверждает получение посылки в надлежащем виде, без претензий к содержимому и внешнему виду.</p>
        
        <div class="signature-line">
            <div class="signature-field">
                <p>Подпись получателя</p>
                <div style="height: 50px; border-bottom: 1px solid #333;"></div>
                <p>(Расшифровка)</p>
            </div>
            
            <div class="signature-field">
                <p>Подпись курьера</p>
                <div style="height: 50px; border-bottom: 1px solid #333;"></div>
                <p>(<?= htmlspecialchars($courier['name'] ?? $courier['login'] ?? 'Курьер') ?>)</p>
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <p><strong>Дата и время получения:</strong> _____________</p>
        </div>
    </div>
    
    <div style="margin-top: 50px; text-align: center; font-size: 0.9em; color: #666;">
        <p>Это автоматически сгенерированная накладная. Подписи в электронном виде не имеют юридической силы.</p>
        <p>Подписи должны быть проставлены от руки на бумажном носителе.</p>
    </div>
</body>
</html>