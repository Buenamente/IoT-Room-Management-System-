<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get account ID from POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Prepare SQL query
$sql = "DELETE FROM account WHERE id = ?";

// Prepare and execute statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Record deleted successfully";
        } else {
            echo "No record deleted. Please check if the ID exists.";
        }
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
