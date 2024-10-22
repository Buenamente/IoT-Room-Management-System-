<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify if user is logged in and a moderator
if (!isset($_SESSION['username']) || !isset($_SESSION['id']) || $_SESSION['role'] !== 'moderator') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if location is provided in POST request
if (isset($_POST['location'])) {
    $location = $conn->real_escape_string($_POST['location']);
    
    // Insert room into database
    $sql = "INSERT INTO rooms (location, status, occupied_by, role) VALUES ('$location', 'Available', 'Not Assigned', 'Not Assigned')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

$conn->close();
?>
