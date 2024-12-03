<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Retrieve form data
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $quantity = (int)$_POST['quantity'];
    $tap = (int)$_POST['tap'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['delivery_time'];

    // Calculate total price
    $pricePerBottle = 25;
    $pricePerTap = 50;
    $total_price = ($quantity * $pricePerBottle) + ($tap * $pricePerTap);

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

    // Prepare SQL query
    $sql = "INSERT INTO orders (name, phone, address, quantity, tap, total_price, delivery_date, delivery_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Use prepared statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the statement
    $stmt->bind_param("sssiiiss", $name, $phone, $address, $quantity, $tap, $total_price, $delivery_date, $delivery_time);

    // Execute and redirect if successful
    if ($stmt->execute()) {
        // Redirect to confirm.html with order details in query string
        $query = http_build_query([
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'quantity' => $quantity,
            'tap' => $tap,
            'totalPrice' => $total_price,
            'deliveryDate' => $delivery_date,
            'deliveryTime' => $delivery_time
        ]);
        header("Location: confirm.html?$query");
        exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
