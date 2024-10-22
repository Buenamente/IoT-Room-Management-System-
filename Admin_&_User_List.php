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

if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header("Location: login.html");
    exit();
}

// Get the user ID from the session
$userID = $_SESSION['id'];

$query = "SELECT ProfilePic FROM account WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // If no user is found, use a default profile picture
    $user = ['ProfilePic' => 'uploads/profile_pictures/default.jpg'];
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
	<link rel="stylesheet" href="css/modalform.css">
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/dashboardd.css">
	<title>AdminHub</title>
</head>
<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
	<div class="logo1">
		<a href="dashboard.php" class="brand">
			<img id="logoImage" class="logo" src="pictures/horizontallogo.png" alt="logo">
		</a>
        </div>
		<ul class="side-menu top">
			<li>
				<a href="dashboard.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="Create_Admin_account.php">
					<i class='bx bxs-user' ></i>
					<span class="text">Create Admin account</span>
				</a>
			</li>
			<li>
				<a href="Manage_Admin_account.php">
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">Manage Admin account</span>
				</a>
			</li>
			<li class="active">
				<a href="Admin_&_User_List.php">
					<i class='bx bxs-notification' ></i>
					<span class="text">User List</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
            <li>
                <a href="session.php" class="logout">
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
					<button class="search-btn"><i class='bx bx-search' ></i></button>
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
    <a href="change-admin-profile.php">
        <img src="<?php echo htmlspecialchars($user['ProfilePic']); ?>" alt="Profile Picture" title="Edit Profile" />
    </a>
    <div id="profile-dropdown" class="profile-dropdown">

    </div>
</div>
        </nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>User List</h1>
				</div>
			</div>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>User List</h3>
					</div>
                    <div class="table-data">
				<div class="order">
					
					<table>
						<thead>
							<tr>
							    <th>Id</th>
								<th>Name</th>
								<th>Role</th>
								<th>Date Registered</th>
                                <th>Details</th>
							</tr>
						</thead>
						<tbody>
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
                            
                                // SQL query to fetch user data
                                $sql = "SELECT id, rfid, name, role, DateRegistered FROM users";
                                $result = $conn->query($sql);
                            
                                if ($result->num_rows > 0) {
                                    // Output data for each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['DateRegistered']) . "</td>";
                                        echo "<td><button class='edit-btn' data-id='" . htmlspecialchars($row['id']) . "'>View</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No users found.</td></tr>";
                                }
                            
                                $conn->close();
                                ?>
						</tbody>
					</table>
				</div>
			 </div>
				</div>
			</div>
				</div>
			</div>
		</main>
		
		<div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p class="txt">View User Information</p>
            <form id="editForm">
    <input type="hidden" id="accountId" name="accountId">
    <label for="editName">Name:</label>
    <input type="text" id="editName" name="editName" required>
    <label for="editAddress">Address:</label>
    <input type="text" id="editAddress" name="editAddress" required>
    <label for="editBirthday">Birthday:</label>
    <input type="date" id="editBirthday" name="editBirthday" required>
    <label for="editEmail">Email:</label>
    <input type="email" id="editEmail" name="editEmail" required>
    <label for="editContactNumber">Contact Number:</label>
    <input type="text" id="editContactNumber" name="editContactNumber" required>
    <label for="editRole">Role:</label>
    <input type="text" id="editRole" name="editRole" required>
    <label for="editRFID">RFID:</label>
    <input type="text" id="editRFID" name="editRFID" required>
    <label for="editPin">Pin:</label>
    <input type="text" id="editPin" name="editPin" required>
</form>
 </div>
    </div>
       
		
	<!-- CONTENT -->
<style>
button.edit-btn   {
	background: transparent;
    background-color: var(--light);
	color: var(--dark);
	border: none;
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

</style>
<script>
	document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.bx-menu');
    const logoImage = document.getElementById('logoImage');
    const logoContainer = document.querySelector('.logo1');

    menuIcon.addEventListener('click', function () {
        if (logoImage.src.includes('horizontallogo.png')) {
            logoImage.src = 'pictures/output-onlinepngtools (1).png'; // Change to the new image
            logoContainer.classList.add('active'); // Apply the new styles to the container
        } else {
            logoImage.src = 'pictures/horizontallogo.png'; // Revert to the original image
            logoContainer.classList.remove('active'); // Remove the styles from the container
        }
    });
});
</script>
<script src="testing.js"></script>
<script src="script.js"></script>
<script src="calendar.js"></script>
<script src="closebtn.js"></script>
</body>
</html>
