<?php
// save_order.php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php'; // ✅ make sure this defines $conn (MySQLi connection)

try {
    // Read JSON body
    $raw = file_get_contents("php://input");
    if ($raw === false) throw new Exception("No request body");

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['cart'], $data['payment'], $data['orderType'], $data['customerName'])) {
        throw new Exception("Invalid request data");
    }

    $cart = $data['cart'];
    $payment = trim((string)$data['payment']);
    $customerName = trim((string)$data['customerName']);

    // Normalize order type
    $rawOrderType = strtolower(trim((string)$data['orderType']));
    $rawOrderType = str_replace([' ', '-', '_'], '', $rawOrderType);

    switch ($rawOrderType) {
        case 'dinein':
            $orderType = 'DINE-IN';
            break;
        case 'takeout':
            $orderType = 'TAKE-OUT';
            break;
        case 'online':
            $orderType = 'ONLINE';
            break;
        default:
            $orderType = 'DINE-IN';
    }

    if (!is_array($cart) || count($cart) === 0) throw new Exception("Cart is empty");

    // Sanitize and calculate total
    $total = 0.0;
    foreach ($cart as $i => $item) {
        $name = isset($item['name']) ? trim((string)$item['name']) : '';
        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $priceStr = isset($item['price']) ? (string)$item['price'] : '0';
        $price = (float)preg_replace('/[^\d.]/', '', $priceStr);

        if ($name === '' || $qty <= 0 || $price < 0) throw new Exception("Invalid cart item at index $i");

        $total += ($price * $qty);
        $cart[$i]['_name'] = $name;
        $cart[$i]['_qty'] = $qty;
        $cart[$i]['_price'] = $price;
    }

    $points = isset($data['points']) ? (int)$data['points'] : floor($total / 100);

    // ✅ Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (payment_method, order_type, customer_name, total_price, points) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("sssdi", $payment, $orderType, $customerName, $total, $points);
    if (!$stmt->execute()) throw new Exception("Order insert failed: " . $stmt->error);
    $orderId = $stmt->insert_id;
    $stmt->close();

    // ✅ Insert order items
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
    if (!$itemStmt) throw new Exception("Prepare for items failed: " . $conn->error);

    foreach ($cart as $item) {
        $itemStmt->bind_param(
            "isdi",
            $orderId,
            $item['_name'],
            $item['_price'],
            $item['_qty']
        );
        if (!$itemStmt->execute()) throw new Exception("Item insert failed: " . $itemStmt->error);
    }
    $itemStmt->close();

    // ✅ Success response
    echo json_encode([
        "status" => "success",
        "message" => "Order saved successfully!",
        "order_id" => $orderId,
        "total" => $total,
        "orderType" => $orderType,
        "customerName" => $customerName,
        "points" => $points
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error while saving order.",
        "details" => $e->getMessage()
    ]);
    exit;
}
?>