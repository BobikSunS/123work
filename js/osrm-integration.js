// OSRM Integration for Belarus Delivery Project
let map = null;
let fromMarker = null;
let toMarker = null;
let routeControl = null;
let selectedCarrier = null;
let selectedCarrierColor = '#007bff';
let officesLayer = null;

// Initialize the map
function initMap() {
    if (map) {
        map.remove();
    }
    
    map = L.map('map').setView([53.904133, 27.557541], 7); // Center on Belarus

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add click event to set markers
    map.on('click', function(e) {
        if (!selectedCarrier) {
            alert('Пожалуйста, выберите оператора доставки');
            return;
        }
        
        // Determine if this is from or to marker based on which is not set
        if (!fromMarker) {
            setFromMarker(e.latlng);
        } else if (!toMarker) {
            setToMarker(e.latlng);
        } else {
            // If both are set, ask user which one to update
            if (confirm('Обновить точку отправления или получения?')) {
                if (confirm('Обновить точку отправления?')) {
                    setFromMarker(e.latlng);
                } else {
                    setToMarker(e.latlng);
                }
            }
        }
    });
}

// Set marker for starting point
function setFromMarker(latlng) {
    if (fromMarker) {
        map.removeLayer(fromMarker);
    }
    
    fromMarker = L.marker(latlng, {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);">A</div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        })
    }).addTo(map);
    
    document.getElementById('from-lat').value = latlng.lat;
    document.getElementById('from-lng').value = latlng.lng;
    
    // Reverse geocode to get address
    reverseGeocode(latlng.lat, latlng.lng, 'from');
    
    if (toMarker) {
        calculateRoute();
    }
}

// Set marker for destination point
function setToMarker(latlng) {
    if (toMarker) {
        map.removeLayer(toMarker);
    }
    
    toMarker = L.marker(latlng, {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);">B</div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        })
    }).addTo(map);
    
    document.getElementById('to-lat').value = latlng.lat;
    document.getElementById('to-lng').value = latlng.lng;
    
    // Reverse geocode to get address
    reverseGeocode(latlng.lat, latlng.lng, 'to');
    
    if (fromMarker) {
        calculateRoute();
    }
}

// Select carrier and update UI
function selectCarrier(id, name, color) {
    document.getElementById('selected-carrier').value = id;
    document.getElementById('carrier-name').textContent = name;
    document.getElementById('calc-form').style.display = 'block';
    selectedCarrier = id;
    selectedCarrierColor = color;
    
    // Highlight selected carrier card
    document.querySelectorAll('.carrier-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.carrier-card').classList.add('selected');
    
    // Initialize map if not already done
    if (!map) {
        setTimeout(initMap, 100);
    }
    
    // Load offices for this carrier
    loadOfficesForCarrier(id);
}

// Load offices for selected carrier
function loadOfficesForCarrier(carrierId) {
    if (officesLayer) {
        map.removeLayer(officesLayer);
    }
    
    fetch(`ajax_get_offices.php?carrier_id=${carrierId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                officesLayer = L.layerGroup().addTo(map);
                
                data.offices.forEach(office => {
                    if (office.lat && office.lng) {
                        const marker = L.marker([parseFloat(office.lat), parseFloat(office.lng)], {
                            title: `${office.city} - ${office.address}`
                        }).addTo(officesLayer);
                        
                        marker.bindPopup(`
                            <b>${office.city}</b><br>
                            ${office.address}<br>
                            <button class="btn btn-sm btn-primary mt-1" onclick="selectOffice(${office.id}, ${office.lat}, ${office.lng}, '${office.address}')">
                                Выбрать
                            </button>
                        `);
                    }
                });
            }
        })
        .catch(error => console.error('Error loading offices:', error));
}

// Select office as from/to point
function selectOffice(officeId, lat, lng, address) {
    // Determine which marker to set based on current state
    if (!fromMarker) {
        setFromMarker(L.latLng(lat, lng));
        document.getElementById('from-address').value = address;
    } else if (!toMarker) {
        setToMarker(L.latLng(lat, lng));
        document.getElementById('to-address').value = address;
    } else {
        // Ask user which point to update
        if (confirm('Обновить точку отправления или получения?')) {
            setFromMarker(L.latLng(lat, lng));
            document.getElementById('from-address').value = address;
        } else {
            setToMarker(L.latLng(lat, lng));
            document.getElementById('to-address').value = address;
        }
    }
}

// Get current location
function getCurrentLocation(type) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latlng = L.latLng(position.coords.latitude, position.coords.longitude);
            
            if (type === 'from') {
                setFromMarker(latlng);
            }
        }, function(error) {
            alert('Не удалось получить ваше местоположение: ' + error.message);
        });
    } else {
        alert('Геолокация не поддерживается вашим браузером');
    }
}

// Reverse geocode coordinates to address
function reverseGeocode(lat, lng, type) {
    fetch(`ajax_geocode.php?action=reverse&lat=${lat}&lng=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(type + '-address').value = data.address;
            }
        })
        .catch(error => console.error('Error in reverse geocoding:', error));
}

// Geocode address to coordinates
function geocodeAddress(address, type) {
    fetch(`ajax_geocode.php?action=geocode&address=${encodeURIComponent(address)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const latlng = L.latLng(data.lat, data.lng);
                
                if (type === 'from') {
                    setFromMarker(latlng);
                } else {
                    setToMarker(latlng);
                }
                
                document.getElementById(type + '-address').value = address;
                document.getElementById(type + '-suggestions').style.display = 'none';
            } else {
                console.error('Geocoding failed:', data.message);
            }
        })
        .catch(error => console.error('Error in geocoding:', error));
}

// Calculate route using OSRM
function calculateRoute() {
    const fromLat = parseFloat(document.getElementById('from-lat').value);
    const fromLng = parseFloat(document.getElementById('from-lng').value);
    const toLat = parseFloat(document.getElementById('to-lat').value);
    const toLng = parseFloat(document.getElementById('to-lng').value);
    
    if (!fromLat || !fromLng || !toLat || !toLng) {
        alert('Пожалуйста, укажите обе точки на карте');
        return;
    }
    
    // Remove existing route if present
    if (routeControl) {
        map.removeControl(routeControl);
    }
    
    // Show loading indicator
    document.getElementById('route-info').style.display = 'block';
    document.getElementById('distance').textContent = '...';
    document.getElementById('duration').textContent = '...';
    document.getElementById('cost').textContent = '...';
    
    // Create new route using OSRM
    routeControl = L.Routing.control({
        waypoints: [
            L.latLng(fromLat, fromLng),
            L.latLng(toLat, toLng)
        ],
        routeWhileDragging: false,
        geocoder: null,
        lineOptions: {
            styles: [{color: selectedCarrierColor, weight: 5}]
        },
        createMarker: function() { return null; } // Don't create default markers
    }).addTo(map);
    
    // When route is calculated, update the form and display results
    routeControl.on('routesfound', function(e) {
        const route = e.routes[0];
        const distanceKm = route.summary.totalDistance / 1000; // Convert to km
        const durationMin = route.summary.totalTime / 60;      // Convert to minutes
        
        // Update hidden form fields
        document.getElementById('distance-km').value = distanceKm;
        document.getElementById('duration-min').value = durationMin;
        
        // Update UI with results
        document.getElementById('distance').textContent = distanceKm.toFixed(1);
        document.getElementById('duration').textContent = Math.round(durationMin);
        
        // Calculate and display cost
        const weight = parseFloat(document.querySelector('input[name="weight"]').value) || 1;
        const insurance = document.getElementById('insurance').checked;
        const packaging = document.getElementById('packaging').checked;
        const fragile = document.getElementById('fragile').checked;
        
        // Get current carrier's rates
        const carrierId = document.getElementById('selected-carrier').value;
        const carrierElement = document.querySelector(`.carrier-card[onclick*="selectCarrier(${carrierId}"]`);
        // We'll use a placeholder for now - in real implementation, we'd fetch rates via AJAX
        let cost = 5.00 + (weight * 0.50) + (distanceKm * 0.0200); // Base cost + weight + distance
        if (insurance) cost += 5.00;
        if (packaging) cost += 3.00;
        if (fragile) cost += 2.00;
        
        cost = Math.round(cost * 100) / 100;
        document.getElementById('cost').textContent = cost.toFixed(2);
        
        // Update form values for submission
        document.getElementById('calculation-form').querySelector('input[name="distance_km"]').value = distanceKm;
        document.getElementById('calculation-form').querySelector('input[name="duration_min"]').value = durationMin;
        document.getElementById('calculation-form').querySelector('input[name="cost"]').value = cost;
        
        // Calculate routes for other carriers for comparison
        calculateOtherCarriers(distanceKm, durationMin, cost);
    });
}

// Calculate routes for other carriers for comparison
function calculateOtherCarriers(distanceKm, durationMin, baseCost) {
    // In a real implementation, this would fetch data for all carriers
    // For demo, we'll simulate with different pricing
    let comparisonHtml = '';
    
    // This would be replaced with actual AJAX calls to get rates for each carrier
    // For now, we'll use placeholder data
    const carriers = [
        {id: 1, name: 'Белпочта', color: '#e74c3c', costPerKm: 0.0180},
        {id: 2, name: 'Европочта', color: '#3498db', costPerKm: 0.0220},
        {id: 3, name: 'DPD', color: '#9b59b6', costPerKm: 0.0250},
        {id: 4, name: 'СДЭК', color: '#f39c12', costPerKm: 0.0200},
        {id: 5, name: 'Boxberry', color: '#1abc9c', costPerKm: 0.0190}
    ];
    
    const weight = parseFloat(document.querySelector('input[name="weight"]').value) || 1;
    const insurance = document.getElementById('insurance').checked;
    const packaging = document.getElementById('packaging').checked;
    const fragile = document.getElementById('fragile').checked;
    
    carriers.forEach(carrier => {
        if (carrier.id != selectedCarrier) {
            let cost = 5.00 + (weight * 0.50) + (distanceKm * carrier.costPerKm);
            if (insurance) cost += 5.00;
            if (packaging) cost += 3.00;
            if (fragile) cost += 2.00;
            
            cost = Math.round(cost * 100) / 100;
            
            comparisonHtml += `
                <tr>
                    <td style="background: ${carrier.color}">
                        <div class="text-white">${carrier.name}</div>
                    </td>
                    <td><strong>${cost.toFixed(2)} BYN</strong></td>
                    <td>${Math.round(durationMin)} мин</td>
                    <td>${distanceKm.toFixed(1)} км</td>
                    <td>
                        <a href="order_form.php?carrier=${carrier.id}&weight=${weight}&cost=${cost}&distance=${distanceKm}&duration=${durationMin}&from_lat=${document.getElementById('from-lat').value}&from_lng=${document.getElementById('from-lng').value}&to_lat=${document.getElementById('to-lat').value}&to_lng=${document.getElementById('to-lng').value}" class="btn btn-sm btn-outline-primary">Выбрать</a>
                    </td>
                </tr>
            `;
        }
    });
    
    if (comparisonHtml) {
        document.getElementById('comparison-results').innerHTML = comparisonHtml;
    }
}

// Add event listeners for address inputs
document.addEventListener('DOMContentLoaded', function() {
    // From address input
    if (document.getElementById('from-address')) {
        document.getElementById('from-address').addEventListener('input', function() {
            if (this.value.length > 3) {
                const suggestionsDiv = document.getElementById('from-suggestions');
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'block';
                
                // In a real implementation, this would call geocoding API
                // For demo, we'll just show how it would work
                geocodeAddress(this.value, 'from');
            }
        });
        
        document.getElementById('from-address').addEventListener('blur', function() {
            setTimeout(() => {
                document.getElementById('from-suggestions').style.display = 'none';
            }, 200);
        });
    }
    
    // To address input
    if (document.getElementById('to-address')) {
        document.getElementById('to-address').addEventListener('input', function() {
            if (this.value.length > 3) {
                const suggestionsDiv = document.getElementById('to-suggestions');
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'block';
                
                geocodeAddress(this.value, 'to');
            }
        });
        
        document.getElementById('to-address').addEventListener('blur', function() {
            setTimeout(() => {
                document.getElementById('to-suggestions').style.display = 'none';
            }, 200);
        });
    }
    
    // Initialize map after page loads if carrier is already selected
    if (document.getElementById('calc-form').style.display === 'block') {
        setTimeout(initMap, 100);
    }
});

// Export functions for global use
window.initMap = initMap;
window.setFromMarker = setFromMarker;
window.setToMarker = setToMarker;
window.selectCarrier = selectCarrier;
window.getCurrentLocation = getCurrentLocation;
window.calculateRoute = calculateRoute;