<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data and ensure id is properly sanitized
$id = isset($_POST['accountId']) ? intval($_POST['accountId']) : 0;
$name = $conn->real_escape_string($_POST['editName']);
$birthday = $conn->real_escape_string($_POST['editBirthday']);
$address = $conn->real_escape_string($_POST['editAddress']);
$contact = $conn->real_escape_string($_POST['editContactNumber']);
$email = $conn->real_escape_string($_POST['editEmail']);
$role = $conn->real_escape_string($_POST['editRole']);
$rfid = $conn->real_escape_string($_POST['editRFID']);
$pin = $conn->real_escape_string($_POST['editPin']);

// Check if the ID exists in the database
$check_sql = "SELECT id FROM users WHERE id = ?";
if ($check_stmt = $conn->prepare($check_sql)) {
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // SQL query for updating the account
        $sql = "UPDATE users 
                SET name = ?, birthday = ?, address = ?, contact = ?, email = ?, role = ?, rfid = ?, pin = ?
                WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssssi", $name, $birthday, $address, $contact, $email, $role, $rfid, $pin, $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Record updated successfully";
                } else {
                    echo "No record updated. Please check if the data is unchanged.";
                }
            } else {
                echo "Error executing statement: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "No record found with the provided ID.";
    }

    $check_stmt->close();
} else {
    echo "Error preparing check statement: " . $conn->error;
}

$conn->close();
?>
