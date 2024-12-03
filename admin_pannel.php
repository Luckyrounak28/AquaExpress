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

// Handle order removal and delivery updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $orderId = $_POST['orderId'];

        if ($action == 'remove') {
            $deleteSql = "DELETE FROM orders WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $orderId);
            $deleteStmt->execute();
            if ($deleteStmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Order removed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to remove order.']);
            }
            exit; // Stop execution after sending a response
        } elseif ($action == 'mark_delivered') {
            $updateSql = "UPDATE orders SET status = 'delivered' WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $orderId);
            $updateStmt->execute();
            if ($updateStmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Order marked as delivered']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update order status.']);
            }
            exit; // Stop execution after sending a response
        }
    }
}

// Fetch orders
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Orders</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #d1e7ff;
        }
        .delivered {
            color: green;
            font-weight: bold;
        }
        .action-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .action-button:hover {
            background-color: #2980b9;
        }
        .action-button.remove {
            background-color: #e74c3c;
        }
        .action-button.remove:hover {
            background-color: #c0392b;
        }
    </style>
    <script>
        function performAction(orderId, action) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Sends request to the same page
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        alert(response.message); // Show success message
                        if (action === 'remove') {
                            document.getElementById('order-row-' + orderId).remove(); // Remove row from the table
                        } else if (action === 'mark_delivered') {
                            // Update the status to delivered and add the green tick
                            var statusCell = document.getElementById('order-row-' + orderId).querySelector('.status');
                            statusCell.innerHTML = '✓'; // Update status cell
                            statusCell.classList.add('delivered'); // Add class for green color
                        }
                    } else {
                        alert('An error occurred: ' + response.message); // Error message
                    }
                }
            };

            xhr.send("action=" + encodeURIComponent(action) + "&orderId=" + encodeURIComponent(orderId));
        }
    </script>
</head>
<body>

<h1>Order List</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Quantity</th>
            <th>Tap</th>
            <th>Total Price</th>
            <th>Delivery Date</th>
            <th>Delivery Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='order-row-{$row['id']}'>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['tap']}</td>
                        <td>₹{$row['total_price']}</td>
                        <td>{$row['delivery_date']}</td>
                        <td>{$row['delivery_time']}</td>
                        <td class='status'>" . ($row['status'] == 'delivered' ? "✓" : "") . "</td>
                        <td>
                            <button onclick='performAction({$row['id']}, \"remove\")' class='action-button remove'>Remove</button>
                            <button onclick='performAction({$row['id']}, \"mark_delivered\")' class='action-button'>Deliver</button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No orders found.</td></tr>"; // Updated colspan for 11 columns
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
