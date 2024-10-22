<?php
session_start();

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

$response = [
    'status' => 'error',
    'message' => '',
];

// Check database connection
if ($conn->connect_error) {
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    $response['message'] = "You are not logged in. Redirecting to login page.";
    echo json_encode($response);
    header("Location: login.html");
    exit();
}

// Get the user ID from the session
$userID = $_SESSION['id'];

// Initialize a variable for the profile picture path
$profilePicPath = null;

// Handle profile picture upload
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profilePic']['tmp_name'];
    $fileName = $_FILES['profilePic']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../uploads/profile_pictures/';
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            if (!mkdir($uploadFileDir, 0777, true)) {
                $response['message'] = "Failed to create directory: " . $uploadFileDir;
                echo json_encode($response);
                exit();
            }
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Delete old profile picture if exists
            $stmt = $conn->prepare("SELECT ProfilePic FROM account WHERE id = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($oldProfilePic);
            $stmt->fetch();
            $stmt->close();

            if (!empty($oldProfilePic) && file_exists($oldProfilePic)) {
                unlink($oldProfilePic);
            }

            $profilePicPath = $dest_path;
        } else {
            $response['message'] = "Failed to move the uploaded file.";
            echo json_encode($response);
            exit();
        }
    } else {
        $response['message'] = "Invalid file extension. Only JPG, JPEG, and PNG files are allowed.";
        echo json_encode($response);
        exit();
    }
}

// Prepare the SQL update statement
$sql = "UPDATE account SET FullName = ?, Address = ?, Birthday = ?, ContactNumber = ?, Email = ?";
if ($profilePicPath) {
    $sql .= ", ProfilePic = ?";
}
$sql .= " WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($profilePicPath) {
    $stmt->bind_param(
        "ssssssi",
        $_POST['username'],
        $_POST['address'],
        $_POST['birthday'],
        $_POST['contact'],
        $_POST['email'],
        $profilePicPath,
        $userID
    );
} else {
    $stmt->bind_param(
        "sssssi",
        $_POST['username'],
        $_POST['address'],
        $_POST['birthday'],
        $_POST['contact'],
        $_POST['email'],
        $userID
    );
}

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Profile updated successfully.';
} else {
    $response['message'] = 'Failed to update profile: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
