<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your actual database username
$password = ""; // Replace with your actual database password
$dbname = "saraswati_aqua_neer"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $complaintType = $_POST['complaintType'];
    $complaintDescription = $_POST['complaintDescription'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO complaints (name, phone, complaint_type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $complaintType, $complaintDescription);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to complaint.html with a success message
        header("Location: complaint.html?status=success");
        exit(); // Make sure to exit after the redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
