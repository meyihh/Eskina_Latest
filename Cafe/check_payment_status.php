<?php
header("Content-Type: application/json");

$order_id = $_GET['order_id'] ?? 0;

// Simulate payment success
echo json_encode([
    "status" => "paid"
]);