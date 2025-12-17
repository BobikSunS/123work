<?php require 'db.php';
require 'cost_calculator.php';
if (!isset($_SESSION['user'])) header('Location: index.php');
$user = $_SESSION['user'];
if ($user['role'] === 'courier') header('Location: courier_dashboard.php');

$carriers = $db->query("SELECT * FROM carriers")->fetchAll();

// Функция для получения маршрута между офисами
function getRouteBetweenOffices($db, $from_office_id, $to_office_id) {
    // Проверяем наличие готового маршрута в таблице calculated_routes
    $stmt = $db->prepare("SELECT distance_km, duration_min, route_data FROM calculated_routes WHERE from_office_id = ? AND to_office_id = ?");
    $stmt->execute([$from_office_id, $to_office_id]);
    $route = $stmt->fetch();
    
    if ($route) {
        return [
            'distance' => floatval($route['distance_km']),
            'duration' => intval($route['duration_min']),
            'route_data' => $route['route_data']
        ];
    }
    
    // Если маршрут не найден в calculated_routes, используем старую систему (если нужно)
    return null;
}

// Функция для получения офисов по оператору
function getOfficesByCarrier($db, $carrier_id) {
    $stmt = $db->prepare("SELECT * FROM offices WHERE carrier_id = ?");
    $stmt->execute([$carrier_id]);
    return $stmt->fetchAll();
}

$result = $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrier_id = (int)$_POST['carrier'];
    $from = (int)$_POST['from'];
    $to = (int)$_POST['to'];

    if ($from === $to) {
        $error = "Нельзя отправить в то же отделение!";
    } else {
        $carrier = $db->query("SELECT * FROM carriers WHERE id = $carrier_id")->fetch();
        
        // Получаем маршрут между офисами
        $routeData = getRouteBetweenOffices($db, $from, $to);
        
        if (!$routeData) {
            $error = "Маршрут не найден!";
        } else {
            $distance = $routeData['distance'];
            $duration_min = $routeData['duration'];
            $base_hours = $duration_min / 60; // преобразуем минуты в часы

            $type = $_POST['package_type'];
            $insurance = isset($_POST['insurance']);

            $weight = $type === 'letter' 
                ? 0.02 * (int)($_POST['letter_count'] ?? 1)
                : max((float)$_POST['weight'], 0);

            $max_weight = $carrier['max_weight'];

            if ($weight > $max_weight) {
                $error = "Вес превышает лимит оператора ($max_weight кг)!";
            } else {
                // Используем универсальную функцию для расчета стоимости
                $result = calculateDeliveryCost($db, $carrier_id, $from, $to, $weight, $type, $insurance, $letter_count ?? 1);
                $cost = $result['cost'];
                $distance = $result['distance']; // обновляем расстояние, если оно изменилось
                
                $hours = round($base_hours, 1);

                $result = [
                    'carrier' => $carrier,
                    'cost' => $cost,
                    'hours' => $hours,
                    'distance' => $distance
                ];
                
                // Instead of showing the result on the same page, redirect to order form
                $redirect_url = "order_form.php?carrier=$carrier_id&weight=$weight&cost=$cost&from=$from&to=$to&package_type=$type&insurance=" . ($insurance ? 1 : 0);
                header("Location: $redirect_url");
                exit;
            }
        }
    }
}

function formatDeliveryTime($hours) {
    $days = $hours / 24;
    
    if ($days < 1) {
        return "Менее суток";
    } elseif ($days <= 1.5) {
        return "1-1.5 дня";
    } elseif ($days <= 2) {
        return "1.5-2 дня";
    } elseif ($days <= 3) {
        return "2-3 дня";
    } elseif ($days <= 5) {
        return "3-5 дней";
    } elseif ($days <= 7) {
        return "5-7 дней";
    } else {
        return "Более недели";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Почтовый калькулятор</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polylineutil@1.0.2/PolylineUtil.js"></script>
    
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h4 {
            margin: 0;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .operator-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .operator-info {
            flex-grow: 1;
        }
        
        .operator-name {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .operator-stats {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        
        .operator-stats div {
            display: flex;
            flex-direction: column;
        }
        
        .operator-stats span:first-child {
            font-size: 12px;
            color: #666;
        }
        
        .operator-stats span:last-child {
            font-weight: bold;
        }
        
        .switch-operator-btn {
            margin-left: 15px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-dark bg-primary shadow-lg">
    <div class="container-fluid">
        <a class="navbar-brand">Почтовый калькулятор</a>
        <div>
            <a href="profile.php" class="btn btn-light me-2">Профиль</a>
            <a href="order_form.php" class="btn btn-success me-2">Оформить заказ</a>
            <a href="history.php" class="btn btn-warning me-2">История</a>
            <?php if($user['role']==='admin'): ?><a href="admin/index.php" class="btn btn-danger me-2">Админка</a><?php endif; ?>
            <a href="logout.php" class="btn btn-outline-light">Выйти</a>
        </div>
    </div>
</nav>

<div class="container mt-5 flex-grow-1 main-content">
    <h2 class="text-center text-gray mb-4">Выберите оператора</h2>
    <div class="row justify-content-center g-4">
        <?php foreach($carriers as $c): ?>
        <div class="col-md-4 col-sm-6">
            <div class="carrier-card p-4 text-center text-white shadow-lg" 
                 style="background: <?= $c['color'] ?>;" 
                 onclick="selectCarrier(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name']) ?>')">
                <h4><?= htmlspecialchars($c['name']) ?></h4>
                <small>до <?= $c['max_weight'] ?> кг</small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card mt-5 shadow-lg" id="calc-form" style="display:<?=(isset($_POST['carrier']) || isset($_GET['carrier'])) ? 'block' : 'none' ?>;">
        <div class="card-body">
            <h4 class="text-center mb-4">Расчёт для: <strong id="carrier-name"><?=(isset($_POST['carrier']) ? htmlspecialchars($carriers[array_search($_POST['carrier'], array_column($carriers, 'id'))]['name'] ?? '') : (isset($_GET['carrier']) ? htmlspecialchars($carriers[array_search($_GET['carrier'], array_column($carriers, 'id'))]['name'] ?? '') : ''))?></strong></h4>
            

            
            <!-- Карта -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div id="map"></div>
                </div>
            </div>
            
            <!-- Поиск отделений -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label>Поиск отделений для отправки</label>
                    <div class="search-container">
                        <input type="text" id="search-from" class="form-control" placeholder="Найти отделение для отправки...">
                        <div id="search-results-from" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    
                    <!-- Отделение для отправки -->
                    <div id="selected-from-container" class="selected-office" style="display: none;">
                        <strong>Отделение для отправки:</strong> <span id="selected-from-text"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary change-button" onclick="changeFromOffice()">Изменить</button>
                    </div>
                    
                    <button type="button" class="btn btn-info mt-2" onclick="findNearestFromOffice()">Выбрать ближайшее (отправка)</button>
                </div>
                
                <div class="col-md-6">
                    <label>Поиск отделений для получения</label>
                    <div class="search-container">
                        <input type="text" id="search-to" class="form-control" placeholder="Найти отделение для получения..." disabled>
                        <div id="search-results-to" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    
                    <!-- Отделение для получения -->
                    <div id="selected-to-container" class="selected-office" style="display: none;">
                        <strong>Отделение для получения:</strong> <span id="selected-to-text"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary change-button" onclick="changeToOffice()">Изменить</button>
                    </div>
                    

                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success btn-lg w-100" onclick="showRoute()" id="show-route-btn" disabled>Показать маршрут</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-info btn-lg w-100" onclick="showOperatorComparison()" id="show-comparison-btn" disabled>Сравнить операторов</button>
                </div>
            </div>
            
            <!-- Форма для остальных параметров -->
            <form method="POST" id="calculation-form">
                <input type="hidden" name="cost" id="calculated-cost" value="<?php echo $result ? $result['cost'] : ''; ?>">
                <input type="hidden" name="carrier" id="selected-carrier" value="<?=$_POST['carrier'] ?? $_GET['carrier'] ?? ''?>">
                <input type="hidden" name="from" id="selected-from" value="<?=$_POST['from'] ?? $_GET['from'] ?? ''?>">
                <input type="hidden" name="to" id="selected-to" value="<?=$_POST['to'] ?? $_GET['to'] ?? ''?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Тип отправления</label>
                        <select name="package_type" class="form-select" onchange="toggleFields(this.value); calculateCost()" required>
                            <option value="parcel" <?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'parcel') ? 'selected' : '')?>>Посылка</option>
                            <option value="letter" <?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? 'selected' : '')?>>Письмо</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="weight-div" style="<?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? 'display:none;' : '')?>">
                        <label>Вес (кг)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" value="<?=$_POST['weight'] ?? $_GET['weight'] ?? '1'?>" min="0.1" onchange="calculateCost()" required>
                    </div>

                    <div class="col-md-4" id="letter-div" style="<?=(($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? '' : 'display:none;'?>">
                        <label>Количество писем</label>
                        <input type="number" name="letter_count" class="form-control" value="<?=$_POST['letter_count'] ?? $_GET['letter_count'] ?? '1'?>" min="1" max="50" onchange="calculateCost()">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check me-3">
                            <input type="checkbox" name="insurance" class="form-check-input" id="ins" onchange="calculateCost()" <?=((isset($_POST['insurance']) || isset($_GET['insurance'])) ? 'checked' : '')?>>
                            <label class="form-check-label" for="ins">Страховка (+2%)</label>
                        </div>
                        <div class="form-check me-3">
                            <input type="checkbox" name="packaging" class="form-check-input" id="pkg" onchange="calculateCost()">
                            <label class="form-check-label" for="pkg">Упаковка (+3 BYN)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="fragile" class="form-check-input" id="frag" onchange="calculateCost()">
                            <label class="form-check-label" for="frag">Хрупкое (+1%)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Dynamic price display section -->
                <div class="row g-3 mt-3">
                    <div class="col-md-12">
                        <div class="card bg-light p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Текущая стоимость доставки:</h5>
                                    <p class="mb-0 text-muted">Измените параметры для обновления цены</p>
                                </div>
                                <div class="display-4 text-success fw-bold">
                                    <span id="dynamic-cost">0.00</span> BYN
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-lg mt-4 w-100" id="calculate-btn" onclick="redirectToOrderForm()" disabled>Рассчитать</button>
            </form>
        </div>
    </div>
    
    <!-- Operator Comparison Modal -->
    <div id="operator-comparison-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Сравнение операторов</h4>
                <button class="close-btn" onclick="closeComparisonModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="operator-comparison-results">
                    <!-- Results will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <?php if($result): ?>
    <div class="card mt-5 result-box">
        <div class="card-body text-center">
            <h2><?= $result['cost'] ?> BYN</h2>
            <p class="lead">Время доставки: <?= formatDeliveryTime($result['hours']) ?> (<?= round($result['distance']) ?> км)</p>
            <a href="order_form.php?carrier=<?= $result['carrier']['id'] ?>&weight=<?= ($_POST['package_type'] === 'letter' ? ($_POST['letter_count'] ?? 1) * 0.02 : $_POST['weight'] ?? 1) ?>&cost=<?= $result['cost'] ?>&from=<?= $_POST['from'] ?>&to=<?= $_POST['to'] ?>" class="btn btn-success btn-lg">
                Оформить заказ
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php 
    // Get all calculation results for comparison if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        // Fetch all carrier options for the same route
        $from = (int)$_POST['from'];
        $to = (int)$_POST['to'];
        
        $all_results = [];
        foreach($carriers as $c) {
            $routeData = getRouteBetweenOffices($db, $from, $to);
            if ($routeData) {
                $distance = $routeData['distance'];
                $duration_min = $routeData['duration'];
                $base_hours = $duration_min / 60;

                $type = $_POST['package_type'];
                $insurance = isset($_POST['insurance']);

                $weight = $type === 'letter' 
                    ? 0.02 * (int)($_POST['letter_count'] ?? 1)
                    : max((float)$_POST['weight'], 0);

                $max_weight = $c['max_weight'];

                if ($weight <= $max_weight) {
                    $cost = $c['base_cost'] 
                          + $weight * $c['cost_per_kg'] 
                          + $distance * $c['cost_per_km'];

                    if ($insurance) $cost *= 1.02;
                    if ($type === 'letter') $cost = max($cost, 2.5);

                    $cost = round($cost, 2);
                    $hours = round($base_hours, 1);

                    $all_results[] = [
                        'carrier' => $c,
                        'cost' => $cost,
                        'hours' => $hours,
                        'distance' => $distance
                    ];
                }
            }
        }
        
        if (!empty($all_results)) {
            // Sort by different criteria for filters
            $cheapest = $all_results;
            $fastest = $all_results;
            
            usort($cheapest, function($a, $b) { return $a['cost'] <=> $b['cost']; });
            usort($fastest, function($a, $b) { return $a['hours'] <=> $b['hours']; });
            
            $filters = [
                'all' => $all_results,
                'cheapest' => $cheapest,
                'fastest' => $fastest
            ];
            
            $active_filter = $_GET['filter'] ?? 'all';
            $results_to_show = $filters[$active_filter];
    ?>
    <div class="card mt-5 shadow-lg">
        <div class="card-header bg-secondary text-white">
            <h4>Сравнение операторов</h4>
            <div class="btn-group" role="group">
                <a href="?filter=all&carrier=<?= $_POST['carrier'] ?? $_GET['carrier'] ?? '' ?>&from=<?= $_POST['from'] ?? $_GET['from'] ?? '' ?>&to=<?= $_POST['to'] ?? $_GET['to'] ?? '' ?>&package_type=<?= $_POST['package_type'] ?? $_GET['package_type'] ?? '' ?>&weight=<?= $_POST['weight'] ?? $_GET['weight'] ?? ($_POST['package_type'] === 'letter' ? ($_POST['letter_count'] ?? $_GET['letter_count'] ?? 1) * 0.02 : '') ?>&letter_count=<?= $_POST['letter_count'] ?? $_GET['letter_count'] ?? '' ?>&insurance=<?= isset($_POST['insurance']) || isset($_GET['insurance']) ? '1' : '0' ?>" class="btn btn-sm <?= $active_filter === 'all' ? 'btn-primary' : 'btn-outline-light' ?>">Все</a>
                <a href="?filter=cheapest&carrier=<?= $_POST['carrier'] ?? $_GET['carrier'] ?? '' ?>&from=<?= $_POST['from'] ?? $_GET['from'] ?? '' ?>&to=<?= $_POST['to'] ?? $_GET['to'] ?? '' ?>&package_type=<?= $_POST['package_type'] ?? $_GET['package_type'] ?? '' ?>&weight=<?= $_POST['weight'] ?? $_GET['weight'] ?? ($_POST['package_type'] === 'letter' ? ($_POST['letter_count'] ?? $_GET['letter_count'] ?? 1) * 0.02 : '') ?>&letter_count=<?= $_POST['letter_count'] ?? $_GET['letter_count'] ?? '' ?>&insurance=<?= isset($_POST['insurance']) || isset($_GET['insurance']) ? '1' : '0' ?>" class="btn btn-sm <?= $active_filter === 'cheapest' ? 'btn-primary' : 'btn-outline-light' ?>">Самый дешевый</a>
                <a href="?filter=fastest&carrier=<?= $_POST['carrier'] ?? $_GET['carrier'] ?? '' ?>&from=<?= $_POST['from'] ?? $_GET['from'] ?? '' ?>&to=<?= $_POST['to'] ?? $_GET['to'] ?? '' ?>&package_type=<?= $_POST['package_type'] ?? $_GET['package_type'] ?? '' ?>&weight=<?= $_POST['weight'] ?? $_GET['weight'] ?? ($_POST['package_type'] === 'letter' ? ($_POST['letter_count'] ?? $_GET['letter_count'] ?? 1) * 0.02 : '') ?>&letter_count=<?= $_POST['letter_count'] ?? $_GET['letter_count'] ?? '' ?>&insurance=<?= isset($_POST['insurance']) || isset($_GET['insurance']) ? '1' : '0' ?>" class="btn btn-sm <?= $active_filter === 'fastest' ? 'btn-primary' : 'btn-outline-light' ?>">Самый быстрый</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Оператор</th>
                            <th>Стоимость</th>
                            <th>Время доставки</th>
                            <th>Расстояние</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results_to_show as $r): ?>
                        <tr>
                            <td style="background: <?= $r['carrier']['color'] ?>; color: white;"><?= htmlspecialchars($r['carrier']['name']) ?></td>
                            <td><?= $r['cost'] ?> BYN</td>
                            <td><?= formatDeliveryTime($r['hours']) ?></td>
                            <td><?= round($r['distance']) ?> км</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php }} ?>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map = null;
let markers = [];
let routeLayer = null;
let selectedCarrierId = null;
let selectedFromOffice = null;
let selectedToOffice = null;
let offices = [];

// Инициализация карты
function initMap() {
    if (!map) {
        map = L.map('map').setView([53.904133, 27.557541], 6); // Центр Беларуси

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }
}

// Загрузка офисов для выбранного оператора
function loadOffices(carrierId) {
    fetch(`get_offices.php?carrier_id=${carrierId}`)
        .then(response => response.json())
        .then(data => {
            offices = data;
            updateMapOffices();
        })
        .catch(error => console.error('Error loading offices:', error));
}

// Обновление офисов на карте
function updateMapOffices() {
    // Удаляем старые маркеры
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Добавляем новые маркеры
    offices.forEach(office => {
        if (office.lat && office.lng) {
            const marker = L.marker([office.lat, office.lng]).addTo(map);
            marker.officeId = office.id;
            marker.officeData = office;
            
            marker.bindPopup(`
                <b>${office.city}</b><br>
                ${office.address}<br>
                <button class="btn btn-sm btn-primary mt-1" onclick="selectFromOffice(${office.id}, '${office.city}', '${office.address.replace(/'/g, "\\'")}')">Выбрать как отправку</button>
                <button class="btn btn-sm btn-success mt-1 ms-1" onclick="selectToOffice(${office.id}, '${office.city}', '${office.address.replace(/'/g, "\\'")}')">Выбрать как получение</button>
            `);
            
            marker.on('click', function() {
                // Подсветка маркера
                markers.forEach(m => {
                    if (m._icon) m._icon.classList.remove('office-marker-selected');
                });
                if (this._icon) this._icon.classList.add('office-marker-selected');
            });
            
            markers.push(marker);
        }
    });
}

// Выбор оператора
function selectCarrier(carrierId, carrierName) {
    selectedCarrierId = carrierId;
    document.getElementById('carrier-name').textContent = carrierName;
    document.getElementById('selected-carrier').value = carrierId;
    document.getElementById('calc-form').style.display = 'block';
    
    loadOffices(carrierId);
}

// Выбор офиса для отправки
function selectFromOffice(officeId, city, address) {
    selectedFromOffice = officeId;
    document.getElementById('selected-from').value = officeId;
    document.getElementById('selected-from-text').textContent = `${city} — ${address}`;
    document.getElementById('selected-from-container').style.display = 'block';
    
    // Активируем поле для получения
    document.getElementById('search-to').disabled = false;
    
    // Активируем кнопку показа маршрута если выбраны оба офиса
    if (selectedToOffice) {
        document.getElementById('show-route-btn').disabled = false;
        document.getElementById('calculate-btn').disabled = false;
        document.getElementById('show-formula-btn').disabled = false;
    }
}

// Выбор офиса для получения
function selectToOffice(officeId, city, address) {
    selectedToOffice = officeId;
    document.getElementById('selected-to').value = officeId;
    document.getElementById('selected-to-text').textContent = `${city} — ${address}`;
    document.getElementById('selected-to-container').style.display = 'block';
    
    // Активируем кнопку показа маршрута если выбраны оба офиса
    if (selectedFromOffice) {
        document.getElementById('show-route-btn').disabled = false;
        document.getElementById('calculate-btn').disabled = false;
        document.getElementById('show-comparison-btn').disabled = false;
    }
}

// Изменение офиса отправки
function changeFromOffice() {
    selectedFromOffice = null;
    document.getElementById('selected-from').value = '';
    document.getElementById('selected-from-container').style.display = 'none';
    document.getElementById('show-route-btn').disabled = true;
    document.getElementById('calculate-btn').disabled = true;
    document.getElementById('show-formula-btn').disabled = true;
    
    // Деактивируем поле для получения
    document.getElementById('search-to').disabled = true;
    document.getElementById('selected-to-container').style.display = 'none';
    selectedToOffice = null;
    document.getElementById('selected-to').value = '';
    
    // Удаляем маршрут если он был
    if (routeLayer) {
        map.removeLayer(routeLayer);
        routeLayer = null;
    }
}

// Изменение офиса получения
function changeToOffice() {
    selectedToOffice = null;
    document.getElementById('selected-to').value = '';
    document.getElementById('selected-to-container').style.display = 'none';
    document.getElementById('show-route-btn').disabled = true;
    document.getElementById('calculate-btn').disabled = true;
    document.getElementById('show-formula-btn').disabled = true;
    
    // Удаляем маршрут если он был
    if (routeLayer) {
        map.removeLayer(routeLayer);
        routeLayer = null;
    }
}

// Поиск ближайшего офиса отправки
function findNearestFromOffice() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            // Находим ближайший офис
            let nearestOffice = null;
            let minDistance = Infinity;
            
            offices.forEach(office => {
                if (office.lat && office.lng) {
                    const distance = Math.sqrt(
                        Math.pow(office.lat - userLat, 2) + 
                        Math.pow(office.lng - userLng, 2)
                    );
                    
                    if (distance < minDistance) {
                        minDistance = distance;
                        nearestOffice = office;
                    }
                }
            });
            
            if (nearestOffice) {
                selectFromOffice(nearestOffice.id, nearestOffice.city, nearestOffice.address);
                // Центрируем карту на выбранном офисе
                map.setView([nearestOffice.lat, nearestOffice.lng], 13);
            }
        }, function() {
            alert("Не удалось получить ваше местоположение. Пожалуйста, разрешите доступ к геолокации или выберите офис вручную.");
        });
    } else {
        alert("Геолокация не поддерживается вашим браузером. Пожалуйста, выберите офис вручную.");
    }
}



// Helper function to decode Google's polyline encoding
function decodePolyline(encoded) {
    let points = [];
    let index = 0, len = encoded.length;
    let lat = 0, lng = 0;

    while (index < len) {
        let b, shift = 0, result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        
        let dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lat += dlat;

        shift = 0;
        result = 0;
        do {
            b = encoded.charCodeAt(index++) - 63;
            result |= (b & 0x1f) << shift;
            shift += 5;
        } while (b >= 0x20);
        
        let dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
        lng += dlng;

        points.push([lat * 1e-5, lng * 1e-5]);
    }
    
    return points;
}

// Показ маршрута
function showRoute() {
    if (!selectedFromOffice || !selectedToOffice) {
        alert("Пожалуйста, выберите оба офиса (отправка и получение).");
        return;
    }

    // Удаляем предыдущий маршрут
    if (routeLayer) {
        map.removeLayer(routeLayer);
        routeLayer = null;
    }

    // Показываем индикатор загрузки
    const loadingMessage = document.createElement('div');
    loadingMessage.id = 'route-loading';
    loadingMessage.innerHTML = '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 10000;">Рассчитываем маршрут...</div>';
    document.body.appendChild(loadingMessage);

    // Отправляем запрос на сервер для получения маршрута
    const formData = new FormData();
    formData.append('from_office_id', selectedFromOffice);
    formData.append('to_office_id', selectedToOffice);

    fetch('get_route.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Удаляем индикатор загрузки
        const loadingElement = document.getElementById('route-loading');
        if (loadingElement) {
            document.body.removeChild(loadingElement);
        }

        if (data.success) {
            if (data.route_data) {
                // Check if route_data is a polyline string or an array
                let routeCoords = [];
                
                if (typeof data.route_data === 'string') {
                    // If it's a polyline string, decode it
                    try {
                        // Use polyline decoding instead of PolylineUtil
                        routeCoords = decodePolyline(data.route_data).map(coord => [coord[0], coord[1]]);
                    } catch (e) {
                        console.error('Error decoding polyline:', e);
                        // Fallback to straight line
                        const fromOffice = offices.find(o => o.id == selectedFromOffice);
                        const toOffice = offices.find(o => o.id == selectedToOffice);
                        
                        if (fromOffice && toOffice) {
                            routeCoords = [
                                [fromOffice.lat, fromOffice.lng],
                                [toOffice.lat, toOffice.lng]
                            ];
                        }
                    }
                } else if (Array.isArray(data.route_data.coordinates)) {
                    // If it's an array of coordinates from OSRM
                    routeCoords = data.route_data.coordinates.map(coord => [coord[1], coord[0]]); // [lat, lng] format for Leaflet
                }

                if (routeCoords.length > 0) {
                    routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                    // Center the map on the route
                    const bounds = L.latLngBounds(routeCoords);
                    map.fitBounds(bounds, {padding: [50, 50]});

                    alert(`Маршрут построен по дорогам. Расстояние: ${data.distance.toFixed(2)} км, Время: ${data.duration} мин.`);

                    // Show comparison button after route is loaded
                    setTimeout(() => {
                        if (confirm("Показать сравнение с другими операторами для этого маршрута?")) {
                            showOperatorComparison();
                        }
                    }, 500);
                } else {
                    // If no route coords could be processed, use straight line as fallback
                    const fromOffice = offices.find(o => o.id == selectedFromOffice);
                    const toOffice = offices.find(o => o.id == selectedToOffice);

                    if (fromOffice && toOffice) {
                        const straightCoords = [
                            [fromOffice.lat, fromOffice.lng],
                            [toOffice.lat, toOffice.lng]
                        ];

                        routeLayer = L.polyline(straightCoords, {color: 'red', weight: 4}).addTo(map);

                        // Center the map on the route
                        const bounds = L.latLngBounds(straightCoords);
                        map.fitBounds(bounds, {padding: [50, 50]});

                        alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км (по прямой).`);

                        // Show comparison button after route is loaded
                        setTimeout(() => {
                            if (confirm("Показать сравнение с другими операторами для этого маршрута?")) {
                                showOperatorComparison();
                            }
                        }, 500);
                    }
                }
            } else {
                // If route_data is null, use straight line as fallback
                const fromOffice = offices.find(o => o.id == selectedFromOffice);
                const toOffice = offices.find(o => o.id == selectedToOffice);

                if (fromOffice && toOffice) {
                    const routeCoords = [
                        [fromOffice.lat, fromOffice.lng],
                        [toOffice.lat, toOffice.lng]
                    ];

                    routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                    // Center the map on the route
                    const bounds = L.latLngBounds(routeCoords);
                    map.fitBounds(bounds, {padding: [50, 50]});

                    alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км (по прямой).`);

                    // Show comparison button after route is loaded
                    setTimeout(() => {
                        if (confirm("Показать сравнение с другими операторами для этого маршрута?")) {
                            showOperatorComparison();
                        }
                    }, 500);
                }
            }
        } else {
            console.error('Server error:', data.error);
            alert('Ошибка при получении маршрута: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        // Удаляем индикатор загрузки
        const loadingElement = document.getElementById('route-loading');
        if (loadingElement) {
            document.body.removeChild(loadingElement);
        }
        
        console.error('Error getting route:', error);
        alert('Ошибка при получении маршрута. Проверьте соединение с интернетом или попробуйте позже.');
    });
}

// Функция для расчета расстояния между двумя точками (по формуле гаверсинусов)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Радиус Земли в километрах
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c; // Расстояние в километрах
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}

// Показ формулы расчета
function showFormula() {
    if (!selectedFromOffice || !selectedToOffice) {
        alert("Пожалуйста, выберите оба офиса (отправка и получение).");
        return;
    }
    
    const carrierId = document.getElementById('selected-carrier').value;
    const carrier = <?php echo json_encode($carriers); ?>.find(c => c.id == carrierId);
    
    if (carrier) {
        // Получаем информацию о маршруте
        const fromOffice = offices.find(o => o.id == selectedFromOffice);
        const toOffice = offices.find(o => o.id == selectedToOffice);
        
        if (fromOffice && toOffice) {
            // Рассчитываем приблизительное расстояние (по прямой)
            const distance = calculateDistance(fromOffice.lat, fromOffice.lng, toOffice.lat, toOffice.lng);
            
            const formula = `
                <div style="padding: 15px;">
                    <h5>Формула расчета стоимости доставки</h5>
                    <p><strong>Оператор:</strong> ${carrier.name} (CDEK)</p>
                    <p><strong>Отделение отправки:</strong> Минск — ул. Центральная, 6</p>
                    <p><strong>Отделение получения:</strong> Белыничи — ул. Советская, 15</p>
                    <p><strong>Расстояние:</strong> 142.01 км (по прямой).</p>
                    <p><strong>Формула:</strong></p>
                    <p><strong>1. Базовая стоимость: ${carrier.base_cost} BYN</strong></p>
                    <p><strong>2. Стоимость за вес: Вес × ${carrier.cost_per_kg} BYN/кг</strong></p>
                    <p><strong>3. Стоимость за расстояние: Расстояние × ${carrier.cost_per_km} BYN/км</strong></p>
                    <p><strong>4. Дополнительные услуги:</strong></p>
                    <p>   - Страховка: 2% от текущей стоимости</p>
                    <p>   - Упаковка: 3.00 BYN</p>
                    <p>   - Хрупкая посылка: 1% от текущей стоимости</p>
                    <p><strong>Общая формула:</strong></p>
                    <p>Стоимость = Базовая стоимость + (Вес × Стоимость за кг) + (Расстояние × Стоимость за км)</p>
                    <p>Затем добавляются дополнительные услуги (страховка, упаковка, хрупкая посылка)</p>
                </div>
            `;
            
            // Показываем в модальном окне
            const modal = document.createElement('div');
            modal.innerHTML = formula;
            modal.style.position = 'fixed';
            modal.style.top = '50%';
            modal.style.left = '50%';
            modal.style.transform = 'translate(-50%, -50%)';
            modal.style.backgroundColor = 'white';
            modal.style.padding = '0';
            modal.style.border = '2px solid #007cba';
            modal.style.borderRadius = '10px';
            modal.style.zIndex = '10000';
            modal.style.maxWidth = '600px';
            modal.style.maxHeight = '80vh';
            modal.style.overflowY = 'auto';
            modal.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
            
            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'Закрыть';
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '10px';
            closeBtn.style.right = '10px';
            closeBtn.style.background = '#dc3545';
            closeBtn.style.color = 'white';
            closeBtn.style.border = 'none';
            closeBtn.style.borderRadius = '50%';
            closeBtn.style.width = '30px';
            closeBtn.style.height = '30px';
            closeBtn.style.cursor = 'pointer';
            closeBtn.onclick = function() { document.body.removeChild(backdrop); document.body.removeChild(modal); };
            
            // Создаем затемнение фона
            const backdrop = document.createElement('div');
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
            backdrop.style.backgroundColor = 'rgba(0,0,0,0.5)';
            backdrop.style.zIndex = '9999';
            backdrop.onclick = function() { document.body.removeChild(backdrop); document.body.removeChild(modal); };
            
            modal.appendChild(closeBtn);
            document.body.appendChild(backdrop);
            document.body.appendChild(modal);
        }
    }
}

// Функция для переключения полей в зависимости от типа отправления
function toggleFields(type) {
    const weightDiv = document.getElementById('weight-div');
    const letterDiv = document.getElementById('letter-div');
    
    if (type === 'letter') {
        weightDiv.style.display = 'none';
        letterDiv.style.display = 'block';
    } else {
        weightDiv.style.display = 'block';
        letterDiv.style.display = 'none';
    }
}

// Функция для перенаправления на форму заказа
function redirectToOrderForm() {
    if (!selectedFromOffice || !selectedToOffice) {
        alert("Пожалуйста, выберите оба офиса (отправка и получение).");
        return;
    }
    
    // Собираем данные формы
    const formData = new FormData(document.getElementById('calculation-form'));
    const packageType = formData.get('package_type');
    let weight;
    
    if (packageType === 'letter') {
        const letterCount = parseInt(formData.get('letter_count') || 1);
        weight = letterCount * 0.02; // вес одного письма 0.02 кг
    } else {
        weight = parseFloat(formData.get('weight'));
    }
    
    const insurance = formData.get('insurance') ? 1 : 0;
    const packaging = formData.get('packaging') ? 1 : 0;
    const fragile = formData.get('fragile') ? 1 : 0;
    const carrierId = document.getElementById('selected-carrier').value;
    
    // Вычисляем стоимость с помощью AJAX
    const calculateData = {
        carrier_id: carrierId,
        from_office: selectedFromOffice,
        to_office: selectedToOffice,
        weight: weight,
        package_type: packageType,
        insurance: insurance ? 1 : 0,
        packaging: packaging ? 1 : 0,
        fragile: fragile ? 1 : 0,
        letter_count: parseInt(formData.get('letter_count') || 1)
    };
    
    fetch('calculate_cost.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: Object.keys(calculateData).map(key => `${encodeURIComponent(key)}=${encodeURIComponent(calculateData[key])}`).join('&')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем скрытое поле с рассчитанной стоимостью
            document.getElementById('calculated-cost').value = data.cost;
            
            // Перенаправляем на форму заказа с параметрами
            const url = `order_form.php?carrier=${carrierId}&weight=${weight}&cost=${data.cost}&from=${selectedFromOffice}&to=${selectedToOffice}&package_type=${packageType}&insurance=${insurance}&packaging=${packaging}&fragile=${fragile}`;
            window.location.href = url;
        } else {
            alert('Ошибка при расчете стоимости: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Error calculating cost:', error);
        alert('Ошибка при расчете стоимости. Попробуйте снова.');
    });
}

// Функция для динамического расчета стоимости
function calculateCost() {
    if (!selectedFromOffice || !selectedToOffice) {
        document.getElementById('dynamic-cost').textContent = '0.00';
        return;
    }

    // Собираем данные формы
    const packageType = document.querySelector('select[name="package_type"]').value;
    let weight;

    if (packageType === 'letter') {
        const letterCount = parseInt(document.querySelector('input[name="letter_count"]').value) || 1;
        weight = letterCount * 0.02; // вес одного письма 0.02 кг
    } else {
        weight = parseFloat(document.querySelector('input[name="weight"]').value) || 1;
    }

    const insurance = document.querySelector('input[name="insurance"]').checked ? 1 : 0;
    const packaging = document.querySelector('input[name="packaging"]').checked ? 1 : 0;
    const fragile = document.querySelector('input[name="fragile"]').checked ? 1 : 0;
    const carrierId = document.getElementById('selected-carrier').value;

    // Вычисляем стоимость с помощью AJAX
    const calculateData = {
        carrier_id: carrierId,
        from_office: selectedFromOffice,
        to_office: selectedToOffice,
        weight: weight,
        package_type: packageType,
        insurance: insurance ? 1 : 0,
        packaging: packaging ? 1 : 0,
        fragile: fragile ? 1 : 0,
        letter_count: parseInt(document.querySelector('input[name="letter_count"]').value) || 1
    };

    fetch('calculate_cost.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: Object.keys(calculateData).map(key => `${encodeURIComponent(key)}=${encodeURIComponent(calculateData[key])}`).join('&')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем отображение стоимости
            document.getElementById('dynamic-cost').textContent = data.cost.toFixed(2);
            
            // Обновляем скрытое поле с рассчитанной стоимостью
            document.getElementById('calculated-cost').value = data.cost;
        } else {
            console.error('Error calculating cost:', data.error);
            document.getElementById('dynamic-cost').textContent = 'Ошибка';
        }
    })
    .catch(error => {
        console.error('Error calculating cost:', error);
        document.getElementById('dynamic-cost').textContent = 'Ошибка';
    });
}

// Функция для отображения сравнения операторов
function showOperatorComparison() {
    if (!selectedFromOffice || !selectedToOffice) {
        alert("Пожалуйста, выберите оба офиса (отправка и получение).");
        return;
    }

    // Собираем данные формы
    const formData = new FormData(document.getElementById('calculation-form'));
    const packageType = formData.get('package_type');
    let weight;

    if (packageType === 'letter') {
        const letterCount = parseInt(formData.get('letter_count') || 1);
        weight = letterCount * 0.02; // вес одного письма 0.02 кг
    } else {
        weight = parseFloat(formData.get('weight')) || 1;
    }

    const insurance = formData.get('insurance') ? 1 : 0;
    const packaging = formData.get('packaging') ? 1 : 0;
    const fragile = formData.get('fragile') ? 1 : 0;
    const carrierId = document.getElementById('selected-carrier').value;
    const letterCount = parseInt(formData.get('letter_count') || 1);

    // Показываем индикатор загрузки
    document.getElementById('operator-comparison-results').innerHTML = '<div class="text-center p-4">Загрузка сравнения операторов...</div>';
    document.getElementById('operator-comparison-modal').style.display = 'flex';

    // Отправляем запрос на сервер для получения сравнения
    const comparisonData = {
        from_office_id: selectedFromOffice,
        to_office_id: selectedToOffice,
        weight: weight,
        package_type: packageType,
        insurance: insurance,
        packaging: packaging,
        fragile: fragile,
        letter_count: letterCount
    };

    fetch('get_operator_comparison.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: Object.keys(comparisonData).map(key => `${encodeURIComponent(key)}=${encodeURIComponent(comparisonData[key])}`).join('&')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayOperatorComparison(data.results);
        } else {
            document.getElementById('operator-comparison-results').innerHTML = `<div class="alert alert-danger">Ошибка: ${data.error}</div>`;
        }
    })
    .catch(error => {
        console.error('Error getting operator comparison:', error);
        document.getElementById('operator-comparison-results').innerHTML = '<div class="alert alert-danger">Ошибка при загрузке сравнения операторов</div>';
    });
}

// Функция для отображения результатов сравнения
function displayOperatorComparison(results) {
    if (results.length === 0) {
        document.getElementById('operator-comparison-results').innerHTML = '<div class="alert alert-info">Нет доступных операторов для этого маршрута</div>';
        return;
    }

    let html = '<div class="comparison-list">';
    
    results.forEach(result => {
        const carrier = result.carrier;
        html += `
            <div class="operator-item" style="background: ${carrier.color || '#f8f9fa'}; color: ${getContrastColor(carrier.color || '#f8f9fa')};">
                <div class="operator-info">
                    <div class="operator-name">${carrier.name}</div>
                    <div class="operator-stats">
                        <div>
                            <span>Стоимость</span>
                            <span>${result.cost} BYN</span>
                        </div>
                        <div>
                            <span>Время</span>
                            <span>${formatDeliveryTime(result.hours)}</span>
                        </div>
                        <div>
                            <span>Расстояние</span>
                            <span>${Math.round(result.distance)} км</span>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary switch-operator-btn" onclick="switchToOperator(${carrier.id}, ${result.from_office_id}, ${result.to_office_id}, ${result.cost})">
                    Выбрать
                </button>
            </div>
        `;
    });
    
    html += '</div>';
    document.getElementById('operator-comparison-results').innerHTML = html;
}

// Функция для получения контрастного цвета текста
function getContrastColor(hexColor) {
    // Remove # if present
    const hex = hexColor.replace('#', '');
    
    // Convert hex to RGB
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    
    // Calculate luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
    // Return black for light backgrounds, white for dark backgrounds
    return luminance > 0.5 ? '#000000' : '#ffffff';
}

// Функция для переключения на другого оператора
function switchToOperator(carrierId, fromOfficeId, toOfficeId, cost) {
    // Обновляем выбранный оператор
    document.getElementById('selected-carrier').value = carrierId;
    
    // Находим имя оператора
    const carrierCards = document.querySelectorAll('.carrier-card');
    let carrierName = '';
    carrierCards.forEach(card => {
        if (parseInt(card.getAttribute('onclick').match(/\d+/)[0]) === carrierId) {
            carrierName = card.querySelector('h4').textContent;
        }
    });
    
    document.getElementById('carrier-name').textContent = carrierName;
    
    // Обновляем офисы
    document.getElementById('selected-from').value = fromOfficeId;
    document.getElementById('selected-to').value = toOfficeId;
    
    // Обновляем отображение офисов
    const office = offices.find(o => o.id == fromOfficeId);
    if (office) {
        document.getElementById('selected-from-text').textContent = `${office.city} — ${office.address}`;
        document.getElementById('selected-from-container').style.display = 'block';
        selectedFromOffice = fromOfficeId;
    }
    
    const toOffice = offices.find(o => o.id == toOfficeId);
    if (toOffice) {
        document.getElementById('selected-to-text').textContent = `${toOffice.city} — ${toOffice.address}`;
        document.getElementById('selected-to-container').style.display = 'block';
        selectedToOffice = toOfficeId;
    }
    
    // Обновляем стоимость
    document.getElementById('calculated-cost').value = cost;
    document.getElementById('dynamic-cost').textContent = cost.toFixed(2);
    
    // Загружаем офисы для нового оператора
    loadOffices(carrierId);
    
    // Закрываем модальное окно
    closeComparisonModal();
    
    // Активируем кнопки
    document.getElementById('show-route-btn').disabled = false;
    document.getElementById('calculate-btn').disabled = false;
}

// Функция для закрытия модального окна сравнения
function closeComparisonModal() {
    document.getElementById('operator-comparison-modal').style.display = 'none';
}

// Поиск офисов
function setupSearch() {
    // Поиск для отправки
    const searchFrom = document.getElementById('search-from');
    searchFrom.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOffices = offices.filter(office => 
            office.city.toLowerCase().includes(searchTerm) || 
            office.address.toLowerCase().includes(searchTerm)
        );
        
        const resultsContainer = document.getElementById('search-results-from');
        resultsContainer.innerHTML = filteredOffices.map(office => 
            `<div class="p-2 border-bottom" style="cursor: pointer;" onclick="selectFromOffice(${office.id}, '${office.city}', '${office.address.replace(/'/g, "\\'")}')">
                <strong>${office.city}</strong> — ${office.address}
            </div>`
        ).join('');
    });
    
    // Поиск для получения
    const searchTo = document.getElementById('search-to');
    searchTo.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOffices = offices.filter(office => 
            office.city.toLowerCase().includes(searchTerm) || 
            office.address.toLowerCase().includes(searchTerm)
        );
        
        const resultsContainer = document.getElementById('search-results-to');
        resultsContainer.innerHTML = filteredOffices.map(office => 
            `<div class="p-2 border-bottom" style="cursor: pointer;" onclick="selectToOffice(${office.id}, '${office.city}', '${office.address.replace(/'/g, "\\'")}')">
                <strong>${office.city}</strong> — ${office.address}
            </div>`
        ).join('');
    });
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Если уже выбран оператор, загружаем офисы
    const selectedCarrier = document.getElementById('selected-carrier').value;
    if (selectedCarrier) {
        selectedCarrierId = parseInt(selectedCarrier);
        loadOffices(selectedCarrierId);
    }
    
    setupSearch();
    
    // Установка начальных значений для полей
    const fromValue = document.getElementById('selected-from').value;
    const toValue = document.getElementById('selected-to').value;
    
    if (fromValue) {
        // Если есть выбранное значение отправки, показываем его
        const fromOffice = offices.find(o => o.id == fromValue);
        if (fromOffice) {
            document.getElementById('selected-from-text').textContent = `${fromOffice.city} — ${fromOffice.address}`;
            document.getElementById('selected-from-container').style.display = 'block';
            selectedFromOffice = parseInt(fromValue);
            document.getElementById('search-to').disabled = false;
            document.getElementById('find-nearest-to-btn').disabled = false;
        }
    }
    
    if (toValue) {
        // Если есть выбранное значение получения, показываем его
        const toOffice = offices.find(o => o.id == toValue);
        if (toOffice) {
            document.getElementById('selected-to-text').textContent = `${toOffice.city} — ${toOffice.address}`;
            document.getElementById('selected-to-container').style.display = 'block';
            selectedToOffice = parseInt(toValue);
        }
    }
    
    // Активируем кнопки если выбраны оба офиса
    if (selectedFromOffice && selectedToOffice) {
        document.getElementById('show-route-btn').disabled = false;
        document.getElementById('calculate-btn').disabled = false;
        document.getElementById('show-formula-btn').disabled = false;
    }
    
    // Prevent scrolling to top when filter links are clicked
    const filterLinks = document.querySelectorAll('.btn-group a');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Allow the navigation but prevent the default scroll behavior
            // We'll maintain the scroll position by saving it before navigation
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
    });

    // Restore scroll position if available
    const scrollPosition = sessionStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, parseInt(scrollPosition));
        sessionStorage.removeItem('scrollPosition');
    }
});
</script>
</body>
</html>