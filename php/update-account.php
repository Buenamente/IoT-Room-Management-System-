<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data and ensure id is properly sanitized//
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$fullname = $conn->real_escape_string($_POST['name']);
$birthday = $conn->real_escape_string($_POST['birthday']);
$address = $conn->real_escape_string($_POST['address']);
$contact = $conn->real_escape_string($_POST['contact']);
$email = $conn->real_escape_string($_POST['email']);
$role = $conn->real_escape_string($_POST['role']);

//this is to Check if the ID exists in the database//
$check_sql = "SELECT id FROM account WHERE id = ?";
if ($check_stmt = $conn->prepare($check_sql)) {
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // this is the sql query for updating the account//
        $sql = "UPDATE account 
                SET FullName = ?, Birthday = ?, Address = ?, ContactNumber = ?, Email = ?, Role = ? 
                WHERE id = ?";
         // this is the sql query for updating the account//
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssi", $fullname, $birthday, $address, $contact, $email, $role, $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Record updated successfully";
                } else {
                    echo "No record updated. Please check if the ID exists or if the data is unchanged.";
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
