<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['file']['tmp_name'])) {
        $uploaded_file = $_FILES['file']['tmp_name'];
        
        // Define the command to run the ml_model.py with the uploaded file
        $command = escapeshellcmd("python ml_model.py " . escapeshellarg($uploaded_file));
        
        // Execute the command
        $output = shell_exec($command);

        // Display the output (for debugging purposes)
        echo $output;
    } else {
        echo "No file uploaded.";
    }
}
?>
