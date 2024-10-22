<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'senti-shield';


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    echo json_encode(array('status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error));
    exit;
}


$sql = "DELETE FROM notification";


if ($conn->query($sql) === TRUE) {
    echo json_encode(array('status' => 'success', 'message' => 'All records deleted successfully.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Error deleting records: ' . $conn->error));
}


$conn->close();
?>
