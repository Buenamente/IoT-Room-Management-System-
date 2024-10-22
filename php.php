<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "senti-shield";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get the user ID from the session
$userID = $_SESSION['id'];
$username = $_SESSION['username'];

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

// Query to count the number of users for each month in the current year (for Line Chart)
$sql = "SELECT MONTH(access_time) as month, COUNT(*) as count 
        FROM access_logs 
        WHERE YEAR(access_time) = YEAR(CURDATE()) 
        GROUP BY MONTH(access_time)";

$result = $conn->query($sql);
$monthly_data = array_fill(1, 12, 0); // Initialize an array with 12 zeros (for 12 months)

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $monthly_data[intval($row['month'])] = intval($row['count']);
    }
}

$monthly_data_json = json_encode(array_values($monthly_data));

// Query to count the number of activities per location (Room) for Pie Chart
$sql_pie = "SELECT Location, COUNT(*) as count 
            FROM activities 
            GROUP BY Location";

$result_pie = $conn->query($sql_pie);
$location_data = [];
$location_labels = [];

if ($result_pie->num_rows > 0) {
    while($row = $result_pie->fetch_assoc()) {
        $location_labels[] = $row['Location']; // Store room names (e.g., Room 1, Room 2)
        $location_data[] = intval($row['count']); // Store the count of activities for each room
    }
}

$conn->close();

$location_data_json = json_encode($location_data);
$location_labels_json = json_encode($location_labels);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            <li class="active">
                <a href="dashboard.php">
                    <i class='bx bxs-dashboard' ></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="Create_Admin_account.php">
                    <i class='bx bxs-user'></i>
                    <span class="text">Create Admin account</span>
                </a>
            </li>
            <li>
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
            <label for="switch-mode" class="switch-mode" title="switchMode Dark/Light"></label>
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
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                </div>
            </div>

            <ul class="box-info">
                <li>
            <i class='bx bxs-user'></i>
            <span class="text">
                <h3><?php echo $admin_count; ?></h3>
                    <p>Admin Count</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3><?php echo $user_count; ?></h3>
                <p>Users Count</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-user'></i>
            <span class="text">
                <h3><?php echo $moderator_count; ?></h3>
                <p>Moderator Count</p>
            </span>
        </li>
            </ul>

            <div class="chart-container">
                <div class="chart-box">
                    <canvas id="chartContainerLine"></canvas>
                </div>
                <div class="chart-box">
                    <canvas id="chartContainerPie"></canvas>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the monthly data from PHP (for Line Chart)
            const monthlyData = <?php echo $monthly_data_json; ?>;

            // Line chart data
            const lineData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Monthly Users',
                    data: monthlyData,
                    fill: false,
                    borderColor: '#FF6384',
                    tension: 0.1
                }]
            };

            // Initialize the line chart
            const ctxLine = document.getElementById('chartContainerLine').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: lineData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Get the location data from PHP (for Pie Chart)
            const locationData = <?php echo $location_data_json; ?>;
            const locationLabels = <?php echo $location_labels_json; ?>;

            // Pie chart data
            const pieData = {
                labels: locationLabels,
                datasets: [{
                    data: locationData,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            };

            // Initialize the pie chart
            const ctxPie = document.getElementById('chartContainerPie').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: pieData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
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

    <style>
        .chart-container {
            display: flex;
            gap: 20px;
        }
        .chart-box {
            flex: 1;
            background: var(--light);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .chart-box canvas {
            width: 100%;
            max-width: 400px;
            height: auto;
        }
        @media (max-width: 768px) {
            .chart-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
<script src="scriptadmin.js"></script>
<script src="calendar.js"></script>
</body>
</html>
