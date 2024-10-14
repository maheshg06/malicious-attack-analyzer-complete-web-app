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

// Fetch the count and details of new users (registered in the last 24 hours)
$new_users_query = "SELECT name, reg_date FROM users WHERE reg_date >= NOW() - INTERVAL 1 DAY";
$new_users_result = $conn->query($new_users_query);
$new_users_count = $new_users_result->num_rows;

$new_users = [];
if ($new_users_count > 0) {
    while ($row = $new_users_result->fetch_assoc()) {
        $new_users[] = $row['name'] . " (" . $row['reg_date'] . ")";
    }
}

// Fetch the total count of users
$total_users_query = "SELECT COUNT(*) as total_users FROM users";
$total_users_result = $conn->query($total_users_query);
$total_users = 0;
if ($total_users_result->num_rows > 0) {
    $row = $total_users_result->fetch_assoc();
    $total_users = $row['total_users'];
}

// Fetch the total count of FAQs
$total_faqs_query = "SELECT COUNT(*) as total_faqs FROM faqs";
$total_faqs_result = $conn->query($total_faqs_query);
$total_faqs = 0;
if ($total_faqs_result->num_rows > 0) {
    $row = $total_faqs_result->fetch_assoc();
    $total_faqs = $row['total_faqs'];
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }

        /* Sidebar */
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            position: fixed;
            width: 220px;
            padding-top: 20px;
            color: #fff;
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

        .sidebar.collapsed a {
            padding: 10px;
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
        }

        .collapsed + .content {
            margin-left: 80px;
        }

        /* Header */
        .header {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        /* Footer */
        .footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: calc(100% - 240px);
            bottom: 0;
            margin-left: 240px;
        }

        .collapsed + .footer {
            width: calc(100% - 80px);
            margin-left: 80px;
        }

        /* Center the cards */
        .center-cards {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
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
            <span class="badge"><?php echo $new_users_count; ?></span>
            <i class="fas fa-bell"></i> New User Registrations
            <div class="notification-details">
                <strong>New Users:</strong><br>
                <?php
                if ($new_users_count > 0) {
                    foreach ($new_users as $user) {
                        echo $user . "<br>";
                    }
                } else {
                    echo "No new users.";
                }
                ?>
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
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row center-cards">
                <div class="card-container">
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total FAQs</h5>
                                <p class="card-text"><?php echo $total_faqs; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Predictions</h5>
                                <p class="card-text">5</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">System Health</h5>
                                <p class="card-text">Good</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
</body>
</html>
