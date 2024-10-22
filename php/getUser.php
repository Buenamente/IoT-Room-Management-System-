<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

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

// SQL query to fetch the logged-in user's FullName
$loggedInUsername = $_SESSION['username']; // Use the correct session variable
$sql = "SELECT FullName FROM account WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUsername);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();

$stmt->close();
$conn->close();

// Output the FullName
echo htmlspecialchars($fullName);
?>
