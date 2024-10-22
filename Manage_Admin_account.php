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

if (!isset($_SESSION['username']) || !isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}
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

// SQL query to fetch all accounts//
$sql = "SELECT id, FullName, Role, Registered, Address, ContactNumber, Email FROM account";
$result = $conn->query($sql);
// SQL query to fetch all accounts//

$result = $conn->query($sql);
if ($result === false) {
	die("Error executing query: " . $conn->error);
}
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
	<link rel="stylesheet" href="css/dashboard.css">
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
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="Create_Admin_account.php">
					<i class='bx bxs-user'></i>
					<span class="text">Create Admin account</span>
				</a>
			</li>
			<li class="active">
				<a href="Manage_Admin_account.php">
					<i class='bx bxs-calendar-check'></i>
					<span class="text">Manage Admin account</span>
				</a>
			</li>
			<li>
				<a href="Admin_&_User_List.php">
					<i class='bx bxs-notification'></i>
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
			<i class='bx bx-menu'></i>
			<form action="#">
				<div class="form-input">


					<button class="search-btn"><i class='bx bx-search'></i></button>
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
					<img src="<?php echo htmlspecialchars($user['ProfilePic']); ?>" alt="Profile Picture"
						title="Edit Profile" />
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
					<h1>Manage Admin Account</h1>
				</div>
			</div>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Manage Admin Account</h3>
					</div>

					<div class="table-data">
						<div class="order">
							<div class="head">
								<h3>List</h3>
							</div>
							<table>
								<thead>
									<tr>
										<th>Name</th>
										<th>Role</th>
										<th>Date Registered</th>
										<th>Action</th>

									</tr>
								</thead>
								<tbody>

									<?php
									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo "<tr>";
											echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
											echo "<td>" . htmlspecialchars($row['Role']) . "</td>";
											echo "<td>" . htmlspecialchars($row['Registered']) . "</td>";
											echo "<td><button class='edit-button' data-id='" . htmlspecialchars($row['id']) . "'>Edit</button></td>";
											echo "</tr>";
										}
									} else {
										echo "<tr><td colspan='5'>No records found</td></tr>";
									}
									?>
								</tbody>
							</table>
						</div>

					</div>

				</div>
			</div>

		</main>

		<!-- Modal -->
		<div id="myModal" class="modal">
			<div class="modal-content">
				<span class="close">&times;</span>
				<form id="modalForm">
					<span>
						<p class="txt">Form</p>
					</span>
					<input type="hidden" id="accountId" name="id">
					<label for="name">Name:</label>
					<input type="text" id="name" name="name" placeholder="Enter Name" required>
					<label for="birthday">Birthday</label>
					<input class="bd" type="date" name="birthday" id="birthday">
					<label for="address">Address:</label>
					<input type="text" id="address" name="address" placeholder="Enter Address" required>
					<label for="contact">Contact Number:</label>
					<input type="tel" id="contact" name="contact" placeholder="Enter Contact Number" required>
					<label for="email">Email Address:</label>
					<input type="email" id="email" name="email" placeholder="Enter Email" required>
					<label for="role">Role:</label>
					<input type="text" id="role" name="role" placeholder="Enter Role" required>
					<div class="modal-buttons">
						<button type="button" id="" class="delete-btn">Delete</button>
						<button type="submit" class="submit-btn">Submit</button>
					</div>
				</form>
			</div>
		</div>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->



	<style>
		:root {
			--poppins: 'Poppins', sans-serif;
			--lato: 'Lato', sans-serif;

			--light: #F9F9F9;
			--blue: #3C91E6;
			--light-blue: #CFE8FF;
			--grey: #eee;
			--dark-grey: #AAAAAA;
			--dark: #342E37;
			--red: #DB504A;
			--yellow: #FFCE26;
			--light-yellow: #FFF2C6;
			--orange: #FD7238;
			--light-orange: #FFE0D3;
		}

		.edit-button {
			background: transparent;
			background-color: var(--light);
			color: var(--dark);
			border: none;
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
	<script src="modalform.js"></script>
	<script src="script.js"></script>
	<script src="calendar.js"></script>
	<script src="closebtn.js"></script>
</body>

</html>