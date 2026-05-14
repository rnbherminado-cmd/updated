<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

if ($_SESSION['users_role'] !== 'owner') {
    header("Location: product_management.php?error=Only the owner can add products");
    exit();
}

$product_name = trim($_POST['product_name'] ?? '');
$product_quantity = isset($_POST['product_quantity']) ? intval($_POST['product_quantity']) : 0;
$store_id = $_SESSION['store_id'] ?? 0;

if (empty($product_name) || $product_quantity <= 0) {
    header("Location: product_management.php?error=Please provide a valid product name and quantity");
    exit();
}

$nextIdResult = $connection->query("SELECT COALESCE(MAX(product_id), 0) + 1 AS next_id FROM product");
$nextId = 1;
if ($nextIdResult) {
    $row = $nextIdResult->fetch_assoc();
    $nextId = $row['next_id'];
}

$stmt = $connection->prepare("INSERT INTO product (product_id, store_id, product_name, product_quantity, product_date_added) VALUES (?, ?, ?, ?, CURDATE())");
$stmt->bind_param("sssi", $nextId, $store_id, $product_name, $product_quantity);

if ($stmt->execute()) {
    header("Location: product_management.php?message=Product added successfully");
} else {
    header("Location: product_management.php?error=Failed to add product");
}
$stmt->close();
$connection->close();
exit();
