<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$query = "SELECT store_id, store_name FROM store ORDER BY store_name";
$result = $connection->query($query);

$stores = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
}

$connection->close();
echo json_encode($stores);
