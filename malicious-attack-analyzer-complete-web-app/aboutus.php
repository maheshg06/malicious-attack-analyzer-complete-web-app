<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Malware Analysis System</title>
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
        <h2>About Us</h2>
        <p>Welcome to the Malware Analysis System. Our mission is to provide an easy-to-use platform for analyzing and predicting potential malware threats in digital files. We focus on using advanced machine learning algorithms to classify and detect various types of malicious software.</p>
        <p>Our team consists of cybersecurity experts, data scientists, and software engineers dedicated to making malware detection accessible to everyone. We believe in continuous improvement and constantly update our system with the latest threat intelligence.</p>
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
