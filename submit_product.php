<?php
// Database connection parameters
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "saraswati_aqua_neer"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$product = $_POST['product'];
$quantity = (int)$_POST['quantity']; // Cast to integer
$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$pincode = (int)$_POST['pincode']; // Cast to integer if necessary
$totalPrice = (float)$_POST['finalPrice']; // Make sure to use the hidden input name

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO productorder (product, quantity, name, phone, address, pincode, totalPrice) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissssi", $product, $quantity, $name, $phone, $address, $pincode, $totalPrice);

// Execute the statement
if ($stmt->execute()) {
    echo "New order recorded successfully";
} else {
    echo "Error: " . $stmt->error; // Provide detailed error message
}

// Close connections
$stmt->close();
$conn->close();
?>
