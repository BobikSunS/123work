<?php
// Тестовый скрипт для проверки расчета стоимости

// Имитируем данные из базы данных
$carrier = [
    'base_cost' => 9.50,
    'cost_per_kg' => 0.750,
    'cost_per_km' => 0.0190
];

$weight = 1.000;
$distance = 368.2; // км (примерное расстояние для получения нужной стоимости)

// Расчет компонентов
$base_cost = $carrier['base_cost'];
$weight_cost = $weight * $carrier['cost_per_kg'];
$distance_cost = $distance * $carrier['cost_per_km'];

echo "Базовая стоимость: " . number_format($base_cost, 2) . " BYN\n";
echo "Доставка (вес): " . number_format($weight_cost, 2) . " BYN\n";
echo "Доставка (расстояние): " . number_format($distance_cost, 2) . " BYN\n";
echo "Подытог: " . number_format($base_cost + $weight_cost + $distance_cost, 2) . " BYN\n";

// Применяем страховку (2% от текущей стоимости)
$insurance = true;
if ($insurance) {
    $insurance_cost = ($base_cost + $weight_cost + $distance_cost) * 0.02;
    $total_with_insurance = ($base_cost + $weight_cost + $distance_cost) * 1.02;
    echo "Страховка (2%): " . number_format($insurance_cost, 2) . " BYN\n";
    echo "Итого с страховкой: " . number_format($total_with_insurance, 2) . " BYN\n";
}

echo "\nПроверим, какое расстояние нужно для получения 17.59 BYN:\n";
$target_total = 17.59;
// (base_cost + weight_cost + distance_cost) * 1.02 = target_total
// base_cost + weight_cost + distance_cost = target_total / 1.02
// distance_cost = target_total / 1.02 - base_cost - weight_cost
$required_distance_cost = $target_total / 1.02 - $carrier['base_cost'] - $weight * $carrier['cost_per_kg'];
$required_distance = $required_distance_cost / $carrier['cost_per_km'];

echo "Требуемая стоимость по расстоянию: " . number_format($required_distance_cost, 2) . " BYN\n";
echo "Требуемое расстояние: " . number_format($required_distance, 1) . " км\n";
echo "Проверка: " . number_format(($carrier['base_cost'] + $weight * $carrier['cost_per_kg'] + $required_distance * $carrier['cost_per_km']) * 1.02, 2) . " BYN\n";
?>