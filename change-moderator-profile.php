<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Check session variables
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    echo "Session variables are not set. Redirecting to login page.";
    header("Location: login.html");
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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Change Profile</title>
    <style>

.pushable {
  position: relative;
  background: transparent;
  margin-top:10%;
  margin-left: 25%;
  width: 50%;
  padding: 0px;
  border: none;
  cursor: pointer;
  outline-offset: 4px;
  outline-color: deeppink;
  transition: filter 250ms;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}

.shadow {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: hsl(226, 25%, 69%);
  border-radius: 8px;
  filter: blur(2px);
  will-change: transform;
  transform: translateY(2px);
  transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);
}

.edge {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  border-radius: 8px;
  background: linear-gradient(
    to right,
    hsl(248, 39%, 39%) 0%,
    hsl(248, 39%, 49%) 8%,
    hsl(248, 39%, 39%) 92%,
    hsl(248, 39%, 29%) 100%
  );
}

.front {
  display: block;
  position: relative;
  border-radius: 8px;
  background: hsl(248, 53%, 58%);
  padding: 16px 32px;
  color: white;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
    Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-size: 1rem;
  transform: translateY(-4px);
  transition: transform 600ms cubic-bezier(0.3, 0.7, 0.4, 1);
}

.pushable:hover {
  filter: brightness(110%);
}

.pushable:hover .front {
  transform: translateY(-6px);
  transition: transform 250ms cubic-bezier(0.3, 0.7, 0.4, 1.5);
}

.pushable:active .front {
  transform: translateY(-2px);
  transition: transform 34ms;
}

.pushable:hover .shadow {
  transform: translateY(4px);
  transition: transform 250ms cubic-bezier(0.3, 0.7, 0.4, 1.5);
}

.pushable:active .shadow {
  transform: translateY(1px);
  transition: transform 34ms;
}

.pushable:focus:not(:focus-visible) {
  outline: none;
}



.profilee {
    margin: 20px auto;
    width: 200px; /* Fixed width */
    height: 200px; /* Fixed height */
    position: relative;
    border-radius: 50%; /* Circular shape */
    overflow: hidden; /* Hide overflow */
    display: flex;
    align-items: center;
    justify-content: center;
}

.profilee:hover .overlay {
    background-color: rgba(0, 0, 0, 0.5);
}

.profilee:hover .overlay p {
    display: block;
}

.profilee img {
    width: 100%; /* Ensure the image covers the entire container */
    height: 100%; /* Ensure the image covers the entire container */

}

.profilee .overlay {
    position: absolute;
    width: 200%;
    height: 200%;
    bottom: 0;
    border-radius: 50%; /* Maintain circular shape */
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0); /* Initially transparent */
    transition: background-color 0.3s; /* Smooth transition for hover effect */
}

.profilee .overlay input {
    position: absolute;
    width: 200%;
    height: 200%;
    opacity: 0;
    cursor: pointer;
}

.profilee .overlay p {
    position: absolute;
    bottom: 10px;
    font-weight: bold;
    text-align: center;
    color: #fff;
    width: 100%;
    display: none; /* Hidden by default */
}

input#address, #name, #contact, #email, #username, #birthday {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
 

}

button{

    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
    margin-top: 10px;
}

    </style>
</head>
<body>
<section id="sidebar">
<div class="logo1">
		<a href="#" class="brand">
			<img id="logoImage" class="logo" src="pictures/horizontallogo.png" alt="logo">
		</a>
        </div>
        <ul class="side-menu top">
            <li>
                <a href="admin/index.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="admin/user.php">
                    <i class='bx bxs-user'></i>
                    <span class="text">User Info</span>
                </a>
            </li>

            <li>
                <a href="admin/userActivities.php">
                    <i class='bx bxs-notification'></i>
                    <span class="text">User Activities</span>
                </a>
            </li>
            <li>
                <a href="admin/room-status.php">
				<i class='bx bxs-door-open'></i>
                    <span class="text">Room Status</span>
                </a>
            </li>
            <li>
                <a href="admin/notification.php">
                    <i class='bx bxs-notification'></i>
                    <span class="text">Alert/Notifications</span>
                </a>
            </li>
            <li>
                <a href="admin/registration.php">
                    <i class='bx bxs-user-plus'></i>
                    <span class="text">Registration</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
        <li>
        <a href="php/logout.php" class="logout">
            <i class='bx bxs-log-out-circle'></i>
            <span class="text">Logout</span>
        </a>
    </li>
</ul>

    </section>
    <section id="content">
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
            <img  src="<?php echo htmlspecialchars($user['ProfilePic']) ?>" alt="Profile Picture" />
                <div id="profile-dropdown" class="profile-dropdown">
                    <a href="#">Change Profile</a>
                </div>
            </div>
        </nav>
        <main>
            <h1 class="title">Profile Information</h1>
            <div class="table-container">
                <div class="table-data">
                    <div class="register-form-container">
                        <h3>Profile</h3>
                        

                        <form id="profileForm" action="update-moderator-profile.php" method="POST" enctype="multipart/form-data">
                        <div class="container">
                            <div class="profilee">
                                <img id="blah" src="<?php echo htmlspecialchars($user['ProfilePic']) ? htmlspecialchars($user['ProfilePic']) : ''; ?>" alt="Profile Picture" />
                                <div class="overlay">
                                <input type="file" id="Picture" name="profilePic" accept="image/*">
                                    <p>Change Picture</p>
                                </div>
                            </div>
                        </div>
                            <div>
                                <label for="username">Name:</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['FullName']); ?>">
                            </div>
                            <div>
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address']); ?>">
                            </div>
                            <div>
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user['Birthday']); ?>">
                            </div>
                            <div>
                                <label for="contact">Contact Number:</label>
                                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['ContactNumber']); ?>">
                            </div>
                            <div>
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>">
                            </div>
                            <div>
                                <button class="pushable">
                                    <span class="shadow"></span>
                                    <span class="edge"></span>
                                    <span class="front">
                                        Save Changes
                                    </span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>
    </section>
    <script>
        document.getElementById('Picture').addEventListener('change', function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('blah');
        output.src = reader.result;
    };
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
});


 </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm'); // Replace with your form ID

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        
        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="script.js"></script>
<script src="calendar.js"></script>
<script src="registrationmodal.js"></script>
</body>
</html>
