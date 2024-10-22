<?php
header('Content-Type: application/json');

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "senti-shield"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(array('status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "INSERT INTO `account` (`FullName`, `Username`, `Birthday`, `Address`, `ContactNumber`, `Email`, `Role`, `Password`) 
            VALUES ('$fullname', '$username', '$birthday', '$address', '$contact', '$email', '$role', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array('status' => 'success', 'message' => 'New record created successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error: ' . $conn->error));
    }
}

$conn->close();
?>
