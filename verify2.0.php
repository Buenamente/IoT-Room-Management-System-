<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle RFID or PIN data
    if (isset($_POST['rfid'])) {
        $input = $_POST['rfid'];
        $column = 'rfid';
    } elseif (isset($_POST['pin'])) {
        $input = $_POST['pin'];
        $column = 'pin';
    } else {
        echo json_encode(["status" => "error", "message" => "No RFID or PIN data received"]);
        exit;
    }

    // Establish a connection to the database
    $conn = new mysqli('localhost', 'root', '', 'senti-shield');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Clean the input to prevent SQL injection
    $input = mysqli_real_escape_string($conn, trim($input));

    // Prepare and execute the SQL statement to fetch the user associated with the RFID or PIN
    $sql = $conn->prepare("SELECT name, role FROM users WHERE $column = ?");
    $sql->bind_param("s", $input);
    $sql->execute();
    $result = $sql->get_result();

    // Initialize default values for unknown user
    $name = "Unknown";
    $role = "Unknown";
    $accessGranted = false;

    // Check if the user is registered
    if ($result->num_rows > 0) {
        // Fetch the user info if found
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $role = $row['role'];
        $accessGranted = true;
        
        // Insert log into access_logs table for registered users
        $logSql = $conn->prepare("INSERT INTO access_logs (rfid, name, role) VALUES (?, ?, ?)");
        $logSql->bind_param("sss", $input, $name, $role);
        if ($logSql->execute()) {
            echo json_encode(["status" => "success", "message" => "Access Granted", "name" => $name, "role" => $role]);
        } else {
            echo "Error: " . $logSql->error . "\n";
        }
        $logSql->close();

    } else {
        // User is unknown, store the attempt in the notification table
        $timesAttempt = 1;  // Set the number of attempts, this could be dynamic if needed
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $location = "Unknown Location";  // Modify or dynamically set the location as needed

        // Insert into notification table for unknown users
        $notifySql = $conn->prepare("INSERT INTO notification (Times_Attempt, RFID_Tag_Pin, Date, Time, Location) VALUES (?, ?, ?, ?, ?)");
        $notifySql->bind_param("issss", $timesAttempt, $input, $date, $time, $location);
        if ($notifySql->execute()) {
            echo json_encode(["status" => "error", "message" => "Access Denied - Unknown User", "rfid_pin" => $input]);
        } else {
            echo "Error: " . $notifySql->error . "\n";
        }
        $notifySql->close();
    }

    // Close the main SQL query and the database connection
    $sql->close();
    $conn->close();
}
?>
