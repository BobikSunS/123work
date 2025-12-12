<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $operatorId = (int)$_POST['operator_id'];
    $fromOfficeId = (int)$_POST['from_office_id'];
    $toOfficeId = (int)$_POST['to_office_id'];
    $weight = floatval($_POST['weight_kg']);
    $distance = floatval($_POST['distance_km']);
    $duration = (int)$_POST['duration_min'];
    $finalPrice = floatval($_POST['final_price']);
    $insurance = (bool)$_POST['insurance'];
    $fragile = (bool)$_POST['fragile'];
    $packaging = (bool)$_POST['packaging'];
    $senderName = trim($_POST['sender_name']);
    $senderPhone = trim($_POST['sender_phone']);
    $recipientName = trim($_POST['recipient_name']);
    $recipientPhone = trim($_POST['recipient_phone']);
    $recipientAddress = trim($_POST['recipient_address']);
    $paymentMethod = trim($_POST['payment_method']);
    $comment = trim($_POST['comment']);
    
    // Validate required fields
    if (empty($senderName) || empty($senderPhone) || empty($recipientName) || empty($recipientPhone) || empty($recipientAddress) || empty($paymentMethod)) {
        $error = "Пожалуйста, заполните все обязательные поля!";
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
    }
    
    // Prepare order data
    $orderData = [
        'operator_id' => $operatorId,
        'from_office_id' => $fromOfficeId,
        'to_office_id' => $toOfficeId,
        'sender_name' => $senderName,
        'sender_phone' => $senderPhone,
        'recipient_name' => $recipientName,
        'recipient_phone' => $recipientPhone,
        'recipient_address' => $recipientAddress,
        'weight_kg' => $weight,
        'distance_km' => $distance,
        'duration_min' => $duration,
        'final_price' => $finalPrice,
        'insurance' => $insurance,
        'fragile' => $fragile,
        'packaging' => $packaging,
        'payment_method' => $paymentMethod,
        'comment' => $comment
    ];
    
    // Insert order into database
    $orderId = insertOrder($orderData);
    
    if ($orderId) {
        // Redirect to success page
        header("Location: order_success.php?id=" . $orderId);
        exit;
    } else {
        $error = "Ошибка при создании заказа!";
        echo json_encode(['success' => false, 'message' => $error]);
        exit;
    }
} else {
    // If not POST request, redirect to order form
    header("Location: index.php");
    exit;
}
?>