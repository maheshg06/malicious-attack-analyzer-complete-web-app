<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Change this if you have set a password for MySQL
$database = "malware_analysis_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// For simplicity, we assume that the system health is "Good"
$system_health = "Good";

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health - Malware Analysis System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
        }

        .sidebar {
            background-color: #343a40;
            height: 100vh;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            color: #fff;
            padding-top: 20px;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            text-align: center;
            transition: padding 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar i {
            font-size: 18px;
            margin-right: 10px;
        }

        .sidebar.collapsed i {
            margin-right: 0;
        }

        .sidebar h4 {
            text-align: center;
            font-size: 18px;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            margin-top: 70px; /* To account for the fixed header */
            min-height: calc(100vh - 100px); /* To account for the height of the header and footer */
        }

        .collapsed + .content {
            margin-left: 80px;
        }

        .header {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 60px;
            position: fixed;
        }

        .footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: calc(100% - 240px);
            bottom: 0;
            left: 240px;
            z-index: 1000;
            height: 40px;
        }

        .collapsed + .header,
        .collapsed + .footer {
            width: calc(100% - 80px); /* Adjust width when sidebar is collapsed */
            left: 80px; /* Align to collapsed sidebar */
        }

        .header .notification {
            position: relative;
            cursor: pointer;
        }

        .header .notification .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
        }

        .header .notification-details {
            display: none;
            position: absolute;
            top: 40px;
            right: 0;
            background-color: #fff;
            color: #000;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }

        .header .notification:hover .notification-details {
            display: block;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #fff;
        }

        .health-indicator {
            font-size: 24px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <button class="btn btn-secondary" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h4>Admin Dashboard</h4>
        <div class="notification">
            <span class="badge">1</span>
            <i class="fas fa-bell"></i> New User Registrations
            <div class="notification-details">
                <strong>New Users:</strong><br>
                No new users.
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h4 class="text-center">Admin</h4>
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="text">Dashboard</span></a>
        <a href="user_manage.php"><i class="fas fa-users"></i><span class="text">User Management</span></a>
        <a href="faqs.php"><i class="fas fa-question-circle"></i><span class="text">FAQ</span></a>
        <a href="predict_trozen.php"><i class="fas fa-bug"></i><span class="text">Predict Trojan</span></a>
        <a href="system_health.php"><i class="fas fa-heartbeat"></i><span class="text">System Health</span></a>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <h5 class="card-title">System Health Status</h5>
                <p class="card-text">Current System Health: <span class="health-indicator"><?php echo $system_health; ?></span></p>
                <p>Everything seems to be working well.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
    </div>

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content');
        const footer = document.querySelector('.footer');
        
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            footer.classList.toggle('collapsed');
        });
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
