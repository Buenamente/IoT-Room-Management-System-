<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get account ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare SQL query
$sql = "SELECT rfid, name, address, birthday, pin, role, DateRegistered, email, contact FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No account found']);
}

$stmt->close();
$conn->close();
?>
