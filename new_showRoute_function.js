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