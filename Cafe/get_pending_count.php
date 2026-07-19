<?php
include '../config.php';
$today = date("Y-m-d");

$stmt = $conn->prepare("
    SELECT COUNT(*) AS count
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.status != 'DONE' AND DATE(o.created_at) = ?
");
$stmt->bind_param("s", $today);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo json_encode(["count" => $row['count'] ?? 0]);
