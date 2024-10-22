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
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<!-- My CSS -->
	<link rel="stylesheet" href="styleadmin.css">
	<link rel="stylesheet" href="dashboard.css">
	<title>AdminHub</title>
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
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="user.php">
					<i class='bx bxs-user' ></i>
					<span class="text">User Info</span>
				</a>
			</li>
			
			<li>
				<a href="userActivities.php">
					<i class='bx bxs-notification' ></i>
					<span class="text">User Activities</span>
				</a>
			</li>
			<li>
                <a href="room-status.php">
				<i class='bx bxs-door-open'></i>
                    <span class="text">Room Status</span>
                </a>
            </li>
			<li class="active">
				<a href="notification.php">
					<i class='bx bxs-notification' ></i>
					<span class="text">Alert/Notifications</span>
				</a>
			</li>
			<li>
				<a href="registration.php">
					<i class='bx bxs-user-plus' ></i>
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
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
            <i class='bx bx-menu' ></i>
			<form action="#">
				<div class="form-input">

					<button  class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
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
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Notification</h1>
					<p id="total-records">Total Records: Loading...</p>
				</div>
			</div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Notification History</h3>
						<form action="delete-notification.php" method="POST" style="display: inline;">
                            <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                <i class='bx bxs-trash' title="Delete All Content" id="delete-icon"></i>
                            </button>
                        </form>
						
                    </div>
                    
					<table>
						<thead>
							<tr>
                                <th>RFID Tag/PIN</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
								</tr>
					</thead>
					<tbody id="notification-table">
					<?php
						// Database connection
						$servername = "localhost";
						$username = "root";
						$password = "";
						$dbname = "senti-shield";

						$conn = new mysqli($servername, $username, $password, $dbname);

						// Check connection
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						}

						// SQL query to fetch user data and order by Date and Time in descending order
						$sql = "SELECT Times_Attempt, RFID_Tag_PIN, Date, Time, Location FROM notification ORDER BY Date DESC, Time DESC";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							// Output data for each row
							while($row = $result->fetch_assoc()) {
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['RFID_Tag_PIN']) . "</td>";
								echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
								echo "<td>" . htmlspecialchars($row['Time']) . "</td>";
								echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='4'>No users found.</td></tr>";
						}
						$conn->close();
					?>
				</tbody>

				</table>
                  <!-- dito kau mag add ng code  -->

                </div>
            </div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<script src="scriptadmin.js"></script>
	<script src="calendar.js"></script>
<script>
	function downloadFile(rowId) {
            console.log("Downloading file for row: " + rowId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Count total records
            const totalRecords = document.querySelectorAll('#notification-table tr').length;
            document.getElementById('total-records').textContent = `Total Records: ${totalRecords}`;
        });

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

document.getElementById('delete-icon').addEventListener('click', function(event) {
            event.preventDefault(); 

            if (confirm("Are you sure you want to delete all records?")) {
                fetch('delete-notification.php', {
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

</script>
<style>
	
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

</style>
</body>
</html>



