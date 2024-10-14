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
    <title>Predict Trojan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
    background-color: #f4f4f4;
    margin: 0;
    font-family: Arial, sans-serif;
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
    top: 0;
    left: 0;
    overflow-y: auto;
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

/* Content */
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
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
    left: 240px;
    z-index: 1000;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
}

.collapsed + .footer {
    width: calc(100% - 80px);
    left: 80px;
}

/* Responsive layout */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 60px;
    }
    
    .content {
        margin-left: 80px;
    }

    .footer {
        width: calc(100% - 80px);
        left: 80px;
    }
}

/* Card */
.card {
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    background-color: #fff;
    margin-top: 80px; /* to give space for fixed header */
}

/* Button */
.btn-upload {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
}

.btn-upload:hover {
    background-color: #218838;
}

/* Chart container */
.chart-container {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    flex-wrap: wrap;
}

.chart-wrapper {
    width: 48%;
}

/* Ensuring footer doesn't overlap content */
.content {
    padding-bottom: 60px; /* To prevent footer overlap */
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
<h4 class="text-center">Admin</h4>
<a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="text">Dashboard</span></a>
    <a href="user_manage.php"><i class="fas fa-users"></i><span class="text">User Management</span></a>
    <a href="faqs.php"><i class="fas fa-question-circle"></i><span class="text">FAQ</span></a>
    <a href="predict_trozen.php"><i class="fas fa-bug"></i><span class="text">Predict Trojan</span></a>
</div>

<!-- Main content -->
<div class="content">
    <div class="container">
        <h2 class="mb-4">Predict Trojan</h2>
        <div class="card">
            <h5 class="mb-3">Upload File for Prediction</h5>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" id="fileInput" class="form-control" accept=".csv" required>
                </div>
                <button type="button" class="btn-upload" onclick="uploadAndPredict()">Upload and Predict</button>
            </form>
            <div class="mt-4" id="predictionResult" style="display: none;">
                <h5>Prediction Result:</h5>
                <div class="chart-container">
                    <div class="chart-wrapper">
                        <canvas id="kmeansPieChart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="isolationBarChart"></canvas>
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

<!-- JavaScript for AJAX request and visualization -->
<script>
    function uploadAndPredict() {
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length === 0) {
            alert('Please select a file to upload.');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        // Upload the file to the server and train the model
        fetch('train_model.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to train the model.');
            }
            return response.text();
        })
        .then(trainingOutput => {
            console.log('Training Output:', trainingOutput);

            // Once the model is trained, send the file for prediction
            return fetch('http://127.0.0.1:5000/predict', {
                method: 'POST',
                body: formData
            });
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch prediction results.');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Display the prediction result
            document.getElementById('predictionResult').style.display = 'block';

            // Prepare data for the charts
            const kmeansLabels = Object.keys(data.kmeans_prediction);
            const kmeansValues = Object.values(data.kmeans_prediction);
            const isolationLabels = Object.keys(data.isolation_forest_prediction);
            const isolationValues = Object.values(data.isolation_forest_prediction);

            // Create the K-Means pie chart
            const kmeansCtx = document.getElementById('kmeansPieChart').getContext('2d');
            new Chart(kmeansCtx, {
                type: 'pie',
                data: {
                    labels: kmeansLabels,
                    datasets: [{
                        label: 'K-Means Prediction Count',
                        data: kmeansValues,
                        backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#28a745']
                    }]
                }
            });

            // Create the Isolation Forest bar chart
            const isolationCtx = document.getElementById('isolationBarChart').getContext('2d');
            new Chart(isolationCtx, {
                type: 'bar',
                data: {
                    labels: isolationLabels,
                    datasets: [{
                        label: 'Isolation Forest Prediction Count',
                        data: isolationValues,
                        backgroundColor: '#36a2eb'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => {
            alert('An error occurred: ' + error.message);
        });
    }

    // Sidebar toggle functionality
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

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
