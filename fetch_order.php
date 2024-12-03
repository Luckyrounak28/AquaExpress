<?php
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

// Check if an action is requested
if (isset($_GET['action']) && isset($_GET['id'])) {
    $orderId = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'deliver') {
        // Mark order as delivered
        $sql = "UPDATE orders SET delivered = 1 WHERE id = ?";
    } elseif ($action === 'remove') {
        // Remove order
        $sql = "DELETE FROM orders WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        echo "<p>Action '$action' completed successfully.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close statement and exit to prevent further execution
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $orderId);
    exit();
}

// Check if the order ID is provided to display details
if (isset($_GET['id'])) {
    $orderId = intval($_GET['id']);

    // Prepare SQL query to fetch order details
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();

            // Output the order details
            echo "<h1>Order Details</h1>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($order['name']) . "</p>";
            echo "<p><strong>Phone:</strong> " . htmlspecialchars($order['phone']) . "</p>";
            echo "<p><strong>Address:</strong> " . htmlspecialchars($order['address']) . "</p>";
            echo "<p><strong>Quantity:</strong> " . htmlspecialchars($order['quantity']) . "</p>";
            echo "<p><strong>Tap:</strong> " . htmlspecialchars($order['tap']) . "</p>";
            echo "<p><strong>Total Price:</strong> ₹" . htmlspecialchars($order['total_price']) . "</p>";
            echo "<p><strong>Delivery Date:</strong> " . htmlspecialchars($order['delivery_date']) . "</p>";
            echo "<p><strong>Delivery Time:</strong> " . htmlspecialchars($order['delivery_time']) . "</p>";
            echo "<p><strong>Status:</strong> " . ($order['delivered'] ? "Delivered" : "Pending") . "</p>";

            // Add action buttons
            if (!$order['delivered']) {
                echo "<a href='?action=deliver&id=$orderId' style='color: green;'>Mark as Delivered</a> ";
            }
            echo "<a href='?action=remove&id=$orderId' style='color: red;'>Remove Order</a>";
        } else {
            echo "<p>No order found with this ID.</p>";
        }
    } else {
        echo "<p>Error fetching order: " . $stmt->error . "</p>";
    }

    $stmt->close();
} else {
    echo "<p>Order ID not provided.</p>";
}

$conn->close();
?>
