<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username']) || !isset($_SESSION['id']) || $_SESSION['role'] !== 'moderator') {
    header("Location: ../login.html");
    exit();
}


// Fetch user data using prepared statements
$username = $_SESSION['username'];
$userID = $_SESSION['id']; // Ensure this is set correctly during login

$sql = "SELECT * FROM account WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();  // Fetch the user data into the $user array
} else {
    echo "User not found.";
    exit();
}

// Handle search parameter

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the query with the search parameter
// Assuming the search is applied to name, rfid, or role
$sql = "SELECT id, rfid, name, role, access_time FROM access_logs WHERE name LIKE ? OR rfid LIKE ? OR role LIKE ? OR access_time LIKE ? ORDER BY access_time DESC";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";  // Adding % to allow partial matches
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- CSS -->
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="dashboard.css">
    <title>History Logs</title>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
    <div class="logo1">
		<a href="#" class="brand">
			<img id="logoImage" class="logo" src="pictures/horizontallogo.png" alt="logo">
		</a>
        </div>
        <ul class="side-menu top">
            <li>
                <a href="index.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="user.php">
                    <i class='bx bxs-user'></i>
                    <span class="text">User Info</span>
                </a>
            </li>

            <li>
                <a href="userActivities.php">
                    <i class='bx bxs-notification'></i>
                    <span class="text">User Activities</span>
                </a>
            </li>
            <li>
                <a href="room-status.php">
				<i class='bx bxs-door-open'></i>
                    <span class="text">Room Status</span>
                </a>
            </li>
            <li>
                <a href="notification.php">
                    <i class='bx bxs-notification'></i>
                    <span class="text">Alert/Notifications</span>
                </a>
            </li>
            <li>
                <a href="registration.php">
                    <i class='bx bxs-user-plus'></i>
                    <span class="text">Registration</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="../php/logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- CONTENT -->
    <section id="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <button class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode" title="switchMode Dark/Light"></label>
            <div class="calendar">
                <div class="cd">
                    <i class='bx bx-calendar'></i>
                </div>
                <div id="calendar"></div>
            </div>
            <div id="profile" class="profile">
            <a href="../change-moderator-profile.php">
    <img src="<?php echo htmlspecialchars('../' . $user['ProfilePic']); ?>" alt="Profile Picture" title="Edit Profile" />
    </a>  
    </div>
        </nav>
        <!-- Navbar -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>History Logs</h1>
                    <p id="total-records">Total Records: Loading...</p>
                </div>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>History Logs</h3>
                        <form action="" method="GET" style="display: inline; margin-left: 10px;">
                            <input type="search" id="search-input" name="search" placeholder="Search...">
                        <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                            <i class='bx bx-refresh' title="Refresh"></i>
                        </button>
                        </form>
                        <form action="export_csv.php" method="POST" style="display: inline;">
                            <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                <i class='bx bxs-cloud-download' title="Download All Content"></i>
                            </button>
                        </form>
                        <form action="delete.php" method="POST" style="display: inline;">
                            <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                <i class='bx bxs-trash' title="Delete All Content" id="delete-icon"></i>
                            </button>
                        </form>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>RFID Tag/Pin Key</th>
                                <th>Role</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                            <tbody id="history-logs-table">
                            <?php
                            if ($result->num_rows > 0) {
                            $counter = 1; // Initialize counter
                            while ($row = $result->fetch_assoc()) {
                            $date_time = explode(' ', $row['access_time']);
                            $date = $date_time[0];
                            $time = $date_time[1];

                            echo "<tr>
                                <td>{$counter}</td> <!-- Use the counter for the ID -->
                                <td>{$row['rfid']}</td>
                                <td>{$row['role']}</td>
                                <td>{$row['name']}</td>
                                <td>{$date}</td>
                                <td>{$time}</td>
                                <td>
                                <button class='btn btn-primary' data-name='" . $row['name'] . "' title='View User History' onclick='editRecord(\"" . $row['name'] . "\")'>View</button>
                                <button class='btn btn-danger' title='Delete Row' onclick='deleteRecord(" . $row['id'] . ")'>Delete</button>
                                </td>
                                </tr>";
                                $counter++; // Increment counter
                                }
                                } else {
                            echo "<tr><td colspan='7'>No logs found</td></tr>";
                            }
                            $stmt->close();
                            $conn->close();
                            ?>
                            </tbody>



                    </table>
                </div>
            </div>
        </main>
    </section>

<div id="userModal" class="modal">
    <div class="historymodal">
        <span class="close">&times;</span>
        <h2>User Logs </h2>
        <table id="userTable">
            <thead>
                <tr class="thead">
                    <th>Name</th>
                    <th>RFID Tag/Pin</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <!-- Rows will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Count total records
            const totalRecords = document.querySelectorAll('#history-logs-table tr').length;
            document.getElementById('total-records').textContent = `Total Records: ${totalRecords}`;
        });

        // Toggle dark mode
        document.getElementById('switch-mode').addEventListener('change', function() {
            document.body.classList.toggle('dark-mode');
        });

        // Delete All Records Confirmation
        document.getElementById('delete-icon').addEventListener('click', function(event) {
            event.preventDefault(); 

            if (confirm("Are you sure you want to delete all records?")) {
                fetch('delete.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload(); // Reload page to update the table
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting records.');
                });
            }
        });



        function editRecord(name) {
    fetch(`historylogs-get-info.php?name=${encodeURIComponent(name)}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('userTableBody');
            tableBody.innerHTML = '';  // Clear previous data

            if (data.length > 0) {
                data.forEach(record => {
                    const row = document.createElement('tr');

                    const nameCell = document.createElement('td');
                    nameCell.textContent = record.name;
                    row.appendChild(nameCell);

                    const rfidCell = document.createElement('td');
                    rfidCell.textContent = record.rfid;
                    row.appendChild(rfidCell);

                    const roleCell = document.createElement('td');
                    roleCell.textContent = record.role;
                    row.appendChild(roleCell);


                    const dateCell = document.createElement('td');
                    dateCell.textContent = record.access_date;
                    row.appendChild(dateCell);

                    const timeCell = document.createElement('td');
                    timeCell.textContent = record.access_time;
                    row.appendChild(timeCell);

                    tableBody.appendChild(row);
                });

                const modal = document.getElementById('userModal');
                modal.style.display = 'block';  // Show modal
            } else {
                alert('No records found for this user.');
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('An error occurred while fetching user data.');
        });
}







document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.bx-menu');
    const logoImage = document.getElementById('logoImage');
    const logoContainer = document.querySelector('.logo1');

    menuIcon.addEventListener('click', function () {
        if (logoImage.src.includes('horizontallogo.png')) {
            logoImage.src = '../pictures/output-onlinepngtools (1).png'; // Change to the new image
            logoContainer.classList.add('active'); // Apply the new styles to the container
        } else {
            logoImage.src = 'pictures/horizontallogo.png'; // Revert to the original image
            logoContainer.classList.remove('active'); // Remove the styles from the container
        }
    });
});



// Get the modal element
const modal = document.getElementById('userModal');

// Get the <span> element that closes the modal
const span = document.querySelector('.close');

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = 'none';
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}


function deleteRecord(id) {
    if (confirm("Are you sure you want to delete this record?")) {
        // Send a DELETE request using fetch with the id
        fetch('delete-record.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                // Optionally reload or refresh the table to reflect the deleted data
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the record.');
        });
    }
}



</script>
<script src="scriptadmin.js"></script>
<script src="calendar.js"></script>
<style>
#sidebar .side-menu li a.logout {

    color: var(--red);
}
i.bx.bx-refresh {
            color: blue;
        }

        .head form {
            display: inline-block;
            margin-right: 10px;
        }

        #search-input {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 150px;
        }

        .head button[type="submit"] {
            margin-left: 5px;
        }

.logo1.active {
    /* Example of changes: */
    background-color: transparent; /* Change background color */
    border-radius: 10px; /* Round the corners */
    /* Add any other styles you want to apply */
}
img#logoImage{
    position: relative;
    width: 100%;
    object-fit: cover;
}


.historymodal {
    background-color: var(--light);
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    max-width: 800px;
    color: var(--dark);
    position: relative;
}
.modal table {
    width: 100%;
    border-collapse: collapse;
}

.modal table th{
    border-bottom: 1px solid #ddd; 
}

.modal table td {
    padding: 8px;
    text-align: left;

}


#userTable {
    width: 100%;
    border-collapse: collapse; /* Ensures no space between the table and its borders */
}

#userTable th, #userTable td {
    padding: 10px;
    text-align: left; /* Align text to the left */
     /* Optional: Adds borders to cells */
}
</style>
</body>
</html>
