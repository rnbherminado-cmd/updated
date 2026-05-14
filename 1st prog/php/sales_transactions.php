<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.html?error=Please login first');
    exit();
}

$role = $_SESSION['users_role'] ?? 'clerk';
$storeId = $_SESSION['store_id'] ?? 0;
$connection->close();

include '../html/sales_transactions.html';
?>
