<?php
require_once 'db.php';

header('Content-Type: application/json');

$operatorId = isset($_GET['operator_id']) ? (int)$_GET['operator_id'] : 0;

if ($operatorId > 0) {
    $offices = getOfficesByOperator($operatorId);
    echo json_encode(['offices' => $offices]);
} else {
    echo json_encode(['offices' => []]);
}
?>