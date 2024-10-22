<!-- delete button for historylogs -->

<?php

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

if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Make sure the id is an integer to avoid SQL injection

    // Prepare the SQL DELETE statement
    $stmt = $conn->prepare("DELETE FROM access_logs WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' means the bound variable is an integer
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Record deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete the record"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
}

$conn->close();
?>

<!-- delete button for historylogs -->

