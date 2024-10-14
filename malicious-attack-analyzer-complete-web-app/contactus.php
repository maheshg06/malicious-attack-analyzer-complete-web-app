<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Change this if needed for your MySQL setup
$database = "malware_analysis_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $uid = $_SESSION['user_id'];  // Assuming user is logged in and uid is stored in session

    // Insert data into contact_us table
    $sql = "INSERT INTO contact_us (uid, name, email, message) VALUES ('$uid', '$name', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {
        $success = "Thank you! Your message has been submitted.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Malware Analysis System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-link:hover {
            color: #ddd !important;
        }
        .content-section {
            padding: 40px 0;
            background-color: #fff;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Malware Analysis System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="user_frontpage.php?user_id=<?php echo $_GET['user_id']; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutus.php?user_id=<?php echo $_GET['user_id']; ?>">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.php?user_id=<?php echo $_GET['user_id']; ?>">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user_faqs.php?user_id=<?php echo $_GET['user_id']; ?>">FAQ</a>
                </li>
            </ul>
            <div class="d-flex">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="btn btn-outline-light me-2">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                    <a href="register.php" class="btn btn-success">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Content Section -->
<div class="content-section">
    <div class="container">
        <h2>Contact Us</h2>
        <p>If you have any questions, feel free to reach out to us using the form below or through our contact details.</p>

        <!-- Success/Failure messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="contactus.php">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
