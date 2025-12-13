<?php require 'db.php';
if (!isset($_SESSION['user'])) header('Location: index.php');
$user = $_SESSION['user'];

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
                $cost = $carrier['base_cost'] 
                      + $weight * $carrier['cost_per_kg'] 
                      + $distance * $carrier['cost_per_km'];

                if ($insurance) $cost *= 1.02;
                if ($type === 'letter') $cost = max($cost, 2.5);

                $cost = round($cost, 2);
                $hours = round($base_hours, 1);

                $result = [
                    'carrier' => $carrier,
                    'cost' => $cost,
                    'hours' => $hours,
                    'distance' => $distance
                ];
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
    <style>
        #map { height: 500px; width: 100%; }
        .office-marker { cursor: pointer; }
        .search-container { margin-bottom: 15px; }
        .selected-office { background-color: #e7f3ff; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .change-button { margin-left: 10px; }
        .office-marker-icon {
            background-color: #fff;
            border: 2px solid #007cba;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .office-marker-selected {
            background-color: #ff6b6b !important;
            border-color: #d63031 !important;
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
    <h2 class="text-center text-white mb-4">Выберите оператора</h2>
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
            
            <!-- Поле для ввода адреса получателя -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label>Адрес получателя</label>
                    <input type="text" id="recipient-address" class="form-control" placeholder="Введите адрес получателя">
                </div>
            </div>
            
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
                    
                    <button type="button" class="btn btn-info mt-2" onclick="findNearestToOffice()" id="find-nearest-to-btn" disabled>Выбрать ближайшее (получение)</button>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-success btn-lg" onclick="showRoute()" id="show-route-btn" disabled>Показать маршрут</button>
                    <button type="button" class="btn btn-primary ms-2" onclick="showFormula()" id="show-formula-btn" disabled>Формула расчета</button>
                </div>
            </div>
            
            <!-- Форма для остальных параметров -->
            <form method="POST" id="calculation-form">
                <input type="hidden" name="carrier" id="selected-carrier" value="<?=$_POST['carrier'] ?? $_GET['carrier'] ?? ''?>">
                <input type="hidden" name="from" id="selected-from" value="<?=$_POST['from'] ?? $_GET['from'] ?? ''?>">
                <input type="hidden" name="to" id="selected-to" value="<?=$_POST['to'] ?? $_GET['to'] ?? ''?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Тип отправления</label>
                        <select name="package_type" class="form-select" onchange="toggleFields(this.value)" required>
                            <option value="parcel" <?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'parcel') ? 'selected' : '')?>>Посылка</option>
                            <option value="letter" <?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? 'selected' : '')?>>Письмо</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="weight-div" style="<?=((($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? 'display:none;' : '')?>">
                        <label>Вес (кг)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" value="<?=$_POST['weight'] ?? $_GET['weight'] ?? '1'?>" min="0.1" required>
                    </div>

                    <div class="col-md-4" id="letter-div" style="<?=(($_POST['package_type'] ?? $_GET['package_type'] ?? '') == 'letter') ? '' : 'display:none;'?>">
                        <label>Количество писем</label>
                        <input type="number" name="letter_count" class="form-control" value="<?=$_POST['letter_count'] ?? $_GET['letter_count'] ?? '1'?>" min="1" max="50">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="insurance" class="form-check-input" id="ins" <?=((isset($_POST['insurance']) || isset($_GET['insurance'])) ? 'checked' : '')?>>
                            <label class="form-check-label" for="ins">Страховка (+2%)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg mt-4 w-100" id="calculate-btn" disabled>Рассчитать</button>
            </form>
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
    document.getElementById('find-nearest-to-btn').disabled = false;
    
    // Активируем кнопку показа маршрута если выбраны оба офиса
    if (selectedToOffice) {
        document.getElementById('show-route-btn').disabled = false;
        document.getElementById('calculate-btn').disabled = false;
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
    }
}

// Изменение офиса отправки
function changeFromOffice() {
    selectedFromOffice = null;
    document.getElementById('selected-from').value = '';
    document.getElementById('selected-from-container').style.display = 'none';
    document.getElementById('show-route-btn').disabled = true;
    document.getElementById('calculate-btn').disabled = true;
    
    // Деактивируем поле для получения
    document.getElementById('search-to').disabled = true;
    document.getElementById('find-nearest-to-btn').disabled = true;
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

// Поиск ближайшего офиса получения
function findNearestToOffice() {
    const recipientAddress = document.getElementById('recipient-address').value.trim();
    
    if (!recipientAddress) {
        alert("Пожалуйста, сначала введите адрес получателя.");
        return;
    }
    
    // В реальном приложении здесь должен быть вызов геокодера для получения координат адреса
    // Для демонстрации просто найдем ближайший офис к отправке
    if (selectedFromOffice) {
        // Находим ближайший офис к отправке (в реальности это будет к адресу получателя)
        let nearestOffice = null;
        let minDistance = Infinity;
        
        const fromOffice = offices.find(o => o.id == selectedFromOffice);
        if (fromOffice) {
            offices.forEach(office => {
                if (office.lat && office.lng && office.id != selectedFromOffice) {
                    const distance = Math.sqrt(
                        Math.pow(office.lat - fromOffice.lat, 2) + 
                        Math.pow(office.lng - fromOffice.lng, 2)
                    );
                    
                    if (distance < minDistance) {
                        minDistance = distance;
                        nearestOffice = office;
                    }
                }
            });
        }
        
        if (nearestOffice) {
            selectToOffice(nearestOffice.id, nearestOffice.city, nearestOffice.address);
            // Центрируем карту на выбранном офисе
            map.setView([nearestOffice.lat, nearestOffice.lng], 13);
        }
    }
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
    }
    
    // Получаем координаты офисов
    const fromOffice = offices.find(o => o.id == selectedFromOffice);
    const toOffice = offices.find(o => o.id == selectedToOffice);
    
    if (fromOffice && toOffice) {
        // В реальном приложении здесь должен быть вызов API для получения маршрута
        // Для демонстрации рисуем прямую линию
        const routeCoords = [
            [fromOffice.lat, fromOffice.lng],
            [toOffice.lat, toOffice.lng]
        ];
        
        routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);
        
        // Центрируем карту на маршруте
        const bounds = L.latLngBounds(routeCoords);
        map.fitBounds(bounds, {padding: [50, 50]});
        
        // Показываем информацию о маршруте
        const distance = Math.sqrt(
            Math.pow(toOffice.lat - fromOffice.lat, 2) + 
            Math.pow(toOffice.lng - fromOffice.lng, 2)
        ) * 111; // Приблизительное расстояние в км
        
        alert(`Маршрут построен. Приблизительное расстояние: ${distance.toFixed(2)} км.`);
    }
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
            // В реальном приложении здесь будет информация из базы данных о фактическом маршруте
            const distance = Math.sqrt(
                Math.pow(toOffice.lat - fromOffice.lat, 2) + 
                Math.pow(toOffice.lng - fromOffice.lng, 2)
            ) * 111; // Приблизительное расстояние в км
            
            const formula = `
                <h5>Формула расчета стоимости доставки</h5>
                <p><strong>Оператор:</strong> ${carrier.name}</p>
                <p><strong>Отделение отправки:</strong> ${fromOffice.city} — ${fromOffice.address}</p>
                <p><strong>Отделение получения:</strong> ${toOffice.city} — ${toOffice.address}</p>
                <p><strong>Расстояние:</strong> ${distance.toFixed(2)} км</p>
                <p><strong>Формула:</strong></p>
                <p>Стоимость = Базовая стоимость + (Вес × Стоимость за кг) + (Расстояние × Стоимость за км)</p>
                <p>Стоимость = ${carrier.base_cost} + (Вес × ${carrier.cost_per_kg}) + (${distance.toFixed(2)} × ${carrier.cost_per_km})</p>
                <p><em>Время доставки: Расстояние / Скорость оператора (${carrier.speed_kmh} км/ч)</em></p>
            `;
            
            // Показываем в модальном окне или алерте
            const div = document.createElement('div');
            div.innerHTML = formula;
            div.style.position = 'fixed';
            div.style.top = '50%';
            div.style.left = '50%';
            div.style.transform = 'translate(-50%, -50%)';
            div.style.backgroundColor = 'white';
            div.style.padding = '20px';
            div.style.border = '2px solid #ccc';
            div.style.borderRadius = '10px';
            div.style.zIndex = '10000';
            div.style.maxWidth = '500px';
            div.style.maxHeight = '80vh';
            div.style.overflowY = 'auto';
            
            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'Закрыть';
            closeBtn.style.display = 'block';
            closeBtn.style.marginTop = '15px';
            closeBtn.style.padding = '5px 10px';
            closeBtn.onclick = function() { document.body.removeChild(div); };
            
            div.appendChild(closeBtn);
            document.body.appendChild(div);
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