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

// Update the answer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faqId = $_POST['faqId'];
    $answer = $_POST['answer'];

    // Update the answer in the database
    $sql = "UPDATE faqs SET answer = '$answer', answered_date = NOW() WHERE id = $faqId";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Answer updated successfully.";
    } else {
        $_SESSION['message'] = "Error updating answer: " . $conn->error;
    }
}

// Redirect back to the FAQs management page
header("Location: faqs.php");
exit();
?>
