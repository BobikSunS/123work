<?php
require 'db.php';

try {
    // Check if routes table exists, if not create it
    $tables = $db->query("SHOW TABLES LIKE 'routes'")->fetchAll();
    
    if (count($tables) == 0) {
        echo "Creating routes table...\n";
        
        $sql = "CREATE TABLE routes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_office INT NOT NULL,
            to_office INT NOT NULL,
            distance_km DECIMAL(8,2) NOT NULL,
            INDEX idx_from_to (from_office, to_office)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        
        $db->exec($sql);
        echo "Routes table created successfully.\n";
    } else {
        echo "Routes table already exists.\n";
    }
    
    // Check if calculated_routes table exists and copy data if needed
    $calc_tables = $db->query("SHOW TABLES LIKE 'calculated_routes'")->fetchAll();
    
    if (count($calc_tables) > 0) {
        // Copy data from calculated_routes to routes if routes is empty
        $route_count = $db->query("SELECT COUNT(*) FROM routes")->fetchColumn();
        if ($route_count == 0) {
            echo "Copying data from calculated_routes to routes...\n";
            
            $stmt = $db->query("SELECT from_office_id, to_office_id, distance_km FROM calculated_routes");
            $rows = $stmt->fetchAll();
            
            $insert_stmt = $db->prepare("INSERT INTO routes (from_office, to_office, distance_km) VALUES (?, ?, ?)");
            
            foreach ($rows as $row) {
                $insert_stmt->execute([$row['from_office_id'], $row['to_office_id'], $row['distance_km']]);
            }
            
            echo "Copied " . count($rows) . " routes from calculated_routes to routes table.\n";
        }
    }
    
    echo "Database fix completed!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>