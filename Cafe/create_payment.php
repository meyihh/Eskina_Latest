<?php
header("Content-Type: application/json");

$order_id = $_POST['order_id'] ?? 0;
$method   = $_POST['method'] ?? 'gcash';
$amount   = $_POST['amount'] ?? 0;

// For now, use a static QR stored in /eskina/cafe/qrs/test_qr.png
$qr_url = "http://192.168.1.20/eskina/cafe/qrs/test_qr.png";

echo json_encode([
    "success" => true,
    "qr_url" => $qr_url
]);