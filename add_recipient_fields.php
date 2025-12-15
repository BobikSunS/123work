<?php
require 'db.php';

echo "Adding recipient fields to orders table...\n";

try {
    // Check if recipient_name column exists
    $check_name = $db->query("SHOW COLUMNS FROM orders LIKE 'recipient_name'");
    if ($check_name->rowCount() == 0) {
        $db->exec("ALTER TABLE orders ADD COLUMN recipient_name VARCHAR(255) DEFAULT NULL");
        echo "Added recipient_name column\n";
    } else {
        echo "recipient_name column already exists\n";
    }

    // Check if recipient_address column exists
    $check_address = $db->query("SHOW COLUMNS FROM orders LIKE 'recipient_address'");
    if ($check_address->rowCount() == 0) {
        $db->exec("ALTER TABLE orders ADD COLUMN recipient_address TEXT DEFAULT NULL");
        echo "Added recipient_address column\n";
    } else {
        echo "recipient_address column already exists\n";
    }

    echo "Recipient fields update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>