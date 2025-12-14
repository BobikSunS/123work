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
                // Декодируем маршрут из polyline
                const decodedRoute = L.PolylineUtil.decode(data.route_data);
                
                // Преобразуем координаты из [lng, lat] в [lat, lng] формат Leaflet
                const routeCoords = decodedRoute.map(coord => [coord[1], coord[0]]);

                routeLayer = L.polyline(routeCoords, {color: 'red', weight: 4}).addTo(map);

                // Центрируем карту на маршруте
                const bounds = L.latLngBounds(routeCoords);
                map.fitBounds(bounds, {padding: [50, 50]});

                alert(`Маршрут построен по дорогам. Расстояние: ${data.distance.toFixed(2)} км, Время: ${data.duration} мин.`);
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