<?php
require '../config.php'; // db connection
require_once '../auth.php';

// check if client only asks for "last update"
if (isset($_GET['check'])) {
    $res = $conn->query("SELECT MAX(created_at) as last_update 
                         FROM orders 
                         WHERE id IN (SELECT order_id FROM order_items WHERE status='DONE')");
    $row = $res->fetch_assoc();
    echo $row['last_update'] ?? '';
    exit;
}

function showOrders($conn, $type, $label) {
    echo "<div class='order-column'><h3>$label</h3>";

    $sql = "SELECT o.id, o.customer_name, o.total_price, o.created_at, o.payment_method
            FROM orders o
            WHERE o.order_type = '$type'
              AND o.id IN (SELECT order_id FROM order_items WHERE status='DONE')
            ORDER BY o.created_at DESC
            LIMIT 10";

    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo "<div class='order-card'>
                    <p><strong>Order #:</strong> {$row['id']}</p>
                    <p><strong>Customer:</strong> {$row['customer_name']}</p>
                    <p><strong>Total:</strong> ₱{$row['total_price']}</p>
                    <p><strong>Payment:</strong> {$row['payment_method']}</p>
                    <p><strong>Date & Time:</strong> {$row['created_at']}</p>";

            // Items
            $itemsRes = $conn->query("SELECT product_name, quantity 
                                      FROM order_items 
                                      WHERE order_id = {$row['id']} AND status='DONE'");
            if ($itemsRes && $itemsRes->num_rows > 0) {
                echo "<div class='order-items'><strong>Items:</strong>";
                while ($item = $itemsRes->fetch_assoc()) {
                    echo "<div>{$item['quantity']}x {$item['product_name']}</div>";
                }
                echo "</div>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>No recent $label orders.</p>";
    }

    echo "</div>";
}

showOrders($conn, "DINE-IN", "DINE-IN");
showOrders($conn, "TAKE-OUT", "TAKE OUT");
showOrders($conn, "ONLINE", "ONLINE ORDERS");
?>
