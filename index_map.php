<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система доставки по Беларуси</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 100px 20px;
            margin-bottom: 50px;
        }
        
        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-large {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            margin: 10px;
            transition: background 0.3s;
        }
        
        .btn-large:hover {
            background: #ff5252;
        }
        
        .features {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 50px 0;
        }
        
        .feature {
            flex: 1;
            min-width: 300px;
            margin: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid #eee;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .feature h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .feature p {
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="hero">
        <h1>Система доставки по Беларуси</h1>
        <p>Интегрированная система для расчета и оформления доставки с использованием интерактивных карт и реальных маршрутов</p>
        <a href="calculator_map.php" class="btn-large">Калькулятор доставки</a>
        <a href="order_form_map.php" class="btn-large">Оформить заказ</a>
    </div>
    
    <div class="container">
        <div class="features">
            <div class="feature">
                <h3>Интерактивная карта</h3>
                <p>Выбирайте отделения на карте, просматривайте маршруты и расстояния в реальном времени</p>
            </div>
            <div class="feature">
                <h3>Реальные маршруты</h3>
                <p>Расчет доставки по реальным дорогам с учетом расстояния и времени в пути</p>
            </div>
            <div class="feature">
                <h3>Несколько перевозчиков</h3>
                <p>Сравнивайте стоимость и сроки доставки у разных операторов связи</p>
            </div>
        </div>
        
        <div style="text-align: center; margin: 50px 0;">
            <h2>Начните использовать систему</h2>
            <p>Выберите перевозчика, укажите пункты отправления и получения на карте, и получите точный расчет стоимости и времени доставки</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>