<?php 
require 'db.php';
if (!isset($_SESSION['user'])) header('Location: index.php');
$user = $_SESSION['user'];

$carriers = $db->query("SELECT * FROM carriers ORDER BY name")->fetchAll();

// Получаем параметры из GET запроса (если пришли из калькулятора)
$preselected_carrier = isset($_GET['carrier']) ? (int)$_GET['carrier'] : 0;
$preselected_weight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0;
$preselected_cost = isset($_GET['cost']) ? floatval($_GET['cost']) : 0;
$preselected_from_office = isset($_GET['from']) ? (int)$_GET['from'] : 0;
$preselected_to_office = isset($_GET['to']) ? (int)$_GET['to'] : 0;
$preselected_distance = isset($_GET['distance']) ? floatval($_GET['distance']) : 0;

// Получаем информацию об офисах если они были переданы
$from_office_info = null;
$to_office_info = null;

if ($preselected_from_office > 0) {
    $stmt = $db->prepare("SELECT o.*, c.name as carrier_name FROM offices o LEFT JOIN carriers c ON o.carrier_id = c.id WHERE o.id = ?");
    $stmt->execute([$preselected_from_office]);
    $from_office_info = $stmt->fetch();
}

if ($preselected_to_office > 0) {
    $stmt = $db->prepare("SELECT o.*, c.name as carrier_name FROM offices o LEFT JOIN carriers c ON o.carrier_id = c.id WHERE o.id = ?");
    $stmt->execute([$preselected_to_office]);
    $to_office_info = $stmt->fetch();
}

// Обработка POST запроса для создания заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация и очистка данных
    $full_name = trim($_POST['full_name'] ?? '');
    $home_address = trim($_POST['home_address'] ?? '');
    $recipient_name = trim($_POST['recipient_name'] ?? '');
    $recipient_address = trim($_POST['recipient_address'] ?? '');
    $weight = floatval($_POST['weight'] ?? 0);
    $carrier_id = intval($_POST['carrier'] ?? 0);
    $from_office = intval($_POST['from_office'] ?? 0);
    $to_office = intval($_POST['to_office'] ?? 0);
    $desired_date = trim($_POST['desired_date'] ?? '');
    $insurance = isset($_POST['insurance']);
    $packaging = isset($_POST['packaging']);
    $fragile = isset($_POST['fragile']);
    $payment_method = trim($_POST['payment_method'] ?? 'cash');
    $comment = trim($_POST['comment'] ?? '');

    // Валидация обязательных полей
    if (empty($full_name) || empty($home_address) || empty($recipient_name) || empty($recipient_address) || $weight <= 0 || $carrier_id <= 0 || $from_office <= 0 || $to_office <= 0) {
        $error = "Пожалуйста, заполните все обязательные поля!";
    } else {
        try {
            // Получаем информацию о выбранном перевозчике
            $carrier = $db->query("SELECT * FROM carriers WHERE id = $carrier_id")->fetch();
            if (!$carrier) {
                throw new Exception("Неверный перевозчик");
            }

            // Используем переданную стоимость из калькулятора, если она есть
            $cost = floatval($_POST['cost'] ?? 0);
            if ($cost <= 0) {
                // Если стоимость не передана, вычисляем её
                $cost = $carrier['base_cost'] + $weight * $carrier['cost_per_kg'];
                
                // Если выбрана страховка, добавляем 2%
                if ($insurance) {
                    $cost *= 1.02;
                }
                
                // Если выбрана упаковка, добавляем фиксированную стоимость
                if ($packaging) {
                    $cost += 3.00;
                }
                
                // Если хрупкая посылка, добавляем 1%
                if ($fragile) {
                    $cost *= 1.01;
                }
                
                $cost = round($cost, 2);
            }

            // Рассчитываем примерное время доставки на основе расстояния
            $distance = floatval($_POST['distance'] ?? 0);
            $delivery_hours = $distance / $carrier['speed_kmh'];
            
            // Генерируем трек-номер
            $track = strtoupper(substr(md5(uniqid()), 0, 12));

            // Вставляем заказ в базу данных
            $stmt = $db->prepare("INSERT INTO orders (user_id, carrier_id, from_office, to_office, weight, cost, delivery_hours, track_number, tracking_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([
                $user['id'], $carrier_id, $from_office, $to_office, $weight, $cost, $delivery_hours, $track
            ]);
            
            // Получаем ID созданного заказа
            $order_id = $db->lastInsertId();
            
            // Обновляем заказ с дополнительными полями
            $update_fields = [];
            $update_values = [];
            
            // Проверяем существование колонок перед обновлением
            $columns_query = $db->query("SHOW COLUMNS FROM orders");
            $existing_columns = [];
            while ($row = $columns_query->fetch()) {
                $existing_columns[] = $row['Field'];
            }
            
            if (in_array('full_name', $existing_columns)) {
                $update_fields[] = "full_name = ?";
                $update_values[] = $full_name;
            }
            if (in_array('home_address', $existing_columns)) {
                $update_fields[] = "home_address = ?";
                $update_values[] = $home_address;
            }
            if (in_array('recipient_name', $existing_columns)) {
                $update_fields[] = "recipient_name = ?";
                $update_values[] = $recipient_name;
            }
            if (in_array('recipient_address', $existing_columns)) {
                $update_fields[] = "recipient_address = ?";
                $update_values[] = $recipient_address;
            }
            if (in_array('desired_date', $existing_columns)) {
                $update_fields[] = "desired_date = ?";
                $update_values[] = $desired_date;
            }
            if (in_array('insurance', $existing_columns)) {
                $update_fields[] = "insurance = ?";
                $update_values[] = $insurance;
            }
            if (in_array('packaging', $existing_columns)) {
                $update_fields[] = "packaging = ?";
                $update_values[] = $packaging;
            }
            if (in_array('fragile', $existing_columns)) {
                $update_fields[] = "fragile = ?";
                $update_values[] = $fragile;
            }
            if (in_array('payment_method', $existing_columns)) {
                $update_fields[] = "payment_method = ?";
                $update_values[] = $payment_method;
            }
            if (in_array('comment', $existing_columns)) {
                $update_fields[] = "comment = ?";
                $update_values[] = $comment;
            }
            if (in_array('tracking_status', $existing_columns)) {
                $update_fields[] = "tracking_status = ?";
                $update_values[] = 'created';
            }
            
            if (!empty($update_fields)) {
                $update_values[] = $order_id; // for WHERE clause
                $stmt_update = $db->prepare("UPDATE orders SET " . implode(", ", $update_fields) . " WHERE id = ?");
                $stmt_update->execute($update_values);
            }

            // Add initial status to tracking history
            $tables_query = $db->query("SHOW TABLES LIKE 'tracking_status_history'");
            if ($tables_query->rowCount() > 0) {
                $status_stmt = $db->prepare("INSERT INTO tracking_status_history (order_id, status, description) VALUES (?, ?, ?)");
                $status_stmt->execute([$order_id, 'pending', 'Заказ создан, ожидает оплаты']);
            }

            // Redirect to payment page after successful order creation
            header("Location: payment.php?order_id=" . $order_id);
            exit;

        } catch (Exception $e) {
            $error = "Ошибка при создании заказа: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа с картой</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" rel="stylesheet"/>
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        #map { 
            height: 400px; 
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .form-section {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        body.dark .form-section {
            background: #16213e !important;
        }
        .section-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #495057;
        }
        body.dark .section-title {
            border-color: #444;
            color: #e0e0e0;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        body.dark .info-box {
            background: #1a2a4a;
        }
        .order-summary {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
        }
        body.dark .order-summary {
            background: #1a2a4a;
            border-color: #444;
        }
        .route-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        body.dark .route-info {
            background: #1a2a4a;
            border-color: #444;
        }
        .office-marker {
            background: #fff;
            border: 2px solid #007bff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .office-marker:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary shadow-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand">Оформление заказа с картой</a>
        <div>
            <a href="map_calculator.php" class="btn btn-light me-2">Калькулятор</a>
            <a href="profile.php" class="btn btn-light me-2">Профиль</a>
            <a href="history.php" class="btn btn-warning me-2">История</a>
            <?php if($user['role']==='admin'): ?>
                <a href="admin/index.php" class="btn btn-danger me-2">Админка</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-light">Выйти</a>
        </div>
    </div>
</nav>

<!-- Spacer to prevent content from being hidden behind fixed navbar -->
<div style="height: 80px;"></div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="text-center mb-4">Форма оформления заказа</h2>
            
            <?php if ($preselected_carrier > 0 || $preselected_weight > 0): ?>
                <div class="alert alert-info text-center">
                    <strong>Информация из калькулятора:</strong> 
                    <?php 
                    $selected_carrier = null;
                    foreach($carriers as $carrier) {
                        if($carrier['id'] == $preselected_carrier) {
                            $selected_carrier = $carrier;
                            break;
                        }
                    }
                    if($selected_carrier) echo htmlspecialchars($selected_carrier['name']);
                    ?>
                    <?php if($preselected_weight > 0): ?>, вес: <?= $preselected_weight ?> кг<?php endif; ?>
                    <?php if($preselected_cost > 0): ?>, расчетная стоимость: <?= number_format($preselected_cost, 2) ?> BYN<?php endif; ?>
                    <?php if($preselected_distance > 0): ?>, расстояние: <?= number_format($preselected_distance, 2) ?> км<?php endif; ?>
                    <?php if($from_office_info && $to_office_info): ?>
                        <br><strong>Маршрут:</strong> <?= htmlspecialchars($from_office_info['city']) ?> — <?= htmlspecialchars($to_office_info['city']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- Скрытое поле для передачи стоимости из калькулятора -->
                <input type="hidden" name="cost" value="<?= $preselected_cost ?>">
                <input type="hidden" name="distance" value="<?= $preselected_distance ?>">
                
                <!-- Личные данные -->
                <div class="form-section">
                    <h4 class="section-title">Личные данные</h4>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">ФИО отправителя <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required 
                                   placeholder="Иванов Иван Иванович" 
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Домашний адрес отправителя <span class="text-danger">*</span></label>
                            <textarea name="home_address" class="form-control" rows="2" required 
                                      placeholder="Укажите ваш постоянный адрес проживания"><?= htmlspecialchars($_POST['home_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Данные получателя -->
                <div class="form-section">
                    <h4 class="section-title">Данные получателя</h4>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">ФИО получателя <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" class="form-control" required 
                                   placeholder="ФИО получателя" 
                                   value="<?= htmlspecialchars($_POST['recipient_name'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Адрес получателя <span class="text-danger">*</span></label>
                            <textarea name="recipient_address" class="form-control" rows="2" required 
                                      placeholder="Адрес, куда будет доставлена посылка"><?= htmlspecialchars($_POST['recipient_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Карта и выбор офисов -->
                <div class="form-section">
                    <h4 class="section-title">Выбор офисов на карте</h4>
                    
                    <div class="info-box">
                        <strong>Важно:</strong> Выберите офисы отправления и получения на карте ниже.
                    </div>
                    
                    <div id="map"></div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Отделение отправления <span class="text-danger">*</span></label>
                            <select name="from_office" class="form-select" required id="from-office-select">
                                <option value="">Выберите офис на карте</option>
                                <?php
                                $offices = $db->query("SELECT o.*, c.name as carrier_name FROM offices o LEFT JOIN carriers c ON o.carrier_id = c.id ORDER BY c.name, o.city")->fetchAll();
                                foreach($offices as $office):
                                ?>
                                    <option value="<?= $office['id'] ?>" <?= ($preselected_from_office == $office['id']) ? 'selected' : '' ?> data-carrier="<?= $office['carrier_id'] ?>" data-lat="<?= $office['lat'] ?>" data-lng="<?= $office['lng'] ?>">
                                        <?= htmlspecialchars($office['carrier_name']) ?>, <?= htmlspecialchars($office['city']) ?> — <?= htmlspecialchars($office['address']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Отделение получения <span class="text-danger">*</span></label>
                            <select name="to_office" class="form-select" required id="to-office-select">
                                <option value="">Выберите офис на карте</option>
                                <?php foreach($offices as $office): ?>
                                    <option value="<?= $office['id'] ?>" <?= ($preselected_to_office == $office['id']) ? 'selected' : '' ?> data-carrier="<?= $office['carrier_id'] ?>" data-lat="<?= $office['lat'] ?>" data-lng="<?= $office['lng'] ?>">
                                        <?= htmlspecialchars($office['carrier_name']) ?>, <?= htmlspecialchars($office['city']) ?> — <?= htmlspecialchars($office['address']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Информация о доставке -->
                <div class="form-section">
                    <h4 class="section-title">Информация о доставке</h4>

                    <div class="route-info" id="route-info" style="display:<?= ($preselected_from_office && $preselected_to_office) ? 'block' : 'none' ?>;">
                        <h5>Информация о маршруте:</h5>
                        <p><strong>Расстояние:</strong> <span id="route-distance"><?= $preselected_distance ? number_format($preselected_distance, 2) : '-' ?></span> км</p>
                        <p><strong>Время доставки:</strong> <span id="route-duration">-</span> ч</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Служба доставки <span class="text-danger">*</span></label>
                            <select name="carrier" class="form-select" required id="carrier-select">
                                <option value="">Выберите службу доставки</option>
                                <?php foreach($carriers as $carrier): ?>
                                    <option value="<?= $carrier['id'] ?>"
                                        <?= (isset($_POST['carrier']) && $_POST['carrier'] == $carrier['id']) ? 'selected' : (isset($preselected_carrier) && $preselected_carrier == $carrier['id'] ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($carrier['name']) ?> (до <?= $carrier['max_weight'] ?> кг)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Вес посылки (кг) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="weight" class="form-control" required
                                   min="0.1" max="50"
                                   value="<?= htmlspecialchars($_POST['weight'] ?? ($preselected_weight > 0 ? $preselected_weight : '1')) ?>">
                            <div class="form-text">Максимальный вес зависит от выбранной службы доставки</div>
                        </div>
                    </div>
                </div>

                <!-- Дополнительные услуги -->
                <div class="form-section">
                    <h4 class="section-title">Дополнительные услуги</h4>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="insurance" id="insurance"
                                       <?= (isset($_POST['insurance'])) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="insurance">
                                    Страховка (+2%)
                                </label>
                            </div>
                            <div class="form-text">Защита стоимости посылки</div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="packaging" id="packaging"
                                       <?= (isset($_POST['packaging'])) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="packaging">
                                    Упаковка (+5 BYN)
                                </label>
                            </div>
                            <div class="form-text">Профессиональная упаковка</div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="fragile" id="fragile"
                                       <?= (isset($_POST['fragile'])) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="fragile">
                                    Хрупкая посылка (+1%)
                                </label>
                            </div>
                            <div class="form-text">Особое обращение</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Комментарий к заказу</label>
                        <textarea name="comment" class="form-control" rows="3"
                                  placeholder="Дополнительная информация, пожелания"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Кнопки управления -->
                <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                    <a href="map_calculator.php" class="btn btn-secondary btn-lg">Вернуться к калькулятору</a>
                    <button type="submit" class="btn btn-success btn-lg px-5">К Оплате</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer mt-5 py-4 bg-light border-top">
    <div class="container text-center">
        <p class="mb-1 text-muted" style="opacity: 0.5; color: #999 !important;">&copy; 2025 Служба доставки. Все права защищены.</p>
        <p class="mb-1 text-muted" style="opacity: 0.5; color: #999 !important;">Контактный телефон: +375-25-005-50-50</p>
        <p class="mb-0 text-muted" style="opacity: 0.5; color: #999 !important;">Email: freedeliverya@gmail.com</p>
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let map = null;
let routingControl = null;
let markers = [];
let fromMarker = null;
let toMarker = null;

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

    // Initialize map
    initMap();
    
    // Setup office selection
    setupOfficeSelection();
    
    // Calculate duration if we have distance
    if (document.getElementById('route-distance').textContent !== '-') {
        calculateDuration();
    }
});

function initMap() {
    // Initialize the map centered on Belarus
    map = L.map('map').setView([53.904133, 27.557545], 7); // Center on Belarus

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add markers for all offices
    addOfficeMarkers();
    
    // Setup route calculation when both offices are selected
    document.getElementById('from-office-select').addEventListener('change', updateRoute);
    document.getElementById('to-office-select').addEventListener('change', updateRoute);
}

function addOfficeMarkers() {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Add markers for all offices
    const options = document.querySelectorAll('#from-office-select option, #to-office-select option');
    options.forEach(option => {
        if (option.value && option.dataset.lat && option.dataset.lng) {
            const lat = parseFloat(option.dataset.lat);
            const lng = parseFloat(option.dataset.lng);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(`<b>${option.text}</b>`)
                    .on('click', function() {
                        // When marker is clicked, select the corresponding option in the dropdown
                        const selectElement = event.target.closest('#from-office-select, #to-office-select') || 
                                            document.getElementById('from-office-select');
                        
                        if (!document.getElementById('from-office-select').value) {
                            document.getElementById('from-office-select').value = option.value;
                            document.getElementById('from-office-select').dispatchEvent(new Event('change'));
                        } else if (!document.getElementById('to-office-select').value) {
                            document.getElementById('to-office-select').value = option.value;
                            document.getElementById('to-office-select').dispatchEvent(new Event('change'));
                        }
                    });
                markers.push(marker);
            }
        }
    });
}

function setupOfficeSelection() {
    // Pre-select offices if they were passed as parameters
    if (<?= $preselected_from_office ?>) {
        document.getElementById('from-office-select').value = <?= $preselected_from_office ?>;
    }
    if (<?= $preselected_to_office ?>) {
        document.getElementById('to-office-select').value = <?= $preselected_to_office ?>;
    }
}

function updateRoute() {
    const fromSelect = document.getElementById('from-office-select');
    const toSelect = document.getElementById('to-office-select');
    const fromOption = fromSelect.options[fromSelect.selectedIndex];
    const toOption = toSelect.options[toSelect.selectedIndex];
    
    const fromLat = parseFloat(fromOption.dataset.lat);
    const fromLng = parseFloat(fromOption.dataset.lng);
    const toLat = parseFloat(toOption.dataset.lat);
    const toLng = parseFloat(toOption.dataset.lng);
    
    if (isNaN(fromLat) || isNaN(fromLng) || isNaN(toLat) || isNaN(toLng)) {
        return;
    }
    
    // Remove previous route if exists
    if (routingControl) {
        map.removeControl(routingControl);
    }
    
    // Remove previous markers
    if (fromMarker) {
        map.removeLayer(fromMarker);
    }
    if (toMarker) {
        map.removeLayer(toMarker);
    }
    
    // Create new markers
    fromMarker = L.marker([fromLat, fromLng], { 
        icon: L.divIcon({
            className: 'office-marker',
            html: '<div style="background: #28a745; color: white; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">A</div>',
            iconSize: [30, 30]
        })
    }).addTo(map);
    
    toMarker = L.marker([toLat, toLng], { 
        icon: L.divIcon({
            className: 'office-marker',
            html: '<div style="background: #dc3545; color: white; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">B</div>',
            iconSize: [30, 30]
        })
    }).addTo(map);
    
    // Fit map to show both markers
    const group = new L.featureGroup([fromMarker, toMarker]);
    map.fitBounds(group.getBounds().pad(0.1));
    
    // Create routing control
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(fromLat, fromLng),
            L.latLng(toLat, toLng)
        ],
        routeWhileDragging: false,
        show: false, // Don't show the itinerary by default
        createMarker: function() { return null; } // We create our own markers
    }).addTo(map);
    
    // Calculate route and update info when route is found
    routingControl.on('routesfound', function(e) {
        const route = e.routes[0];
        const distanceKm = route.summary.totalDistance / 1000; // Convert to km
        
        // Update route info display
        document.getElementById('route-distance').textContent = distanceKm.toFixed(2);
        document.getElementById('route-info').style.display = 'block';
        
        // Calculate duration based on distance and carrier speed
        calculateDuration();
    });
    
    // Trigger route calculation
    routingControl.route();
}

function calculateDuration() {
    const distanceKm = parseFloat(document.getElementById('route-distance').textContent);
    if (isNaN(distanceKm)) return;
    
    const carrierSelect = document.getElementById('carrier-select');
    const selectedCarrierOption = carrierSelect.options[carrierSelect.selectedIndex];
    
    // This is a simplified calculation - in a real app you might fetch the carrier's speed from the server
    let speedKmh = 60; // Default speed
    
    // We'll use a hidden field to store carrier speeds or fetch them via AJAX
    // For now, we'll use a simple approach
    if (selectedCarrierOption.value) {
        // In a real implementation, we would fetch the actual speed from the server
        // For demonstration purposes, we'll use a default value
        const carrierId = parseInt(selectedCarrierOption.value);
        
        // Map carrier IDs to speeds (this should be fetched from the database)
        const carrierSpeeds = {
            1: 60, // Белпочта
            2: 95, // DPD
            3: 90, // СДЭК
            4: 80, // Европочта
            5: 85, // Boxberry
            6: 92  // Autolight Express
        };
        
        speedKmh = carrierSpeeds[carrierId] || 60;
    }
    
    const durationHours = distanceKm / speedKmh;
    document.getElementById('route-duration').textContent = durationHours.toFixed(2);
}

// Update duration when carrier changes
document.addEventListener('DOMContentLoaded', function() {
    const carrierSelect = document.getElementById('carrier-select');
    if (carrierSelect) {
        carrierSelect.addEventListener('change', calculateDuration);
    }
});
</script>
</body>
</html>