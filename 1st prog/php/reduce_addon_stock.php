<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

$addon_id = $_POST['addon_id'] ?? null;
$reduce_quantity = isset($_POST['reduce_quantity']) ? intval($_POST['reduce_quantity']) : 0;
$store_id = $_SESSION['store_id'] ?? 0;

if (empty($addon_id) || $reduce_quantity <= 0) {
    header("Location: product_addons.php?error=Please select a valid add-on and quantity");
    exit();
}

$stmt = $connection->prepare("SELECT addon_quantity, store_id FROM product_addons WHERE addon_id = ?");
$stmt->bind_param("s", $addon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $connection->close();
    header("Location: product_addons.php?error=Add-on not found");
    exit();
}

$row = $result->fetch_assoc();
$currentQuantity = (int)$row['addon_quantity'];
$addonStoreId = $row['store_id'];
$stmt->close();

if ($_SESSION['users_role'] === 'clerk' && intval($addonStoreId) !== intval($store_id)) {
    $connection->close();
    header("Location: product_addons.php?error=Clerks can only update add-ons from their store");
    exit();
}

$newQuantity = max(0, $currentQuantity - $reduce_quantity);

$updateStmt = $connection->prepare("UPDATE product_addons SET addon_quantity = ? WHERE addon_id = ?");
$updateStmt->bind_param("is", $newQuantity, $addon_id);

if ($updateStmt->execute()) {
    header("Location: product_addons.php?message=Add-on stock reduced successfully");
} else {
    header("Location: product_addons.php?error=Failed to reduce add-on stock");
}

$updateStmt->close();
$connection->close();
exit();
?>
