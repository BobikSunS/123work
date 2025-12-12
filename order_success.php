<?php
require_once 'db.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId > 0) {
    $order = getOrderById($orderId);
    
    if (!$order) {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - Доставка по Беларуси</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="green" class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-success mb-3">Заказ успешно оформлен!</h2>
                    <p class="lead">Ваш заказ №<?= $order['id'] ?> принят в обработку</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Информация о доставке</h5>
                                </div>
                                <div class="card-body text-start">
                                    <p><strong>Оператор:</strong> <?= htmlspecialchars($order['operator_name']) ?></p>
                                    <p><strong>Отправка:</strong> <?= htmlspecialchars($order['from_office_title']) ?></p>
                                    <p><strong>Получение:</strong> <?= htmlspecialchars($order['to_office_title']) ?></p>
                                    <p><strong>Расстояние:</strong> <?= number_format($order['distance_km'], 2) ?> км</p>
                                    <p><strong>Время в пути:</strong> <?= $order['duration_min'] ?> мин</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Стоимость</h5>
                                </div>
                                <div class="card-body text-start">
                                    <p><strong>Вес посылки:</strong> <?= number_format($order['weight_kg'], 2) ?> кг</p>
                                    <p><strong>Стоимость:</strong> <?= number_format($order['final_price'], 2) ?> руб</p>
                                    <p><strong>Способ оплаты:</strong> 
                                        <?php if ($order['payment_method'] == 'card'): ?>
                                            Оплата картой
                                        <?php else: ?>
                                            Наличными курьеру
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-2">Сделать новый расчет</a>
                        <a href="track_order.php?id=<?= $order['id'] ?>" class="btn btn-outline-success">Отследить заказ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>