<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

if ($_SESSION['users_role'] !== 'owner') {
    header("Location: product_management.php?error=Only the owner can delete products");
    exit();
}

$product_id = $_POST['product_id'] ?? null;

if (empty($product_id)) {
    header("Location: product_management.php?error=Invalid product ID");
    exit();
}

$stmt = $connection->prepare("DELETE FROM product WHERE product_id = ?");
$stmt->bind_param("s", $product_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: product_management.php?message=Product deleted successfully");
    } else {
        header("Location: product_management.php?error=Product not found");
    }
} else {
    header("Location: product_management.php?error=Failed to delete product");
}

$stmt->close();
$connection->close();
exit();
?>
