<?php 
require 'db.php';
if (!isset($_SESSION['user'])) header('Location: index.php');
$user = $_SESSION['user'];

$carriers = $db->query("SELECT * FROM carriers")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор доставки с картой</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" rel="stylesheet"/>
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        #map { 
            height: 500px; 
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
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
        .route-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        .office-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
        .office-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .office-item:hover {
            background-color: #e9ecef;
        }
        .office-item:last-child {
            border-bottom: none;
        }
        .selected-office {
            background-color: #d4edda;
        }
        .carrier-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .carrier-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .selected {
            border: 3px solid #28a745;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .result-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-dark bg-primary shadow-lg">
    <div class="container-fluid">
        <a class="navbar-brand">Калькулятор доставки с картой</a>
        <div>
            <a href="profile.php" class="btn btn-light me-2">Профиль</a>
            <a href="order_form_map.php" class="btn btn-success me-2">Оформить заказ</a>
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
                 onclick="selectCarrier(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name']) ?>', '<?= $c['color'] ?>', <?= $c['id'] ?>)">
                <h4><?= htmlspecialchars($c['name']) ?></h4>
                <small>до <?= $c['max_weight'] ?> кг</small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card mt-5 shadow-lg" id="calc-form" style="display:none;">
        <div class="card-body">
            <h4 class="text-center mb-4">Расчёт для: <strong id="carrier-name"></strong></h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Отделение отправления:</label>
                        <select class="form-select" id="from-select" onchange="updateFromOffice()">
                            <option value="">Выберите отделение</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Отделение получения:</label>
                        <select class="form-select" id="to-select" onchange="updateToOffice()">
                            <option value="">Выберите отделение</option>
                        </select>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Тип отправления</label>
                            <select name="package_type" class="form-select" id="package-type" onchange="toggleFields(this.value)" required>
                                <option value="parcel">Посылка</option>
                                <option value="letter">Письмо</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="weight-div">
                            <label class="form-label fw-bold">Вес (кг)</label>
                            <input type="number" step="0.1" name="weight" class="form-control" id="weight" value="1" min="0.1" required>
                        </div>

                        <div class="col-md-6" id="letter-div" style="display:none;">
                            <label class="form-label fw-bold">Количество писем</label>
                            <input type="number" name="letter_count" class="form-control" id="letter-count" value="1" min="1" max="50">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="form-check">
                            <input type="checkbox" name="insurance" class="form-check-input" id="insurance">
                            <label class="form-check-label" for="insurance">Страховка (+2%)</label>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-success btn-lg mt-3 w-100" onclick="calculateRoute()">Рассчитать маршрут</button>
                </div>
                
                <div class="col-md-6">
                    <div id="map"></div>
                    <div class="route-info" id="route-info">
                        <h5>Информация о маршруте:</h5>
                        <p><strong>Расстояние:</strong> <span id="route-distance">-</span> км</p>
                        <p><strong>Время в пути:</strong> <span id="route-duration">-</span> ч</p>
                        <p><strong>Стоимость:</strong> <span id="route-cost">-</span> BYN</p>
                        <div id="route-instructions" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="result-container" style="display:none;">
        <div class="result-box">
            <h2 id="result-cost">0 BYN</h2>
            <p class="lead" id="result-info">Время доставки: -, Расстояние: - км</p>
            <a href="#" id="order-link" class="btn btn-light btn-lg">Оформить заказ</a>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
let selectedCarrier = null;
let fromOffice = null;
let toOffice = null;
let map = null;
let routingControl = null;
let markers = [];
let selectedMarker = null;
let currentCarrierId = null;

function selectCarrier(id, name, color, carrierId) {
    if (selectedCarrier) selectedCarrier.classList.remove('selected');
    const card = event.currentTarget;
    card.classList.add('selected');
    selectedCarrier = card;

    document.getElementById('carrier-name').textContent = name;
    document.getElementById('calc-form').style.display = 'block';
    currentCarrierId = carrierId;

    // Initialize map if not already done
    if (!map) {
        initMap();
    }

    // Load offices for this carrier
    loadOfficesForCarrier(id);
}

function initMap() {
    // Initialize the map centered on Belarus
    map = L.map('map').setView([53.904133, 27.557545], 7); // Center on Belarus

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add click event to map for selecting coordinates
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Reverse geocode to get address
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Create a temporary marker for the clicked location
                if (selectedMarker) {
                    map.removeLayer(selectedMarker);
                }
                
                selectedMarker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(address)
                    .openPopup();
                
                // Ask user if this is from or to location
                if (!fromOffice) {
                    if (confirm(`Вы хотите выбрать это место как отделение отправления?\n${address}`)) {
                        // Add as a custom location (not an office)
                        fromOffice = { lat: lat, lng: lng, address: address, id: 'custom' };
                        // We'll need to update the select dropdown to include this custom location
                        updateFromSelectWithCustom(address, lat, lng);
                    }
                } else if (!toOffice) {
                    if (confirm(`Вы хотите выбрать это место как отделение получения?\n${address}`)) {
                        // Add as a custom location (not an office)
                        toOffice = { lat: lat, lng: lng, address: address, id: 'custom' };
                        // We'll need to update the select dropdown to include this custom location
                        updateToSelectWithCustom(address, lat, lng);
                    }
                }
            })
            .catch(error => {
                console.error('Error with reverse geocoding:', error);
                alert('Не удалось получить адрес для этой точки');
            });
    });
}

function updateFromSelectWithCustom(address, lat, lng) {
    const select = document.getElementById('from-select');
    const option = document.createElement('option');
    option.value = `custom_${lat}_${lng}`;
    option.text = `Пользовательская точка: ${address}`;
    option.dataset.lat = lat;
    option.dataset.lng = lng;
    select.appendChild(option);
    select.value = option.value;
    updateFromOffice();
}

function updateToSelectWithCustom(address, lat, lng) {
    const select = document.getElementById('to-select');
    const option = document.createElement('option');
    option.value = `custom_${lat}_${lng}`;
    option.text = `Пользовательская точка: ${address}`;
    option.dataset.lat = lat;
    option.dataset.lng = lng;
    select.appendChild(option);
    select.value = option.value;
    updateToOffice();
}

function loadOfficesForCarrier(carrierId) {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Clear existing options
    const fromSelect = document.getElementById('from-select');
    const toSelect = document.getElementById('to-select');
    fromSelect.innerHTML = '<option value="">Выберите отделение</option>';
    toSelect.innerHTML = '<option value="">Выберите отделение</option>';

    fetch(`get_offices.php?carrier=${carrierId}`)
        .then(response => response.json())
        .then(data => {
            // Add options to both select elements
            data.forEach(office => {
                if (office.lat && office.lng) {
                    const fromOption = document.createElement('option');
                    fromOption.value = office.id;
                    fromOption.text = `${office.city}, ${office.address}`;
                    fromOption.dataset.lat = office.lat;
                    fromOption.dataset.lng = office.lng;
                    fromSelect.appendChild(fromOption);

                    const toOption = document.createElement('option');
                    toOption.value = office.id;
                    toOption.text = `${office.city}, ${office.address}`;
                    toOption.dataset.lat = office.lat;
                    toOption.dataset.lng = office.lng;
                    toSelect.appendChild(toOption);

                    // Add markers for all offices of this carrier
                    const marker = L.marker([parseFloat(office.lat), parseFloat(office.lng)])
                        .addTo(map)
                        .bindPopup(`<b>${office.carrier_name}</b><br>${office.city}, ${office.address}`)
                        .on('click', function() {
                            if (!fromOffice) {
                                if (confirm(`Выбрать это отделение как отправление?\n${office.city}, ${office.address}`)) {
                                    fromSelect.value = office.id;
                                    fromOffice = office;
                                    updateFromOffice();
                                }
                            } else if (!toOffice) {
                                if (confirm(`Выбрать это отделение как получение?\n${office.city}, ${office.address}`)) {
                                    toSelect.value = office.id;
                                    toOffice = office;
                                    updateToOffice();
                                }
                            }
                        });
                    markers.push(marker);
                }
            });
        })
        .catch(error => {
            console.error('Error loading offices:', error);
        });
}

function updateFromOffice() {
    const select = document.getElementById('from-select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        if (selectedOption.dataset.lat && selectedOption.dataset.lng) {
            fromOffice = {
                id: selectedOption.value,
                lat: parseFloat(selectedOption.dataset.lat),
                lng: parseFloat(selectedOption.dataset.lng),
                address: selectedOption.text
            };
        } else if (selectedOption.value.startsWith('custom_')) {
            // Custom location
            const coords = selectedOption.value.split('_');
            fromOffice = {
                id: 'custom',
                lat: parseFloat(coords[1]),
                lng: parseFloat(coords[2]),
                address: selectedOption.text
            };
        }
    }
}

function updateToOffice() {
    const select = document.getElementById('to-select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        if (selectedOption.dataset.lat && selectedOption.dataset.lng) {
            toOffice = {
                id: selectedOption.value,
                lat: parseFloat(selectedOption.dataset.lat),
                lng: parseFloat(selectedOption.dataset.lng),
                address: selectedOption.text
            };
        } else if (selectedOption.value.startsWith('custom_')) {
            // Custom location
            const coords = selectedOption.value.split('_');
            toOffice = {
                id: 'custom',
                lat: parseFloat(coords[1]),
                lng: parseFloat(coords[2]),
                address: selectedOption.text
            };
        }
    }
}

function calculateRoute() {
    if (!fromOffice || !toOffice) {
        alert('Пожалуйста, выберите оба отделения!');
        return;
    }

    // Remove previous route if exists
    if (routingControl) {
        map.removeControl(routingControl);
    }

    // Get coordinates for route calculation
    let fromLat = fromOffice.lat;
    let fromLng = fromOffice.lng;
    let toLat = toOffice.lat;
    let toLng = toOffice.lng;

    // Create routing control
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(fromLat, fromLng),
            L.latLng(toLat, toLng)
        ],
        routeWhileDragging: false,
        show: true,
        createMarker: function(i, wp, n) {
            let marker;
            if (i === 0) {
                // From marker
                marker = L.marker(wp.latLng, { 
                    icon: L.divIcon({
                        className: 'office-marker',
                        html: '<div style="background: #28a745; color: white; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">A</div>',
                        iconSize: [30, 30]
                    })
                });
            } else {
                // To marker
                marker = L.marker(wp.latLng, { 
                    icon: L.divIcon({
                        className: 'office-marker',
                        html: '<div style="background: #dc3545; color: white; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">B</div>',
                        iconSize: [30, 30]
                    })
                });
            }
            return marker;
        }
    }).addTo(map);

    // Calculate route and update info when route is found
    routingControl.on('routesfound', function(e) {
        const route = e.routes[0];
        const distanceKm = route.summary.totalDistance / 1000; // Convert to km
        const durationHours = route.summary.totalTime / 3600; // Convert to hours

        // Get carrier info for cost calculation
        const packageType = document.getElementById('package-type').value;
        const weight = packageType === 'letter' ? 
            parseFloat(document.getElementById('letter-count').value) * 0.02 : 
            parseFloat(document.getElementById('weight').value);
        const insurance = document.getElementById('insurance').checked;

        // Fetch carrier details to calculate cost
        fetch(`get_carrier.php?id=${currentCarrierId}`)
            .then(response => response.json())
            .then(carrier => {
                // Calculate cost based on distance, weight, and carrier rates
                let cost = carrier.base_cost + (weight * carrier.cost_per_kg) + (distanceKm * carrier.cost_per_km);
                
                if (insurance) cost *= 1.02;
                if (packageType === 'letter') cost = Math.max(cost, 2.5);
                
                cost = Math.round(cost * 100) / 100; // Round to 2 decimal places

                // Update route info display
                document.getElementById('route-distance').textContent = distanceKm.toFixed(2);
                document.getElementById('route-duration').textContent = durationHours.toFixed(2);
                document.getElementById('route-cost').textContent = cost.toFixed(2);
                
                // Show route instructions
                let instructionsHtml = '<h6>Инструкции по маршруту:</h6><ol>';
                route.instructions.forEach(instruction => {
                    instructionsHtml += `<li>${instruction.text} (${(instruction.distance/1000).toFixed(2)} км)</li>`;
                });
                instructionsHtml += '</ol>';
                document.getElementById('route-instructions').innerHTML = instructionsHtml;
                
                document.getElementById('route-info').style.display = 'block';
                
                // Update result display
                document.getElementById('result-cost').textContent = cost.toFixed(2) + ' BYN';
                document.getElementById('result-info').textContent = `Время доставки: ~${durationHours.toFixed(2)} ч, Расстояние: ${distanceKm.toFixed(2)} км`;
                
                // Update order link with parameters
                const orderLink = document.getElementById('order-link');
                orderLink.href = `order_form_map.php?carrier=${currentCarrierId}&weight=${weight}&cost=${cost}&from=${fromOffice.id}&to=${toOffice.id}&distance=${distanceKm}`;
                
                document.getElementById('result-container').style.display = 'block';
                
                // Scroll to route info
                document.getElementById('route-info').scrollIntoView({ behavior: 'smooth' });
            });
    });
}

function toggleFields(type) {
    const isLetter = type === 'letter';
    document.getElementById('weight-div').style.display = isLetter ? 'none' : 'block';
    document.getElementById('letter-div').style.display = isLetter ? 'block' : 'none';
}

// Theme functionality for cross-page consistency
function toggleTheme() {
    document.body.classList.toggle('dark');
    // Save theme preference in localStorage
    const isDark = document.body.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');

    // Apply theme to all iframes and child elements
    applyThemeToPage(isDark ? 'dark' : 'light');
}

function applyThemeToPage(theme) {
    // This function ensures theme consistency across the site
    if (theme === 'dark') {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
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
</body>
</html>