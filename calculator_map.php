<?php
session_start();
require_once 'db.php';

// Get all carriers
$stmt = $db->query("SELECT * FROM carriers ORDER BY name");
$carriers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор доставки с картой</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        #map {
            height: 500px;
            width: 100%;
            margin: 20px 0;
        }
        
        .office-selector {
            margin: 20px 0;
        }
        
        .office-selector select, .office-selector input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .route-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            display: none;
        }
        
        .result-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .office-marker {
            background: #fff;
            border: 2px solid #333;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Калькулятор доставки с картой</h1>
        
        <div class="calculator">
            <div class="form-group">
                <label for="carrier_id">Выберите перевозчика:</label>
                <select id="carrier_id" name="carrier_id" required>
                    <option value="">-- Выберите перевозчика --</option>
                    <?php foreach ($carriers as $carrier): ?>
                        <option value="<?= $carrier['id'] ?>" 
                            data-color="<?= $carrier['color'] ?>" 
                            data-base-cost="<?= $carrier['base_cost'] ?>"
                            data-cost-per-kg="<?= $carrier['cost_per_kg'] ?>"
                            data-cost-per-km="<?= $carrier['cost_per_km'] ?>"
                            data-speed="<?= $carrier['speed_kmh'] ?>">
                            <?= htmlspecialchars($carrier['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="office-selector">
                <label>Выберите отделение отправления:</label>
                <select id="from_office" name="from_office" required>
                    <option value="">-- Выберите перевозчика сначала --</option>
                </select>
            </div>
            
            <div class="office-selector">
                <label>Выберите отделение получения:</label>
                <select id="to_office" name="to_office" required>
                    <option value="">-- Выберите перевозчика сначала --</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="weight">Вес посылки (кг):</label>
                <input type="number" id="weight" name="weight" min="0.1" max="30" step="0.1" value="1" required>
            </div>
            
            <div id="routeInfo" class="route-info">
                <h3>Результаты расчета</h3>
                <div class="result-item">
                    <strong>Расстояние:</strong> <span id="distance">0</span> км
                </div>
                <div class="result-item">
                    <strong>Время в пути:</strong> <span id="duration">0</span> часов
                </div>
                <div class="result-item">
                    <strong>Стоимость доставки:</strong> <span id="cost">0</span> BYN
                </div>
            </div>
        </div>
        
        <div id="map"></div>
        
        <div style="margin-top: 20px;">
            <a href="order_form_map.php" class="btn">Оформить заказ</a>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize the map centered on Belarus
        const map = L.map('map').setView([53.7098, 27.9534], 7); // Center on Belarus
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Variables to store selected offices and route
        let fromOffice = null;
        let toOffice = null;
        let routeLine = null;
        let fromMarker = null;
        let toMarker = null;
        let officeMarkers = [];
        
        // Get carrier offices when carrier is selected
        document.getElementById('carrier_id').addEventListener('change', function() {
            const carrierId = this.value;
            
            // Clear existing markers
            clearOfficeMarkers();
            
            if (carrierId) {
                fetch(`get_offices_by_carrier.php?carrier_id=${carrierId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            populateOfficeSelects(data.offices);
                            addOfficeMarkers(data.offices);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                clearOfficeSelects();
            }
        });
        
        // Populate office selection dropdowns
        function populateOfficeSelects(offices) {
            const fromSelect = document.getElementById('from_office');
            const toSelect = document.getElementById('to_office');
            
            // Clear existing options
            fromSelect.innerHTML = '<option value="">-- Выберите отделение отправления --</option>';
            toSelect.innerHTML = '<option value="">-- Выберите отделение получения --</option>';
            
            // Add new options
            offices.forEach(office => {
                const fromOption = document.createElement('option');
                fromOption.value = office.id;
                fromOption.textContent = `${office.city}, ${office.address}`;
                fromSelect.appendChild(fromOption);
                
                const toOption = document.createElement('option');
                toOption.value = office.id;
                toOption.textContent = `${office.city}, ${office.address}`;
                toSelect.appendChild(toOption);
            });
        }
        
        // Clear office selection dropdowns
        function clearOfficeSelects() {
            document.getElementById('from_office').innerHTML = '<option value="">-- Выберите перевозчика сначала --</option>';
            document.getElementById('to_office').innerHTML = '<option value="">-- Выберите перевозчика сначала --</option>';
        }
        
        // Add markers for offices
        function addOfficeMarkers(offices) {
            offices.forEach(office => {
                const marker = L.marker([office.lat, office.lng]).addTo(map);
                marker.officeData = office;
                marker.bindPopup(`<b>${office.city}</b><br>${office.address}`);
                
                // Add click event to select office
                marker.on('click', function() {
                    // Check if we're selecting from or to office
                    if (!fromOffice) {
                        selectFromOffice(office);
                    } else if (!toOffice) {
                        selectToOffice(office);
                    } else {
                        // If both are selected, ask user which one to replace
                        if (confirm('Заменить пункт получения?')) {
                            clearToOffice();
                            selectToOffice(office);
                        } else if (confirm('Заменить пункт отправления?')) {
                            clearFromOffice();
                            selectFromOffice(office);
                        }
                    }
                });
                
                officeMarkers.push(marker);
            });
        }
        
        // Clear office markers
        function clearOfficeMarkers() {
            officeMarkers.forEach(marker => map.removeLayer(marker));
            officeMarkers = [];
        }
        
        // Select from office
        function selectFromOffice(office) {
            fromOffice = office;
            
            // Update select dropdown
            document.getElementById('from_office').value = office.id;
            
            // Add special marker for selected from office
            if (fromMarker) {
                map.removeLayer(fromMarker);
            }
            fromMarker = L.marker([office.lat, office.lng], {
                icon: L.divIcon({className: 'office-marker', html: 'A', bgPos: [0, 0]})
            }).addTo(map);
            
            // Calculate route if both offices are selected
            if (toOffice) {
                calculateRoute();
            }
        }
        
        // Select to office
        function selectToOffice(office) {
            toOffice = office;
            
            // Update select dropdown
            document.getElementById('to_office').value = office.id;
            
            // Add special marker for selected to office
            if (toMarker) {
                map.removeLayer(toMarker);
            }
            toMarker = L.marker([office.lat, office.lng], {
                icon: L.divIcon({className: 'office-marker', html: 'B', bgPos: [0, 0]})
            }).addTo(map);
            
            // Calculate route if both offices are selected
            if (fromOffice) {
                calculateRoute();
            }
        }
        
        // Clear from office selection
        function clearFromOffice() {
            fromOffice = null;
            if (fromMarker) {
                map.removeLayer(fromMarker);
                fromMarker = null;
            }
            document.getElementById('from_office').value = '';
        }
        
        // Clear to office selection
        function clearToOffice() {
            toOffice = null;
            if (toMarker) {
                map.removeLayer(toMarker);
                toMarker = null;
            }
            document.getElementById('to_office').value = '';
        }
        
        // Calculate route between selected offices
        function calculateRoute() {
            if (!fromOffice || !toOffice) return;
            
            // Remove previous route line if exists
            if (routeLine) {
                map.removeLayer(routeLine);
            }
            
            // Show loading state
            document.getElementById('distance').textContent = 'Расчет...';
            document.getElementById('duration').textContent = 'Расчет...';
            document.getElementById('cost').textContent = 'Расчет...';
            
            // In a real implementation, you would call a routing service
            // For now, just draw a straight line and calculate haversine distance
            const latlngs = [
                [fromOffice.lat, fromOffice.lng],
                [toOffice.lat, toOffice.lng]
            ];
            
            routeLine = L.polyline(latlngs, {color: 'red', weight: 5}).addTo(map);
            
            // Calculate distance using more realistic calculation
            const distance = calculateRealisticDistance(
                fromOffice.lat, fromOffice.lng,
                toOffice.lat, toOffice.lng
            );
            
            // Calculate estimated time (assuming 60 km/h average speed)
            const time = distance / 60; // hours
            
            // Calculate cost based on carrier settings
            const carrierSelect = document.getElementById('carrier_id');
            const selectedCarrier = carrierSelect.options[carrierSelect.selectedIndex];
            const baseCost = parseFloat(selectedCarrier.getAttribute('data-base-cost')) || 0;
            const costPerKm = parseFloat(selectedCarrier.getAttribute('data-cost-per-km')) || 0;
            const costPerKg = parseFloat(selectedCarrier.getAttribute('data-cost-per-kg')) || 0;
            const weight = parseFloat(document.getElementById('weight').value) || 1;
            
            const distanceCost = distance * costPerKm;
            const weightCost = weight * costPerKg;
            const totalCost = baseCost + distanceCost + weightCost;
            
            // Update route info display
            document.getElementById('distance').textContent = distance.toFixed(2);
            document.getElementById('duration').textContent = time.toFixed(2);
            document.getElementById('cost').textContent = totalCost.toFixed(2);
            
            document.getElementById('routeInfo').style.display = 'block';
            
            // Fit map to show both points
            const bounds = L.latLngBounds(latlngs);
            map.fitBounds(bounds, {padding: [50, 50]});
        }
        
        // More realistic distance calculation (accounts for road routing)
        function calculateRealisticDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth's radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const straightLineDistance = R * c;
            
            // Apply a factor to account for road routing (roads are typically 10-30% longer than straight line)
            const roadDistanceFactor = 1.2; // Adjust this based on real data
            return straightLineDistance * roadDistanceFactor;
        }
        
        // Haversine distance calculation
        function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth's radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
        
        // Event listeners for office selection dropdowns
        document.getElementById('from_office').addEventListener('change', function() {
            if (this.value) {
                // Find the office in the marker list and select it
                const selectedMarker = officeMarkers.find(marker => marker.officeData.id == this.value);
                if (selectedMarker) {
                    selectFromOffice(selectedMarker.officeData);
                }
            }
        });
        
        document.getElementById('to_office').addEventListener('change', function() {
            if (this.value) {
                // Find the office in the marker list and select it
                const selectedMarker = officeMarkers.find(marker => marker.officeData.id == this.value);
                if (selectedMarker) {
                    selectToOffice(selectedMarker.officeData);
                }
            }
        });
        
        // Event listener for weight input to update cost
        document.getElementById('weight').addEventListener('input', function() {
            if (fromOffice && toOffice) {
                calculateRoute();
            }
        });
    </script>
</body>
</html>