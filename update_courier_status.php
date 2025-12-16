<?php
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'courier') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$order_id || !in_array($status, ['assigned', 'in_transit', 'delivered_to_center', 'completed'])) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

$courier_id = $_SESSION['user']['id'];

try {
    // Check if this courier is assigned to this order
    $check = $db->prepare("
        SELECT ca.id 
        FROM courier_assignments ca 
        WHERE ca.order_id = ? AND ca.courier_id = ?
    ");
    $check->execute([$order_id, $courier_id]);
    if (!$check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Вы не назначены на этот заказ']);
        exit;
    }

    // Update the assignment status
    $stmt = $db->prepare("UPDATE courier_assignments SET status = ?, completed_at = CASE WHEN ? = 'completed' THEN NOW() ELSE completed_at END WHERE order_id = ? AND courier_id = ?");
    $stmt->execute([$status, $status, $order_id, $courier_id]);

    // Update the order status based on courier action
    if ($status === 'delivered_to_center') {
        // When courier delivers to center, update the order status to 'sort_center'
        $order_update = $db->prepare("UPDATE orders SET tracking_status = 'sort_center' WHERE id = ?");
        $order_update->execute([$order_id]);
    } elseif ($status === 'completed') {
        // When assignment is completed, update order status to 'out_for_delivery' (for next leg)
        $order_update = $db->prepare("UPDATE orders SET tracking_status = 'out_for_delivery' WHERE id = ?");
        $order_update->execute([$order_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Статус обновлен']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления статуса: ' . $e->getMessage()]);
}