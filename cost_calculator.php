<?php
// Единая функция для расчета стоимости доставки

function calculateDeliveryCost($db, $carrier_id, $from_office_id, $to_office_id, $weight, $package_type, $insurance, $letter_count = 1, $packaging = false, $fragile = false, $with_breakdown = false, $cash_on_delivery_amount = 0) {
    // Получаем информацию о перевозчике
    $carrier = $db->prepare("SELECT * FROM carriers WHERE id = ?");
    $carrier->execute([$carrier_id]);
    $carrier = $carrier->fetch();
    
    if (!$carrier) {
        throw new Exception("Invalid carrier");
    }
    
    // Получаем информацию о маршруте (учитываем оба направления)
    $routeData = $db->prepare("SELECT distance_km, duration_min FROM calculated_routes WHERE (from_office_id = ? AND to_office_id = ?) OR (from_office_id = ? AND to_office_id = ?)");
    $routeData->execute([$from_office_id, $to_office_id, $to_office_id, $from_office_id]);
    $routeData = $routeData->fetch();
    
    if (!$routeData) {
        // Если маршрут не найден, вычисляем приблизительное расстояние
        $offices = $db->prepare("SELECT lat, lng FROM offices WHERE id IN (?, ?)");
        $offices->execute([$from_office_id, $to_office_id]);
        $office_rows = $offices->fetchAll();
        
        if (count($office_rows) < 2) {
            throw new Exception("Invalid office IDs");
        }
        
        $from_office_data = $office_rows[0];
        $to_office_data = $office_rows[1];
        
        // Calculate approximate distance using Haversine formula
        $lat1 = deg2rad($from_office_data['lat']);
        $lon1 = deg2rad($from_office_data['lng']);
        $lat2 = deg2rad($to_office_data['lat']);
        $lon2 = deg2rad($to_office_data['lng']);
        
        $delta_lat = $lat2 - $lat1;
        $delta_lon = $lon2 - $lon1;
        
        $a = sin($delta_lat / 2) * sin($delta_lat / 2) + 
             cos($lat1) * cos($lat2) * 
             sin($delta_lon / 2) * sin($delta_lon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = 6371 * $c; // Earth's radius in km
        $duration_min = 0; // Will be calculated later based on distance and carrier speed
    } else {
        $distance = floatval($routeData['distance_km']);
        $duration_min = intval($routeData['duration_min']);
    }
    
    // Adjust weight for letters
    if ($package_type === 'letter') {
        $weight = $letter_count * 0.02; // weight per letter is 0.02 kg
    }
    
    // Check weight limit
    if ($weight > $carrier['max_weight']) {
        throw new Exception("Weight exceeds carrier limit");
    }
    
    // Calculate base cost using the new formula
    $base_cost = $carrier['base_cost'];
    $weight_cost = $weight * $carrier['cost_per_kg'];
    $distance_cost = $distance * $carrier['cost_per_km'];
    $cod_cost = ($cash_on_delivery_amount > 0) ? $cash_on_delivery_amount * 0.05 : 0; // 5% of COD amount
    
    // Calculate base cost
    $cost = $base_cost + $weight_cost + $distance_cost + $cod_cost;
    
    // Store additional costs for breakdown
    $insurance_cost = 0;
    $packaging_cost = 0;
    $fragile_cost = 0;
    
    // Apply insurance (after base calculation)
    if ($insurance) {
        $insurance_cost = $cost * 0.02;  // 2% of current cost
        $cost += $insurance_cost;
    }
    
    // Apply packaging
    if ($packaging) {
        $packaging_cost = 3.00;
        $cost += $packaging_cost;
    }
    
    // Apply fragile
    if ($fragile) {
        $fragile_cost = $cost * 0.01;  // 1% of current cost
        $cost += $fragile_cost;
    }
    
    // Apply minimum cost for letters
    if ($package_type === 'letter') {
        $cost = max($cost, 2.5);
    }
    
    // Calculate duration if not available from route data
    if ($duration_min == 0) {
        // Calculate based on distance and carrier speed
        $hours = $distance / $carrier['speed_kmh'];
        $duration_min = $hours * 60;
    }
    
    $cost = round($cost, 2);
    
    if ($with_breakdown) {
        return [
            'cost' => $cost,
            'distance' => $distance,
            'duration_min' => $duration_min,
            'breakdown' => [
                'base_cost' => $base_cost,
                'weight_cost' => $weight_cost,
                'distance_cost' => $distance_cost,
                'cod_cost' => $cod_cost,
                'insurance_cost' => $insurance_cost,
                'packaging_cost' => $packaging_cost,
                'fragile_cost' => $fragile_cost
            ]
        ];
    } else {
        return [
            'cost' => $cost,
            'distance' => $distance,
            'duration_min' => $duration_min
        ];
    }
}
?>