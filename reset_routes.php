<?php
require 'db.php';

try {
    // Clear the calculated_routes table to force new route calculations
    $db->exec("DELETE FROM calculated_routes");
    echo "Successfully cleared calculated_routes table. All routes will be recalculated using road-based routing.";
} catch (Exception $e) {
    echo "Error clearing routes: " . $e->getMessage();
}
?>