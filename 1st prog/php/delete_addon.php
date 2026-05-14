<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.html?error=Please login first");
    exit();
}

if ($_SESSION['users_role'] !== 'owner') {
    header("Location: product_addons.php?error=Only the owner can delete add-ons");
    exit();
}

$addon_id = $_POST['addon_id'] ?? null;

if (empty($addon_id)) {
    header("Location: product_addons.php?error=Invalid add-on ID");
    exit();
}

$stmt = $connection->prepare("DELETE FROM product_addons WHERE addon_id = ?");
$stmt->bind_param("s", $addon_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: product_addons.php?message=Add-on deleted successfully");
    } else {
        header("Location: product_addons.php?error=Add-on not found");
    }
} else {
    header("Location: product_addons.php?error=Failed to delete add-on");
}

$stmt->close();
$connection->close();
exit();
?>

}

$updateStmt->close();
$connection->close();
exit();
?>
