<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.html?error=Please login first');
    exit();
}

$role = $_SESSION['users_role'] ?? 'clerk';
$storeId = $_SESSION['store_id'] ?? 0;
$message = isset($_GET['message']) ? $_GET['message'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Get all add-ons
$addons = [];
$result = $connection->query('SELECT pa.addon_id, pa.addon_name, pa.addon_description, pa.addon_quantity, pa.addon_price, pa.addon_date_added, s.store_name FROM product_addons pa LEFT JOIN store s ON pa.store_id = s.store_id ORDER BY pa.addon_name');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $addons[] = $row;
    }
}

// Get add-ons for reduce stock dropdown
$reduceAddons = [];
$reduceQuery = 'SELECT pa.addon_id, pa.addon_name, pa.addon_quantity FROM product_addons pa';
if ($role === 'clerk') {
    $reduceQuery .= ' WHERE pa.store_id = ' . intval($storeId);
}
$reduceQuery .= ' ORDER BY pa.addon_name';
$reduceResult = $connection->query($reduceQuery);
if ($reduceResult) {
    while ($row = $reduceResult->fetch_assoc()) {
        $reduceAddons[] = $row;
    }
}

$connection->close();

include '../html/product_addons.html';
?>
