<?php
require_once 'db.php';

echo "Populating offices table with sample data...\n";

// Sample offices data for Belarus
$offices_data = [
    // Belpost offices
    ['carrier_id' => 1, 'city' => 'Минск', 'address' => 'ул. Свердлова, 15', 'lat' => 53.904133, 'lng' => 27.557541],
    ['carrier_id' => 1, 'city' => 'Минск', 'address' => 'пр. Независимости, 12', 'lat' => 53.884133, 'lng' => 27.557541],
    ['carrier_id' => 1, 'city' => 'Минск', 'address' => 'ул. Карвата, 84', 'lat' => 53.864133, 'lng' => 27.557541],
    ['carrier_id' => 1, 'city' => 'Гомель', 'address' => 'ул. Советская, 10', 'lat' => 52.441330, 'lng' => 30.985740],
    ['carrier_id' => 1, 'city' => 'Могилев', 'address' => 'ул. Первомайская, 5', 'lat' => 53.904133, 'lng' => 30.337541],
    ['carrier_id' => 1, 'city' => 'Витебск', 'address' => 'ул. Октябрьская, 20', 'lat' => 55.181330, 'lng' => 30.205740],
    ['carrier_id' => 1, 'city' => 'Гродно', 'address' => 'ул. Социалистическая, 18', 'lat' => 53.671330, 'lng' => 23.835740],
    ['carrier_id' => 1, 'city' => 'Брест', 'address' => 'ул. Советская, 30', 'lat' => 52.081330, 'lng' => 23.635740],
    
    // Emspost offices
    ['carrier_id' => 2, 'city' => 'Минск', 'address' => 'ул. Кальварийская, 24', 'lat' => 53.894133, 'lng' => 27.547541],
    ['carrier_id' => 2, 'city' => 'Минск', 'address' => 'пр. Дзержинского, 10', 'lat' => 53.874133, 'lng' => 27.537541],
    ['carrier_id' => 2, 'city' => 'Минск', 'address' => 'ул. Тимирязева, 67', 'lat' => 53.854133, 'lng' => 27.527541],
    ['carrier_id' => 2, 'city' => 'Гомель', 'address' => 'ул. Машерова, 2', 'lat' => 52.431330, 'lng' => 30.975740],
    ['carrier_id' => 2, 'city' => 'Могилев', 'address' => 'ул. Ботаническая, 1', 'lat' => 53.894133, 'lng' => 30.325740],
    ['carrier_id' => 2, 'city' => 'Витебск', 'address' => 'ул. Победы, 35', 'lat' => 55.171330, 'lng' => 30.195740],
    ['carrier_id' => 2, 'city' => 'Гродно', 'address' => 'ул. Врублевского, 7', 'lat' => 53.661330, 'lng' => 23.825740],
    ['carrier_id' => 2, 'city' => 'Брест', 'address' => 'ул. Космонавтов, 15', 'lat' => 52.071330, 'lng' => 23.625740],
    
    // DPD offices
    ['carrier_id' => 3, 'city' => 'Минск', 'address' => 'ул. Карвата, 89', 'lat' => 53.884133, 'lng' => 27.567541],
    ['carrier_id' => 3, 'city' => 'Минск', 'address' => 'ул. Немига, 5', 'lat' => 53.904133, 'lng' => 27.547541],
    ['carrier_id' => 3, 'city' => 'Минск', 'address' => 'ул. Купревича, 1', 'lat' => 53.864133, 'lng' => 27.577541],
    ['carrier_id' => 3, 'city' => 'Гомель', 'address' => 'ул. Ильича, 65', 'lat' => 52.451330, 'lng' => 30.995740],
    ['carrier_id' => 3, 'city' => 'Могилев', 'address' => 'ул. Космонавтов, 6', 'lat' => 53.914133, 'lng' => 30.345740],
    ['carrier_id' => 3, 'city' => 'Витебск', 'address' => 'ул. Кирова, 30', 'lat' => 55.191330, 'lng' => 30.215740],
    ['carrier_id' => 3, 'city' => 'Гродно', 'address' => 'ул. Курчатова, 10', 'lat' => 53.681330, 'lng' => 23.845740],
    ['carrier_id' => 3, 'city' => 'Брест', 'address' => 'ул. Махновича, 2', 'lat' => 52.091330, 'lng' => 23.645740],
    
    // SDEK offices
    ['carrier_id' => 4, 'city' => 'Минск', 'address' => 'ул. Бобруйская, 6', 'lat' => 53.874133, 'lng' => 27.587541],
    ['carrier_id' => 4, 'city' => 'Минск', 'address' => 'ул. Притыцкого, 50', 'lat' => 53.894133, 'lng' => 27.537541],
    ['carrier_id' => 4, 'city' => 'Минск', 'address' => 'ул. Кальварийская, 40', 'lat' => 53.854133, 'lng' => 27.597541],
    ['carrier_id' => 4, 'city' => 'Гомель', 'address' => 'ул. Речицкая, 2', 'lat' => 52.421330, 'lng' => 30.965740],
    ['carrier_id' => 4, 'city' => 'Могилев', 'address' => 'ул. Космонавтов, 40', 'lat' => 53.884133, 'lng' => 30.315740],
    ['carrier_id' => 4, 'city' => 'Витебск', 'address' => 'ул. Студенческая, 20', 'lat' => 55.161330, 'lng' => 30.185740],
    ['carrier_id' => 4, 'city' => 'Гродно', 'address' => 'ул. Ожешко, 2', 'lat' => 53.651330, 'lng' => 23.815740],
    ['carrier_id' => 4, 'city' => 'Брест', 'address' => 'ул. Машерова, 4', 'lat' => 52.061330, 'lng' => 23.615740],
    
    // Boxberry offices
    ['carrier_id' => 5, 'city' => 'Минск', 'address' => 'ул. Тимирязева, 125', 'lat' => 53.844133, 'lng' => 27.607541],
    ['carrier_id' => 5, 'city' => 'Минск', 'address' => 'ул. Карвата, 90', 'lat' => 53.864133, 'lng' => 27.617541],
    ['carrier_id' => 5, 'city' => 'Минск', 'address' => 'ул. Притыцкого, 60', 'lat' => 53.884133, 'lng' => 27.627541],
    ['carrier_id' => 5, 'city' => 'Гомель', 'address' => 'ул. Матвеева, 10', 'lat' => 52.461330, 'lng' => 31.005740],
    ['carrier_id' => 5, 'city' => 'Могилев', 'address' => 'ул. Ботаническая, 20', 'lat' => 53.924133, 'lng' => 30.355740],
    ['carrier_id' => 5, 'city' => 'Витебск', 'address' => 'ул. Ленина, 50', 'lat' => 55.201330, 'lng' => 30.225740],
    ['carrier_id' => 5, 'city' => 'Гродно', 'address' => 'ул. Курчатова, 20', 'lat' => 53.691330, 'lng' => 23.855740],
    ['carrier_id' => 5, 'city' => 'Брест', 'address' => 'ул. Московская, 250', 'lat' => 52.101330, 'lng' => 23.655740],
];

try {
    // Clear existing offices
    $db->exec("DELETE FROM offices");
    echo "Cleared existing offices data\n";
    
    // Insert new offices
    $stmt = $db->prepare("INSERT INTO offices (carrier_id, city, address, lat, lng) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($offices_data as $office) {
        $stmt->execute([
            $office['carrier_id'],
            $office['city'],
            $office['address'],
            $office['lat'],
            $office['lng']
        ]);
    }
    
    echo "Successfully inserted " . count($offices_data) . " offices\n";
    
    // Update carriers with colors if not already set
    $carriers = $db->query("SELECT id, name FROM carriers")->fetchAll();
    foreach ($carriers as $carrier) {
        $color = match($carrier['name']) {
            'Белпочта', 'Belpost' => '#e74c3c',
            'Европочта', 'Emspost' => '#3498db',
            'DPD' => '#9b59b6',
            'СДЭК', 'SDEK' => '#f39c12',
            'Boxberry' => '#1abc9c',
            default => '#007bff'
        };
        
        $stmt = $db->prepare("UPDATE carriers SET color = ? WHERE id = ?");
        $stmt->execute([$color, $carrier['id']]);
    }
    
    echo "Updated carrier colors\n";
    echo "Database population completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>