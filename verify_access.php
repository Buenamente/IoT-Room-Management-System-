<?php
// Set the correct time zone for PHP
date_default_timezone_set('Asia/Manila'); // Adjust this to your time zone

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the room location from POST data, default to 'Unknown Location' if not provided
    $roomLocation = isset($_POST['location']) ? $_POST['location'] : 'Unknown Location';

    // Establish a connection to the database
    $conn = new mysqli('localhost', 'root', '', 'senti-shield');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set the MySQL server time zone to match PHP time zone
    $conn->query("SET time_zone = '+08:00'"); // Adjust this to your time zone offset

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

    // Clean the input to prevent SQL injection
    $input = mysqli_real_escape_string($conn, trim($input));

    // Get the current time and status, defaulting to 'Ongoing' if not provided
    $currentTime = date('Y-m-d H:i:s');
    $currentStatus = isset($_POST['status']) ? $_POST['status'] : 'Ongoing';

    // Prepare and execute the SQL statement to fetch the user associated with the RFID or PIN
    $sql = $conn->prepare("SELECT id, name, role FROM users WHERE $column = ?");
    if (!$sql) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit;
    }
    $sql->bind_param("s", $input);
    $sql->execute();
    $result = $sql->get_result();

    // Check if a user is found with the provided RFID/PIN
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        $name = $row['name'];
        $role = $row['role'];

        // Check if the user is already checked in (i.e., no time-out recorded yet)
        $activitySql = $conn->prepare("SELECT * FROM activities WHERE RFID_Tag = ? AND Time_Out IS NULL");
        $activitySql->bind_param("s", $input);
        $activitySql->execute();
        $activityResult = $activitySql->get_result();

        if ($activityResult->num_rows > 0) {
            // User is checking out
            $doneStatus = "Done"; // Set the status to "Done"
            $updateSql = $conn->prepare("UPDATE activities SET Time_Out = ?, Status = ? WHERE RFID_Tag = ? AND Time_Out IS NULL");
            $updateSql->bind_param("sss", $currentTime, $doneStatus, $input);
            $updateSql->execute();
            $updateSql->close();

            // Clear the room status
            $updateRoomSql = $conn->prepare("UPDATE rooms SET status = 'Available', occupied_by = NULL, role = NULL WHERE location = ?");
            $updateRoomSql->bind_param("s", $roomLocation);
            $updateRoomSql->execute();
            $updateRoomSql->close();

            echo json_encode(["status" => "success", "message" => "Checked Out", "name" => $name, "role" => $role]);
        } else {
            // User is checking in
            $logSql = $conn->prepare("INSERT INTO activities (Name, RFID_Tag, Time_In, Status, Location) VALUES (?, ?, ?, ?, ?)");
            $logSql->bind_param("sssss", $name, $input, $currentTime, $currentStatus, $roomLocation);
            $logSql->execute();
            $logSql->close();

            // Update the room's status to occupied
            $updateRoomSql = $conn->prepare("UPDATE rooms SET status = 'Occupied', occupied_by = ?, role = ? WHERE location = ?");
            $updateRoomSql->bind_param("sss", $name, $role, $roomLocation);
            $updateRoomSql->execute();
            $updateRoomSql->close();

            echo json_encode(["status" => "success", "message" => "Checked In", "name" => $name, "role" => $role]);
        }

        // Insert log into the access_logs table
        $logAccessSql = $conn->prepare("INSERT INTO access_logs (rfid, name, role) VALUES (?, ?, ?)");
        $logAccessSql->bind_param("sss", $input, $name, $role);
        $logAccessSql->execute();
        $logAccessSql->close();

    } else {
        // User is unknown, store the attempt in the notification table
        $timesAttempt = 1;
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Insert into notification table for unknown users
        $notifySql = $conn->prepare("INSERT INTO notification (Times_Attempt, RFID_Tag_Pin, Date, Time, Location) VALUES (?, ?, ?, ?, ?)");
        $notifySql->bind_param("issss", $timesAttempt, $input, $date, $time, $roomLocation);
        if ($notifySql->execute()) {
            echo json_encode(["status" => "error", "message" => "Access Denied - Unknown User", "rfid_pin" => $input]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error inserting notification: " . $notifySql->error]);
        }
        $notifySql->close();
    }

    // Close the prepared statement and database connection
    $sql->close();
    $conn->close();
}
?>
