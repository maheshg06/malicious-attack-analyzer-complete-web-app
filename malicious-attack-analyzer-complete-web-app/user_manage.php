<?php
session_start();

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

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the user from the database
    $delete_sql = "DELETE FROM users WHERE uid = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting user: " . $conn->error;
    }

    // Redirect back to the user management page
    header("Location: user_manage.php");
    exit();
}

// Fetch all users from the database
$sql = "SELECT * FROM users ORDER BY reg_date DESC";
$result = $conn->query($sql);

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

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .content {
            margin-left: 240px;
            padding: 20px;
            transition: margin-left 0.3s ease;
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

        /* Table Styling */
        table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            text-align: center;
            padding: 12px;
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .table-header {
            background-color: #343a40;
            color: #fff;
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
<div class="sidebar">
<h4 class="text-center">Admin</h4>
<a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="user_manage.php"><i class="fas fa-users"></i> User Management</a>
    <a href="faqs.php"><i class="fas fa-question-circle"></i> FAQ</a>
    <a href="predict_trozen.php"><i class="fas fa-bug"></i> Predict Trojan</a>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <h2 class="mb-4">User Management</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Reg Date</th>
                    <th>Update Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['uid']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['reg_date']; ?></td>
                            <td><?php echo $row['update_date']; ?></td>
                            <td>
                                <a href="user_manage.php?delete_id=<?php echo $row['uid']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');
    
    toggleSidebar.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });
</script>
</body>
</html>
