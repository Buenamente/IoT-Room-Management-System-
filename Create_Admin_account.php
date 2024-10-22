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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>User Information</title>
</head>
<body>
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
            <li class="active">
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
            <li>
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
    <section id="content">
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

        <main>
            <h1 class="title">Create Admin account</h1>
            <div class="table-container">
                <div class="table-data">
                    <div class="register-form-container">
                        <h3>Creation form</h3>
                        <form class="register-form" id="register-form" action="php/register-process.php" method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" required placeholder="Enter Name">
                            </div>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" required placeholder="Enter Username">
                            </div>
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="date" id="birthday" name="birthday" required placeholder="Enter Birthday">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" required placeholder="Enter Address">
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact Number</label>
                                <input type="contact" id="contact" name="contact" required placeholder="Enter Contact Number">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required placeholder="Enter Email">
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="moderator">moderator</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required placeholder="Enter password">
                            </div>
                            <div class="form-group">
                                <button type="submit">Register</button>
                            </div>
                        </form>
                        <div id="message"></div> <!-- Element to display messages -->
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script>
        document.getElementById('register-form').addEventListener('submit', function(event) {
            event.preventDefault(); 

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            fetch('php/register-process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(jsonData).toString()
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the requestttt.');
            });
        });

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
    <script src="script.js"></script>
    <script src="calendar.js"></script>
    <script src="registrationmodal.js"></script>
</body>
</html>
