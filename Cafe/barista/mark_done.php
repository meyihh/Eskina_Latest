<?php
include '../config.php';
require_once '../auth.php';

if ($_SESSION['user']['role'] !== 'barista') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$order_id = intval($_POST['order_id'] ?? 0);
if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Invalid order ID"]);
    exit;
}

// Update all items in this order to DONE
$stmt = $conn->prepare("UPDATE order_items SET status='DONE' WHERE order_id=?");
$stmt->bind_param("i", $order_id);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update order"]);
}
