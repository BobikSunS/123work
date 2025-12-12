# Belarus Delivery Project - OSRM Integration

## Установка и настройка проекта

### 1. Подготовка XAMPP

1. Установите XAMPP (Apache, MySQL, PHP) на вашу систему
2. Запустите Apache и MySQL через XAMPP Control Panel
3. Скопируйте всю папку проекта в `htdocs` директорию XAMPP

### 2. Настройка базы данных

1. Откройте phpMyAdmin (обычно доступен по адресу `http://localhost/phpmyadmin`)
2. Создайте новую базу данных с названием `delivery_db`
3. Импортируйте файл `delivery_by (2).sql` в созданную базу данных

### 3. Настройка соединения с базой данных

1. Откройте файл `db.php` в корне проекта
2. Убедитесь, что настройки подключения соответствуют вашей локальной установке:

```php
<?php
$host = 'localhost';
$dbname = 'delivery_db';
$username = 'root';  // по умолчанию для XAMPP
$password = '';      // по умолчанию для XAMPP

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>
```

### 4. Обновление структуры базы данных

Запустите скрипт обновления структуры базы данных:

```bash
php /path/to/project/update_db_structure.php
```

Или откройте в браузере: `http://localhost/your-project-folder/update_db_structure.php`

### 5. Заполнение базы данных офисами

Запустите скрипт заполнения офисов:

```bash
php /path/to/project/populate_offices.php
```

Или откройте в браузере: `http://localhost/your-project-folder/populate_offices.php`

### 6. Запуск геокодирования офисов

Для массового геокодирования адресов офисов выполните:

```bash
php /path/to/project/geocode_offices.php
```

Или откройте в браузере: `http://localhost/your-project-folder/geocode_offices.php`

> **Важно:** Этот процесс может занять некоторое время, так как между запросами к Nominatim добавляется задержка 1 секунда для соблюдения условий использования API.

### 7. Проверка работоспособности

1. Откройте в браузере: `http://localhost/your-project-folder/`
2. Зарегистрируйтесь или войдите в систему
3. Перейдите на страницу калькулятора доставки
4. Выберите оператора и проверьте работу карты с маршрутами

## Использование проекта

### Калькулятор доставки

1. Выберите оператора доставки
2. Укажите адрес отправителя (через поиск или клик на карте)
3. Укажите адрес получателя
4. Введите параметры посылки (вес, страховка, упаковка и т.д.)
5. Нажмите "Рассчитать маршрут"
6. Система построит реальный маршрут по дорогам через OSRM и покажет:
   - Расстояние в км
   - Время доставки в минутах
   - Стоимость доставки
7. Ниже отобразится таблица сравнения с другими операторами

### Оформление заказа

1. После расчета на странице калькулятора нажмите "Оформить заказ"
2. Заполните личные данные и данные получателя
3. Подтвердите заказ и перейдите к оплате

## Архитектура интеграции OSRM

### Основные компоненты:

1. **Карта Leaflet** - отображение маршрутов и географических данных
2. **OSRM API** - расчет реальных маршрутов по дорогам
3. **Nominatim API** - геокодирование адресов в координаты
4. **Кеширование** - для ускорения работы и снижения нагрузки на API

### Структура базы данных:

- `carriers` - таблица операторов доставки (добавлены поля: `color`, `cost_per_km`)
- `offices` - таблица отделений (добавлены поля: `lat`, `lng`, `city`)
- `orders` - таблица заказов (добавлены поля: `from_office_id`, `to_office_id`, `distance_km`, `duration_min`, `final_price`)
- `geocache` - таблица кеширования геокодирования
- `route_cache` - таблица кеширования маршрутов

### AJAX-эндпоинты:

- `ajax_get_offices.php` - загрузка офисов выбранного оператора
- `ajax_geocode.php` - геокодирование/реверсивное геокодирование
- `ajax_route.php` - расчет маршрута через OSRM

## Полностью оффлайн-вариант

Для работы без доступа к интернету (но с браузером) можно установить локальный OSRM сервер:

### 1. Установка OSRM

1. Установите Docker на вашу систему
2. Скачайте OSM данные Беларуси с [Geofabrik](https://download.geofabrik.de/europe/belarus.html)
3. Запустите OSRM сервер с этими данными:

```bash
# Загрузите данные Беларуси
wget https://download.geofabrik.de/europe/belarus-latest.osm.pbf

# Запустите OSRM (предварительно установив Docker)
docker run -t -v $(pwd):/data osrm/osrm-backend osrm-extract -p /opt/car.lua /data/belarus-latest.osm.pbf
docker run -t -v $(pwd):/data osrm/osrm-backend osrm-partition /data/belarus-latest.osrm
docker run -t -v $(pwd):/data osrm/osrm-backend osrm-customize /data/belarus-latest.osrm
docker run -d -t -i -p 5000:5000 -v $(pwd):/data osrm/osrm-backend osrm-routed --algorithm mld /data/belarus-latest.osrm
```

### 2. Настройка проекта на локальный OSRM

Измените URL в файле `ajax_route.php` с:
```php
$osrm_url = "https://router.project-osrm.org/route/v1/driving/{$from_lng},{$from_lat};{$to_lng},{$to_lat}?overview=full&steps=true";
```

на:
```php
$osrm_url = "http://localhost:5000/route/v1/driving/{$from_lng},{$from_lat};{$to_lng},{$to_lat}?overview=full&steps=true";
```

## Технические особенности

### Алгоритм расчета стоимости:

1. Расстояние и время берутся исключительно из ответа OSRM:
   - `summary.totalDistance / 1000` (для км)
   - `summary.totalTime / 60` (для минут)

2. Стоимость = (расстояние_в_км × тариф_за_км_оператора) + фиксированные надбавки:
   - Базовая стоимость
   - Стоимость за кг веса
   - Дополнительные услуги (страховка, упаковка, хрупкое)

### Кеширование:

- Геокодирование: результаты сохраняются в таблицу `geocache`
- Маршруты: результаты сохраняются в таблицу `route_cache`
- Защита от частых запросов: задержка 1 секунда между запросами к Nominatim

### Защита от ограничений API:

- Добавлен User-Agent в запросы к Nominatim
- Ограничение частоты запросов (1 в секунду)
- Кеширование результатов для повторного использования

## Зависимости

- PHP 7.4+
- MySQL 5.7+
- Apache с поддержкой mod_rewrite
- Доступ к интернету (для публичных API OSRM и Nominatim)

## Возможные проблемы и решения

1. **Ошибка подключения к базе данных**: проверьте настройки в `db.php`
2. **Маршруты не строятся**: убедитесь, что браузер может обращаться к OSRM API
3. **Медленная работа**: проверьте кеширование в таблицах `geocache` и `route_cache`
4. **Геокодирование не работает**: проверьте ограничения Nominatim API и задержки в коде

Проект полностью готов к использованию с реальными дорожными графиками Беларуси через OpenStreetMap!