#!/usr/bin/env python3

import re

# Read the calculator.php file
with open('/workspace/calculator.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Define the old function (as it appears in the file)
old_function = '''// Показ маршрута
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
    .then(response => response.json())
    .then(data => {
        // Удаляем индикатор загрузки
        const loadingElement = document.getElementById('route-loading');
        if (loadingElement) {
            document.body.removeChild(loadingElement);
        }

        if (data.success) {
            if (data.route_data) {
                // Декодируем маршрут из polyline
                const decodedRoute = L.PolylineUtil.decode(data.route_data);

                // Преобразуем координаты из [lng, lat] в [lat, lng] формат Leaflet
                const routeCoords = decodedRoute.map(coord => [coord[1], coord[0]]);

                routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                // Центрируем карту на маршруте
                const bounds = L.latLngBounds(routeCoords);
                map.fitBounds(bounds, {padding: [50, 50]});

                alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км, Время: ${data.duration} мин.`);
            } else {
                // Если route_data отсутствует, используем прямую линию как fallback
                const fromOffice = offices.find(o => o.id == selectedFromOffice);
                const toOffice = offices.find(o => o.id == selectedToOffice);

                if (fromOffice && toOffice) {
                    const routeCoords = [
                        [fromOffice.lat, fromOffice.lng],
                        [toOffice.lat, toOffice.lng]
                    ];

                    routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                    // Центрируем карту на маршруте
                    const bounds = L.latLngBounds(routeCoords);
                    map.fitBounds(bounds, {padding: [50, 50]});

                    alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км (по прямой).`);
                }
            }
        } else {
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
        alert('Ошибка при получении маршрута. Проверьте соединение с интернетом.');
    });
}'''

# Define the new function
new_function = '''// Показ маршрута
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
    .then(response => response.json())
    .then(data => {
        // Удаляем индикатор загрузки
        const loadingElement = document.getElementById('route-loading');
        if (loadingElement) {
            document.body.removeChild(loadingElement);
        }

        if (data.success) {
            if (data.route_data) {
                // Check if the route_data is an array of points (GraphHopper format) or polyline string
                let routeCoords = [];
                
                if (Array.isArray(data.route_data)) {
                    // This is GraphHopper format: array of [lat, lng] points
                    routeCoords = data.route_data;
                } else if (typeof data.route_data === 'string') {
                    // This is OSRM polyline format
                    const decodedRoute = L.PolylineUtil.decode(data.route_data);
                    // Convert from [lng, lat] to [lat, lng] format
                    routeCoords = decodedRoute.map(coord => [coord[1], coord[0]]);
                } else {
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

                if (routeCoords.length > 0) {
                    routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                    // Центрируем карту на маршруте
                    const bounds = L.latLngBounds(routeCoords);
                    map.fitBounds(bounds, {padding: [50, 50]});

                    alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км, Время: ${data.duration} мин.`);
                } else {
                    // Fallback to straight line if route decoding fails
                    const fromOffice = offices.find(o => o.id == selectedFromOffice);
                    const toOffice = offices.find(o => o.id == selectedToOffice);

                    if (fromOffice && toOffice) {
                        const straightLineCoords = [
                            [fromOffice.lat, fromOffice.lng],
                            [toOffice.lat, toOffice.lng]
                        ];

                        routeLayer = L.polyline(straightLineCoords, {color: 'red', weight: 4}).addTo(map);

                        // Центрируем карту на маршруте
                        const bounds = L.latLngBounds(straightLineCoords);
                        map.fitBounds(bounds, {padding: [50, 50]});

                        alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км (по прямой).`);
                    }
                }
            } else {
                // Если route_data отсутствует, используем прямую линию как fallback
                const fromOffice = offices.find(o => o.id == selectedFromOffice);
                const toOffice = offices.find(o => o.id == selectedToOffice);

                if (fromOffice && toOffice) {
                    const routeCoords = [
                        [fromOffice.lat, fromOffice.lng],
                        [toOffice.lat, toOffice.lng]
                    ];

                    routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                    // Центрируем карту на маршруте
                    const bounds = L.latLngBounds(routeCoords);
                    map.fitBounds(bounds, {padding: [50, 50]});

                    alert(`Маршрут построен. Расстояние: ${data.distance.toFixed(2)} км (по прямой).`);
                }
            }
        } else {
            alert('Ошибка при получении маршрута: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        // Удаляем индикатор загрузки
        const loadingElement = document.getElementById('route-loading');
        if (loadingElement) {
            document.body.removeChild(loadingElement);
        });
        
        console.error('Error getting route:', error);
        alert('Ошибка при получении маршрута. Проверьте соединение с интернетом.');
    });
}'''

# Replace the old function with the new one
updated_content = content.replace(old_function, new_function)

# Write the updated content back to the file
with open('/workspace/calculator.php', 'w', encoding='utf-8') as f:
    f.write(updated_content)

print("showRoute function successfully updated in calculator.php")