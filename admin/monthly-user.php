<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count the number of users for each month in the current year
$sql = "SELECT MONTH(access_time) as month, COUNT(*) as count 
        FROM access_logs 
        WHERE YEAR(access_time) = YEAR(CURDATE()) 
        GROUP BY MONTH(access_time)";

$result = $conn->query($sql);

$monthly_data = array_fill(1, 12, 0); // Initialize an array with 12 zeros (for 12 months)

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $monthly_data[intval($row['month'])] = intval($row['count']);
    }
}

$conn->close();

$monthly_data_json = json_encode(array_values($monthly_data));
?>
