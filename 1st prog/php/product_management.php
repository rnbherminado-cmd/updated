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

$products = [];
$result = $connection->query('SELECT p.product_id, p.product_name, p.product_quantity, p.product_date_added, s.store_name FROM product p LEFT JOIN store s ON p.store_id = s.store_id ORDER BY p.product_name');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$reduceProducts = [];
$reduceQuery = 'SELECT p.product_id, p.product_name, p.product_quantity FROM product p';
if ($role === 'clerk') {
    $reduceQuery .= ' WHERE p.store_id = ' . intval($storeId);
}
$reduceQuery .= ' ORDER BY p.product_name';
$reduceResult = $connection->query($reduceQuery);
if ($reduceResult) {
    while ($row = $reduceResult->fetch_assoc()) {
        $reduceProducts[] = $row;
    }
}

$connection->close();

include '../html/product_management.html';
?>
