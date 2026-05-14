<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$query = "SELECT prov_id, prov_name FROM province ORDER BY prov_name";
$result = $connection->query($query);

$provinces = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $provinces[] = $row;
    }
}

$connection->close();
echo json_encode($provinces);
