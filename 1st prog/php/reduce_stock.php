<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

$product_id = $_POST['product_id'] ?? null;
$reduce_quantity = isset($_POST['reduce_quantity']) ? intval($_POST['reduce_quantity']) : 0;
$store_id = $_SESSION['store_id'] ?? 0;

if (empty($product_id) || $reduce_quantity <= 0) {
    header("Location: product_management.php?error=Please select a valid product and quantity");
    exit();
}

$stmt = $connection->prepare("SELECT product_quantity, store_id FROM product WHERE product_id = ?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $connection->close();
    header("Location: product_management.php?error=Product not found");
    exit();
}

$row = $result->fetch_assoc();
$currentQuantity = (int)$row['product_quantity'];
$productStoreId = $row['store_id'];
$stmt->close();

if ($_SESSION['users_role'] === 'clerk' && intval($productStoreId) !== intval($store_id)) {
    $connection->close();
    header("Location: product_management.php?error=Clerks can only update products from their store");
    exit();
}

$newQuantity = max(0, $currentQuantity - $reduce_quantity);

$updateStmt = $connection->prepare("UPDATE product SET product_quantity = ? WHERE product_id = ?");
$updateStmt->bind_param("is", $newQuantity, $product_id);

if ($updateStmt->execute()) {
    header("Location: product_management.php?message=Stock updated successfully");
} else {
    header("Location: product_management.php?error=Failed to update stock");
}
$updateStmt->close();
$connection->close();
exit();
