<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

if ($_SESSION['users_role'] !== 'owner') {
    header("Location: product_addons.php?error=Only the owner can add add-ons");
    exit();
}

$addon_name = trim($_POST['addon_name'] ?? '');
$addon_description = trim($_POST['addon_description'] ?? '');
$addon_quantity = isset($_POST['addon_quantity']) ? intval($_POST['addon_quantity']) : 0;
$addon_price = isset($_POST['addon_price']) ? floatval($_POST['addon_price']) : 0;
$store_id = $_SESSION['store_id'] ?? 0;

if (empty($addon_name) || $addon_quantity <= 0 || $addon_price <= 0) {
    header("Location: product_addons.php?error=Please provide valid add-on name, quantity, and price");
    exit();
}

$nextIdResult = $connection->query("SELECT COALESCE(MAX(addon_id), 0) + 1 AS next_id FROM product_addons");
$nextId = 1;
if ($nextIdResult) {
    $row = $nextIdResult->fetch_assoc();
    $nextId = $row['next_id'];
}

$stmt = $connection->prepare("INSERT INTO product_addons (addon_id, store_id, addon_name, addon_description, addon_quantity, addon_price, addon_date_added) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
$stmt->bind_param("sssiii", $nextId, $store_id, $addon_name, $addon_description, $addon_quantity, $addon_price);

if ($stmt->execute()) {
    header("Location: product_addons.php?message=Add-on created successfully");
} else {
    header("Location: product_addons.php?error=Failed to create add-on");
}
$stmt->close();
$connection->close();
exit();
?>
