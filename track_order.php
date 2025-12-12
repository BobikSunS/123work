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
    <title>Отследить заказ - Доставка по Беларуси</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        #map { 
            height: 400px; 
            width: 100%;
            border-radius: 8px;
        }
        
        .status-timeline {
            border-left: 3px solid #dee2e6;
            margin-left: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .status-item {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .status-item:before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #dee2e6;
            left: -27px;
            top: 5px;
        }
        
        .status-item.active:before {
            background: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Отслеживание заказа №<?= $order['id'] ?></h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card p-3 mb-4">
                    <h5>Маршрут доставки</h5>
                    <div id="map"></div>
                </div>
                
                <div class="card p-3">
                    <h5>Статус заказа</h5>
                    <div class="status-timeline">
                        <div class="status-item active">
                            <h6 class="text-primary">Заказ оформлен</h6>
                            <p class="text-muted mb-0"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                            <p class="mb-0">Ваш заказ успешно оформлен и принят в обработку</p>
                        </div>
                        <div class="status-item">
                            <h6>В обработке</h6>
                            <p class="text-muted mb-0">-</p>
                            <p class="mb-0">Заказ находится в процессе подготовки к отправке</p>
                        </div>
                        <div class="status-item">
                            <h6>Отправлен</h6>
                            <p class="text-muted mb-0">-</p>
                            <p class="mb-0">Посылка передана в службу доставки</p>
                        </div>
                        <div class="status-item">
                            <h6>Доставлен</h6>
                            <p class="text-muted mb-0">-</p>
                            <p class="mb-0">Посылка успешно доставлена получателю</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card p-3 mb-4">
                    <h5>Информация о заказе</h5>
                    <p><strong>Номер заказа:</strong> <?= $order['id'] ?></p>
                    <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Статус:</strong> 
                        <?php 
                        switch($order['status']) {
                            case 'pending': echo 'В обработке'; break;
                            case 'in_transit': echo 'В пути'; break;
                            case 'delivered': echo 'Доставлен'; break;
                            case 'cancelled': echo 'Отменен'; break;
                            default: echo 'Неизвестен'; break;
                        }
                        ?>
                    </p>
                </div>
                
                <div class="card p-3 mb-4">
                    <h5>Детали доставки</h5>
                    <p><strong>Оператор:</strong> <?= htmlspecialchars($order['operator_name']) ?></p>
                    <p><strong>Отправка:</strong></p>
                    <p class="mb-0"><?= htmlspecialchars($order['from_office_title']) ?></p>
                    <p class="mb-0"><?= htmlspecialchars($order['from_office_address']) ?></p>
                    
                    <p class="mt-3"><strong>Получение:</strong></p>
                    <p class="mb-0"><?= htmlspecialchars($order['to_office_title']) ?></p>
                    <p class="mb-0"><?= htmlspecialchars($order['to_office_address']) ?></p>
                </div>
                
                <div class="card p-3">
                    <h5>Стоимость</h5>
                    <p><strong>Вес посылки:</strong> <?= number_format($order['weight_kg'], 2) ?> кг</p>
                    <p><strong>Расстояние:</strong> <?= number_format($order['distance_km'], 2) ?> км</p>
                    <p><strong>Время в пути:</strong> <?= $order['duration_min'] ?> мин</p>
                    <p><strong>Итоговая стоимость:</strong> <?= number_format($order['final_price'], 2) ?> руб</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize the map
        let map;
        
        function initMap() {
            // Create map centered on Belarus
            map = L.map('map').setView([53.9, 27.55], 7);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add markers for from and to offices
            const fromOfficeLat = <?= $order['from_office_id'] ? $order['from_office_lat'] : 53.9 ?>;
            const fromOfficeLon = <?= $order['from_office_id'] ? $order['from_office_lon'] : 27.55 ?>;
            const toOfficeLat = <?= $order['to_office_id'] ? $order['to_office_lat'] : 53.9 ?>;
            const toOfficeLon = <?= $order['to_office_id'] ? $order['to_office_lon'] : 27.55 ?>;
            
            const fromMarker = L.marker([fromOfficeLat, fromOfficeLon]).addTo(map);
            fromMarker.bindPopup("<b>Отправка:</b><br><?= addslashes(htmlspecialchars($order['from_office_title'])) ?><br><?= addslashes(htmlspecialchars($order['from_office_address'])) ?>");
            
            const toMarker = L.marker([toOfficeLat, toOfficeLon]).addTo(map);
            toMarker.bindPopup("<b>Получение:</b><br><?= addslashes(htmlspecialchars($order['to_office_title'])) ?><br><?= addslashes(htmlspecialchars($order['to_office_address'])) ?>");
            
            // Fit bounds to show both markers
            const group = new L.featureGroup([fromMarker, toMarker]);
            map.fitBounds(group.getBounds().pad(0.1));
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
</body>
</html>