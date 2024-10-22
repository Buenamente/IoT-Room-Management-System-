<?php
session_start(); // Start the session

$servername = "localhost"; 
$username = "root"; 
$password = "";
$dbname = "senti-shield"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);

        $sql = "SELECT * FROM account WHERE Username='$username' AND Password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $role = $row['Role'];
            $userID = $row['id'];

            // Store user information in session variables
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['id'] = $userID;
            

            $response = [
                'status' => 'success',
                'role' => $role,
                'redirect' => ''
            ];

            switch ($role) {
                case 'moderator':
                    $response['redirect'] = 'admin/index.php';
                    break;
                case 'admin':
                    $response['redirect'] = 'dashboard.php';
                    break;
                default:
                    $response = ['status' => 'error', 'message' => 'Unknown role.'];
                    break;
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Invalid username or password.'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Username or password not provided.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method.'];
}

$conn->close();

// Output the JSON response
header('Content-Type: application/json');
echo json_encode($response);


