<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Prepare the SQL statement to search by name
    $stmt = $conn->prepare("SELECT id, rfid, name, role, DATE(access_time) as access_date, TIME(access_time) as access_time FROM access_logs WHERE name = ?");
    $stmt->bind_param("s", $name);  // 's' for string type
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}

$conn->close();
?>
