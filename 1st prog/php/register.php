<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $suffix_name = trim($_POST['suffix_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $barangay_id = !empty($_POST['barangay_id']) ? intval($_POST['barangay_id']) : 0;
    $store_option = $_POST['store_option'];
    $store_id = !empty($_POST['store_id']) ? $_POST['store_id'] : null;
    $custom_store_name = !empty($_POST['custom_store_name']) ? trim($_POST['custom_store_name']) : null;

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($username) || empty($password)) {
        header("Location: ../index.html?error=First name, last name, username and password are required&form=register");
        exit();
    }

    if ($barangay_id <= 0) {
        header("Location: ../index.html?error=Please select your location (Province, Municipality, and Barangay)&form=register");
        exit();
    }

    if ($store_option === 'existing' && empty($store_id)) {
        header("Location: ../index.html?error=Please select a store&form=register");
        exit();
    }

    if ($store_option === 'new' && empty($custom_store_name)) {
        header("Location: ../index.html?error=Please enter a store name&form=register");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../index.html?error=Passwords do not match&form=register");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: ../index.html?error=Password must be at least 6 characters&form=register");
        exit();
    }

    // Check if username already exists in credential table
    $checkUser = $connection->prepare("SELECT users_id FROM credential WHERE cre_username = ?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../index.html?error=Username already registered&form=register");
        $checkUser->close();
        exit();
    }
    $checkUser->close();

    // Start transaction for data integrity
    $connection->begin_transaction();

    try {
        // If creating new store, insert it first
        if ($store_option === 'new') {
            $stmt_store = $connection->prepare("INSERT INTO store (store_name) VALUES (?)");
            $stmt_store->bind_param("s", $custom_store_name);

            if (!$stmt_store->execute()) {
                throw new Exception("Failed to create store");
            }

            $store_id = $connection->insert_id;
            $stmt_store->close();
        }

        // Insert new user into users table
        $stmt1 = $connection->prepare("INSERT INTO users (store_id, barangay_id, users_fname, users_mname, users_sname, users_lname) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("iissss", $store_id, $barangay_id, $first_name, $middle_name, $suffix_name, $last_name);

        if (!$stmt1->execute()) {
            throw new Exception("Failed to create user record");
        }

        $users_id = $connection->insert_id;
        $stmt1->close();

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $cre_status = 1; // Set status to active

        // Insert credentials into credential table
        $stmt2 = $connection->prepare("INSERT INTO credential (users_id, cre_username, cre_password, cre_status) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("issi", $users_id, $username, $hashed_password, $cre_status);

        if (!$stmt2->execute()) {
            throw new Exception("Failed to create credentials");
        }

        $stmt2->close();

        // Commit transaction
        $connection->commit();
        header("Location: ../index.html?message=Registration successful! Please login.&form=login");
        $connection->close();
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $connection->rollback();
        header("Location: ../index.html?error=Registration failed: " . $e->getMessage() . "&form=register");
        $connection->close();
        exit();
    }
}
