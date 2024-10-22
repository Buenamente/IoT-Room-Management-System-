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

// Update profile picture
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profilePic']['tmp_name'];
    $fileName = $_FILES['profilePic']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/profile_pictures/';
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            if (!mkdir($uploadFileDir, 0777, true)) {
                $response['message'] = "Failed to create directory: " . $uploadFileDir;
                echo json_encode($response);
                exit();
            }
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $stmt = $conn->prepare("SELECT ProfilePic FROM account WHERE id = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($oldProfilePic);
            $stmt->fetch();
            $stmt->close();

            if (!empty($oldProfilePic) && file_exists($oldProfilePic)) {
                unlink($oldProfilePic);
            }

            $stmt = $conn->prepare("UPDATE account SET ProfilePic = ? WHERE id = ?");
            $stmt->bind_param("si", $dest_path, $userID);
            if (!$stmt->execute()) {
                $response['message'] = "Error updating profile picture: " . $stmt->error;
                echo json_encode($response);
                exit();
            }
            $stmt->close();

            $response['status'] = 'success';
            $response['message'] = "Profile picture updated successfully.";
        } else {
            $response['message'] = "Error moving the uploaded file. Check directory permissions.";
        }
    } else {
        $response['message'] = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
    }
}

// Update other user information
if (isset($_POST['username']) && isset($_POST['address']) && isset($_POST['birthday']) && isset($_POST['contact']) && isset($_POST['email'])) {
    $username = $_POST['username'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE account SET FullName = ?, Address = ?, Birthday = ?, ContactNumber = ?, Email = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $username, $address, $birthday, $contact, $email, $userID);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Profile updated successfully.";
    } else {
        $response['message'] = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

echo json_encode($response);
?>
