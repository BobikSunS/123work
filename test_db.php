<?php
require 'db.php';

// Test connection and list tables
echo "Testing database connection...\n";

try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check users table structure
    if (in_array('users', $tables)) {
        echo "\nUsers table structure:\n";
        $columns = $db->query("DESCRIBE users")->fetchAll();
        foreach ($columns as $column) {
            echo "  {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']} - {$column['Default']} - {$column['Extra']}\n";
        }
        
        // Count users by role
        $user_counts = $db->query("
            SELECT role, COUNT(*) as count 
            FROM users 
            GROUP BY role
        ")->fetchAll();
        echo "\nUser counts by role:\n";
        foreach ($user_counts as $uc) {
            echo "  {$uc['role']}: {$uc['count']}\n";
        }
    }
    
    // Check orders table structure
    if (in_array('orders', $tables)) {
        echo "\nOrders table structure:\n";
        $columns = $db->query("DESCRIBE orders")->fetchAll();
        foreach ($columns as $column) {
            echo "  {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']} - {$column['Default']} - {$column['Extra']}\n";
        }
    }
    
    // Check if courier_id column exists in orders table
    if (in_array('orders', $tables)) {
        $result = $db->query("SHOW COLUMNS FROM orders LIKE 'courier_id'");
        if ($result->rowCount() > 0) {
            echo "\nFound courier_id column in orders table\n";
        } else {
            echo "\ncourier_id column does not exist in orders table\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>