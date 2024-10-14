<?php
session_start();

// Hide PHP notices and warnings
error_reporting(0);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

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

// Handle FAQ submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $conn->real_escape_string($_POST['question']);
    $asked_by = $conn->real_escape_string($_SESSION['user_name']);
    $email = $conn->real_escape_string($_SESSION['user_email']);
    $uid = $conn->real_escape_string($_SESSION['user_id']); // Get the user ID from session

    // Insert the FAQ into the database with user ID (uid)
    $sql = "INSERT INTO faqs (question, asked_by, email, uid, asked_date) VALUES ('$question', '$asked_by', '$email', '$uid', NOW())";
    if ($conn->query($sql) === TRUE) {
        $submission_success = "Your question has been submitted successfully.";
    } else {
        $submission_error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch FAQs from the database for the logged-in user (using user ID `uid`)
$user_id = $conn->real_escape_string($_SESSION['user_id']); // Ensure we are fetching based on the logged-in user's uid
$sql = "SELECT * FROM faqs WHERE uid = '$user_id' ORDER BY asked_date DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User FAQs - Malware Analysis System</title>
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
        .form-section {
            padding: 60px 0;
        }
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .faq-section {
            padding: 40px 0;
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
                <a href="logout.php" class="btn btn-outline-light me-2">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- FAQ Submission Section -->
<div class="form-section">
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Submit a Question</h2>
            <?php if (isset($submission_success)): ?>
                <div class="alert alert-success"><?php echo $submission_success; ?></div>
            <?php endif; ?>
            <?php if (isset($submission_error)): ?>
                <div class="alert alert-danger"><?php echo $submission_error; ?></div>
            <?php endif; ?>
            <form action="user_faqs.php?user_id=<?php session_start(); echo $_GET['user_id']; ?>" method="post">
                <div class="mb-3">
                    <label for="question" class="form-label">Your Question</label>
                    <textarea class="form-control" id="question" name="question" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Question</button>
            </form>
        </div>
    </div>
</div>

<!-- Display FAQs Section -->
<div class="faq-section">
    <div class="container">
        <h2 class="text-center">Your Questions</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Asked By</th>
                        <th>Answer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['question']; ?></td>
                                <td><?php echo $row['asked_by']; ?></td>
                                <td><?php echo $row['answer'] ? $row['answer'] : 'Pending'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">You have not submitted any questions.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
