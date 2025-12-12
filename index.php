<?php
require_once 'db.php';

$operators = getAllOperators();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор доставки по Беларуси</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <!-- Leaflet Control Geocoder CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/distControlGeocoder.css" />
    
    <style>
        #map { 
            height: 600px; 
            width: 100%;
            border-radius: 8px;
        }
        
        .office-marker {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            cursor: pointer;
        }
        
        .route-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .comparison-table {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Калькулятор доставки по Беларуси</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 mb-4">
                    <h5>Параметры доставки</h5>
                    
                    <div class="mb-3">
                        <label for="operatorSelect" class="form-label">Выберите оператора</label>
                        <select class="form-select" id="operatorSelect">
                            <option value="">-- Выберите оператора --</option>
                            <?php foreach ($operators as $op): ?>
                                <option value="<?= $op['id'] ?>" data-color="<?= $op['color'] ?>" data-tariff="<?= $op['tariff_per_km'] ?>">
                                    <?= htmlspecialchars($op['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fromAddress" class="form-label">Адрес отправителя</label>
                        <input type="text" class="form-control" id="fromAddress" placeholder="Введите адрес отправителя">
                    </div>
                    
                    <div class="mb-3">
                        <button class="btn btn-primary w-100" id="findNearestFromBtn">Выбрать ближайшее отделение отправки</button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="toAddress" class="form-label">Адрес получателя</label>
                        <input type="text" class="form-control" id="toAddress" placeholder="Введите адрес получателя">
                    </div>
                    
                    <div class="mb-3">
                        <button class="btn btn-primary w-100" id="findNearestToBtn">Выбрать ближайшее отделение получения</button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="weight" class="form-label">Вес посылки (кг)</label>
                        <input type="number" class="form-control" id="weight" min="0.1" step="0.1" value="1.0">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="insurance">
                            <label class="form-check-label" for="insurance">Страховка (+2% от стоимости)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fragile">
                            <label class="form-check-label" for="fragile">Хрупкое (+1 руб)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="packaging">
                            <label class="form-check-label" for="packaging">Упаковка (+3 руб)</label>
                        </div>
                    </div>
                    
                    <button class="btn btn-success w-100" id="calculateBtn">Рассчитать стоимость</button>
                    <button class="btn btn-info w-100 mt-2" id="orderBtn" style="display:none;">Оформить заказ</button>
                </div>
                
                <div class="route-info" id="routeInfo" style="display:none;">
                    <h5>Информация о маршруте</h5>
                    <p><strong>Расстояние:</strong> <span id="distanceText">-</span> км</p>
                    <p><strong>Время в пути:</strong> <span id="timeText">-</span> мин</p>
                    <p><strong>Стоимость:</strong> <span id="priceText">-</span> руб</p>
                </div>
            </div>
            
            <div class="col-md-8">
                <div id="map"></div>
                
                <div class="mt-3 comparison-table" id="comparisonTable" style="display:none;">
                    <h5>Сравнение с другими операторами</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Оператор</th>
                                <th>Отделение отправки</th>
                                <th>Отделение получения</th>
                                <th>Расстояние (км)</th>
                                <th>Время (мин)</th>
                                <th>Стоимость (руб)</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody id="comparisonBody">
                            <!-- Comparison rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet Routing Machine -->
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <!-- Leaflet Control Geocoder -->
    <script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/distControlGeocoder.js"></script>
    
    <script>
        // Global variables
        let map;
        let routingControl;
        let markers = [];
        let offices = [];
        let selectedOperator = null;
        let fromOffice = null;
        let toOffice = null;
        let calculatedRoute = null;
        
        // Initialize the map
        function initMap() {
            // Create map centered on Belarus
            map = L.map('map').setView([53.9, 27.55], 7);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add geocoder control
            const geocoder = L.Control.Geocoder.nominatim();
            L.Control.geocoder({
                geocoder: geocoder
            }).on('markgeocode', function(e) {
                map.setView(e.geocode.center, 16);
                // You can add a marker here if needed
            }).addTo(map);
        }
        
        // Load offices for selected operator
        async function loadOfficesForOperator(operatorId) {
            // Clear existing markers
            clearMarkers();
            
            if (!operatorId) return;
            
            try {
                const response = await fetch(`get_offices.php?operator_id=${operatorId}`);
                const data = await response.json();
                
                offices = data.offices;
                
                // Add markers for each office
                offices.forEach(office => {
                    const marker = L.marker([office.lat, office.lon]).addTo(map);
                    marker.bindPopup(`<b>${office.title}</b><br>${office.address}<br><small>г. ${office.city}</small>`);
                    marker.on('click', function() {
                        selectOffice(office);
                    });
                    markers.push(marker);
                });
            } catch (error) {
                console.error('Error loading offices:', error);
            }
        }
        
        // Clear all markers from map
        function clearMarkers() {
            markers.forEach(marker => {
                map.removeLayer(marker);
            });
            markers = [];
        }
        
        // Select an office
        function selectOffice(office) {
            // Here we would implement logic to select office as either from or to
            // For now, we'll just log it
            console.log('Selected office:', office);
        }
        
        // Find nearest office to coordinates
        async function findNearestOffice(lat, lon, operatorId) {
            try {
                const response = await fetch(`get_nearest_office.php?lat=${lat}&lon=${lon}&operator_id=${operatorId}`);
                const data = await response.json();
                return data.office || null;
            } catch (error) {
                console.error('Error finding nearest office:', error);
                return null;
            }
        }
        
        // Calculate route using OSRM
        async function calculateRoute(fromLat, fromLon, toLat, toLon) {
            try {
                // Using demo OSRM server for car routing
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLon},${fromLat};${toLon},${toLat}?overview=full&geometries=geojson`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.code === 'Ok' && data.routes.length > 0) {
                    const route = data.routes[0];
                    return {
                        distance: route.distance / 1000, // Convert meters to km
                        duration: route.duration / 60, // Convert seconds to minutes
                        geometry: route.geometry
                    };
                } else {
                    throw new Error('Route not found');
                }
            } catch (error) {
                console.error('Error calculating route:', error);
                return null;
            }
        }
        
        // Calculate price based on distance and other factors
        function calculatePrice(distance, weight, insurance, fragile, packaging, tariffPerKm) {
            let price = distance * parseFloat(tariffPerKm);
            
            // Additional fees
            if (insurance) price += price * 0.02; // 2% of base cost
            if (fragile) price += 1; // +1 rub
            if (packaging) price += 3; // +3 rub
            
            return Math.round(price * 100) / 100; // Round to 2 decimals
        }
        
        // Show route on map
        function showRoute(routeData, fromOffice, toOffice) {
            // Remove existing route if any
            if (routingControl) {
                map.removeControl(routingControl);
            }
            
            // Create new routing control
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(fromOffice.lat, fromOffice.lon),
                    L.latLng(toOffice.lat, toOffice.lon)
                ],
                routeWhileDragging: false,
                showAlternatives: false,
                fitSelectedRoutes: true,
                lineOptions: {
                    styles: [{color: 'blue', opacity: 0.7, weight: 5}]
                }
            }).addTo(map);
        }
        
        // Compare with all operators
        async function compareWithAllOperators(fromLat, fromLon, toLat, toLon, weight, services) {
            const tbody = document.getElementById('comparisonBody');
            tbody.innerHTML = '<tr><td colspan="7">Загрузка...</td></tr>';
            
            try {
                const response = await fetch('compare_operators.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        from_lat: fromLat,
                        from_lon: fromLon,
                        to_lat: toLat,
                        to_lon: toLon,
                        weight: weight,
                        services: services
                    })
                });
                
                const data = await response.json();
                
                tbody.innerHTML = '';
                data.results.forEach(result => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${result.operator_name}</td>
                        <td>${result.from_office_title}</td>
                        <td>${result.to_office_title}</td>
                        <td>${result.distance.toFixed(2)}</td>
                        <td>${Math.round(result.duration)}</td>
                        <td>${result.price.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary select-operator-btn" 
                                data-operator-id="${result.operator_id}"
                                data-from-office-id="${result.from_office_id}"
                                data-to-office-id="${result.to_office_id}"
                                data-distance="${result.distance}"
                                data-duration="${result.duration}"
                                data-price="${result.price}">
                                Выбрать
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
                
                // Add event listeners to select buttons
                document.querySelectorAll('.select-operator-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const operatorId = this.getAttribute('data-operator-id');
                        const fromOfficeId = this.getAttribute('data-from-office-id');
                        const toOfficeId = this.getAttribute('data-to-office-id');
                        const distance = this.getAttribute('data-distance');
                        const duration = this.getAttribute('data-duration');
                        const price = this.getAttribute('data-price');
                        
                        // Update UI with selected operator
                        document.getElementById('operatorSelect').value = operatorId;
                        loadOfficesForOperator(operatorId);
                        
                        // Update route info
                        document.getElementById('distanceText').textContent = distance;
                        document.getElementById('timeText').textContent = duration;
                        document.getElementById('priceText').textContent = price;
                        
                        // Show order button
                        document.getElementById('orderBtn').style.display = 'block';
                        
                        // Store selected offices
                        fromOffice = { id: fromOfficeId };
                        toOffice = { id: toOfficeId };
                    });
                });
            } catch (error) {
                console.error('Error comparing operators:', error);
                tbody.innerHTML = '<tr><td colspan="7">Ошибка загрузки сравнения</td></tr>';
            }
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            
            // Operator selection
            document.getElementById('operatorSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                selectedOperator = {
                    id: this.value,
                    name: selectedOption.text,
                    color: selectedOption.getAttribute('data-color'),
                    tariff: selectedOption.getAttribute('data-tariff')
                };
                
                if (this.value) {
                    loadOfficesForOperator(this.value);
                } else {
                    clearMarkers();
                }
            });
            
            // Find nearest from office
            document.getElementById('findNearestFromBtn').addEventListener('click', async function() {
                if (!selectedOperator || !document.getElementById('fromAddress').value) {
                    alert('Пожалуйста, выберите оператора и введите адрес отправителя');
                    return;
                }
                
                // Geocode the address first
                const address = document.getElementById('fromAddress').value;
                try {
                    const geocodeUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1&countrycodes=BY`;
                    const geocodeResponse = await fetch(geocodeUrl);
                    const geocodeData = await geocodeResponse.json();
                    
                    if (geocodeData.length > 0) {
                        const lat = parseFloat(geocodeData[0].lat);
                        const lon = parseFloat(geocodeData[0].lon);
                        
                        // Find nearest office
                        const nearestOffice = await findNearestOffice(lat, lon, selectedOperator.id);
                        if (nearestOffice) {
                            fromOffice = nearestOffice;
                            alert(`Ближайшее отделение: ${nearestOffice.title}, ${nearestOffice.address}`);
                            
                            // Center map on the office
                            map.setView([nearestOffice.lat, nearestOffice.lon], 15);
                            
                            // Add temporary marker
                            L.marker([nearestOffice.lat, nearestOffice.lon])
                             .addTo(map)
                             .bindPopup(`<b>Отправка: ${nearestOffice.title}</b><br>${nearestOffice.address}`)
                             .openPopup();
                        } else {
                            alert('Не найдено ближайшее отделение для этого оператора');
                        }
                    } else {
                        alert('Не удалось найти координаты для указанного адреса');
                    }
                } catch (error) {
                    console.error('Error geocoding address:', error);
                    alert('Ошибка при определении координат адреса');
                }
            });
            
            // Find nearest to office
            document.getElementById('findNearestToBtn').addEventListener('click', async function() {
                if (!selectedOperator || !document.getElementById('toAddress').value) {
                    alert('Пожалуйста, выберите оператора и введите адрес получателя');
                    return;
                }
                
                // Geocode the address first
                const address = document.getElementById('toAddress').value;
                try {
                    const geocodeUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1&countrycodes=BY`;
                    const geocodeResponse = await fetch(geocodeUrl);
                    const geocodeData = await geocodeResponse.json();
                    
                    if (geocodeData.length > 0) {
                        const lat = parseFloat(geocodeData[0].lat);
                        const lon = parseFloat(geocodeData[0].lon);
                        
                        // Find nearest office
                        const nearestOffice = await findNearestOffice(lat, lon, selectedOperator.id);
                        if (nearestOffice) {
                            toOffice = nearestOffice;
                            alert(`Ближайшее отделение: ${nearestOffice.title}, ${nearestOffice.address}`);
                            
                            // Add temporary marker
                            L.marker([nearestOffice.lat, nearestOffice.lon])
                             .addTo(map)
                             .bindPopup(`<b>Получение: ${nearestOffice.title}</b><br>${nearestOffice.address}`)
                             .openPopup();
                        } else {
                            alert('Не найдено ближайшее отделение для этого оператора');
                        }
                    } else {
                        alert('Не удалось найти координаты для указанного адреса');
                    }
                } catch (error) {
                    console.error('Error geocoding address:', error);
                    alert('Ошибка при определении координат адреса');
                }
            });
            
            // Calculate button
            document.getElementById('calculateBtn').addEventListener('click', async function() {
                if (!fromOffice || !toOffice || !selectedOperator) {
                    alert('Пожалуйста, выберите оба отделения');
                    return;
                }
                
                const weight = parseFloat(document.getElementById('weight').value);
                const insurance = document.getElementById('insurance').checked;
                const fragile = document.getElementById('fragile').checked;
                const packaging = document.getElementById('packaging').checked;
                
                // Calculate route
                const route = await calculateRoute(
                    fromOffice.lat, fromOffice.lon,
                    toOffice.lat, toOffice.lon
                );
                
                if (route) {
                    // Show route on map
                    showRoute(route, fromOffice, toOffice);
                    
                    // Calculate price
                    const price = calculatePrice(
                        route.distance, 
                        weight, 
                        insurance, 
                        fragile, 
                        packaging, 
                        selectedOperator.tariff
                    );
                    
                    // Update UI
                    document.getElementById('distanceText').textContent = route.distance.toFixed(2);
                    document.getElementById('timeText').textContent = Math.round(route.duration);
                    document.getElementById('priceText').textContent = price.toFixed(2);
                    
                    document.getElementById('routeInfo').style.display = 'block';
                    document.getElementById('orderBtn').style.display = 'block';
                    
                    // Compare with all operators
                    document.getElementById('comparisonTable').style.display = 'block';
                    await compareWithAllOperators(
                        fromOffice.lat, fromOffice.lon,
                        toOffice.lat, toOffice.lon,
                        weight,
                        { insurance, fragile, packaging }
                    );
                    
                    // Store calculated route for later use
                    calculatedRoute = route;
                } else {
                    alert('Не удалось рассчитать маршрут');
                }
            });
            
            // Order button
            document.getElementById('orderBtn').addEventListener('click', function() {
                if (!fromOffice || !toOffice || !selectedOperator) {
                    alert('Пожалуйста, выполните расчет сначала');
                    return;
                }
                
                // Prepare data for order
                const orderData = {
                    operator_id: selectedOperator.id,
                    from_office_id: fromOffice.id,
                    to_office_id: toOffice.id,
                    distance: parseFloat(document.getElementById('distanceText').textContent),
                    duration: parseInt(document.getElementById('timeText').textContent),
                    price: parseFloat(document.getElementById('priceText').textContent),
                    weight: parseFloat(document.getElementById('weight').value),
                    insurance: document.getElementById('insurance').checked,
                    fragile: document.getElementById('fragile').checked,
                    packaging: document.getElementById('packaging').checked,
                    from_address: document.getElementById('fromAddress').value,
                    to_address: document.getElementById('toAddress').value
                };
                
                // Redirect to order form with data
                const params = new URLSearchParams(orderData).toString();
                window.location.href = `order_form.php?${params}`;
            });
        });
    </script>
</body>
</html>