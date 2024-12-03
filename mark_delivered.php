<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
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

    $id = intval($_GET['id']);
    
    // Prepare SQL query to update the delivered status
    $sql = "UPDATE orders SET delivered = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
