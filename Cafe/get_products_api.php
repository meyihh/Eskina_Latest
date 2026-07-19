<?php
require_once 'config.php';
header("Content-Type: application/json");

// Get all categories
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY id ASC");
while ($row = $result->fetch_assoc()) {
    $categories[$row['id']] = [
        "id" => $row['id'],
        "slug" => $row['slug'],
        "name" => $row['name'],
        "products" => []
    ];
}

// Get all products
$result = $conn->query("SELECT * FROM products ORDER BY category_id ASC, id ASC");
while ($row = $result->fetch_assoc()) {
    $categories[$row['category_id']]['products'][] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "meta" => $row['meta'],
        "price" => $row['price'],
        "image" => $row['image'],
        "best_seller" => $row['best_seller']
    ];
}

// Return as JSON
echo json_encode(array_values($categories));
?>