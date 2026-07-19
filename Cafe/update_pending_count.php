<?php
include '../config.php';

// Just recalculate current pending count to keep things updated
$today = date("Y-m-d");
$query = $conn->prepare("
    SELECT COUNT(DISTINCT o.customer_name) AS pending_count
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.status != 'DONE' AND DATE(o.created_at) = ?
");
$query->bind_param("s", $today);
$query->execute();
$result = $query->get_result()->fetch_assoc();

echo json_encode(["count" => $result['pending_count'] ?? 0]);
?>
