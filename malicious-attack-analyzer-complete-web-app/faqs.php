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

// Fetch FAQs from the database
$sql = "SELECT * FROM faqs ORDER BY asked_date DESC";
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
    <title>Manage FAQs</title>
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
            width: 220px;
            position: fixed;
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

        .btn-reply {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-reply:hover {
            background-color: #0056b3;
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
<div class="sidebar" id="sidebar">
    <h4 class="text-center">Admin</h4>
    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="text">Dashboard</span></a>
    <a href="user_manage.php"><i class="fas fa-users"></i><span class="text">User Management</span></a>
    <a href="faqs.php"><i class="fas fa-question-circle"></i><span class="text">FAQ</span></a>
    <a href="predict_trozen.php"><i class="fas fa-bug"></i><span class="text">Predict Trojan</span></a>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <h2 class="mb-4">Manage FAQs</h2>
        <table class="table table-bordered">
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Asked By</th>
                    <th>Email</th>
                    <th>Answer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['question']; ?></td>
                            <td><?php echo $row['asked_by']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['answer'] ? $row['answer'] : 'Pending'; ?></td>
                            <td>
                                <button class="btn-reply" onclick="openReplyModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['answer']); ?>')">Reply</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No FAQs available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">Update Answer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="replyForm" action="updatefaq.php" method="post">
                    <input type="hidden" id="faqId" name="faqId">
                    <div class="form-group">
                        <label for="answer">Answer:</label>
                        <textarea class="form-control" id="answer" name="answer" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function openReplyModal(id, answer) {
        $('#faqId').val(id);
        $('#answer').val(answer);
        $('#replyModal').modal('show');
    }

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
