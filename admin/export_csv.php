<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'senti-shield';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers to indicate that the content is a CSV file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=access_logs.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('ID', 'RFID', 'Name', 'Access Time', 'Role'));

// Fetch the data from the access_logs table
$sql = "SELECT id, rfid, name, access_time, role FROM access_logs";
$result = $conn->query($sql);

// Check if any rows returned
if ($result->num_rows > 0) {
    // Output each row of the data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    echo "No data found";
}

fclose($output);
$conn->close();
?>
