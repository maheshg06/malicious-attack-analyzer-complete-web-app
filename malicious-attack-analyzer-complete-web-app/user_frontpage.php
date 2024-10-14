<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malware Analysis System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .hero-section {
            background: url('42089925_8982017.jpg') no-repeat center center/cover;
            color: #fff;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 48px;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 20px;
        }
        .about-section {
            padding: 40px 0;
            background-color: #fff;
        }
        .about-section img {
            max-width: 100%;
            border-radius: 8px;
        }
        .upload-section {
            padding: 40px 0;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 20px;
        }
        .btn-upload {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-upload:hover {
            background-color: #218838;
        }
        .chart-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .chart-wrapper {
            width: 48%;
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

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1>Welcome to the Malware Analysis System</h1>
        <p>Predict and classify malware types in uploaded files.</p>
    </div>
</div>

<div class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>About Malware Analysis</h2>
                <p>Malware analysis is the process of determining the purpose and functionality of a given malware sample. It helps in understanding the behavior of the malware, its possible impacts on systems, and strategies to prevent future attacks.</p>
                <p>Our system provides an easy way for users to upload files for malware prediction and classification, offering detailed reports on the identified threats.</p>
            </div>
            <div class="col-md-6">
                <img src="26242755_7169070.jpg" alt="Malware Analysis" class="img-fluid">
            </div>
        </div>
    </div>
</div>


<!-- File Upload Section -->
<div class="upload-section">
    <div class="container">
        <h2 class="text-center mb-4">Check File for Malware</h2>
        <div class="card p-4">
            <form id="uploadForm">
                <div class="form-group mb-3">
                    <input type="file" class="form-control" id="fileInput" accept=".exe, .dll, .bin, .csv">
                </div>
                <button type="button" class="btn-upload" onclick="uploadAndAnalyze()">Upload and Analyze</button>
            </form>
            <div class="mt-4" id="analysisResult" style="display: none;">
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
    <div class="container">
        <p>&copy; 2024 Malware Analysis System. All rights reserved.</p>
    </div>
</div>

<!-- JavaScript -->
<script>
    function uploadAndAnalyze() {
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length === 0) {
            alert('Please select a file to upload.');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        // Send the uploaded file to the Flask server for prediction
        fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: formData
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
            document.getElementById('analysisResult').style.display = 'block';

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
</script>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
