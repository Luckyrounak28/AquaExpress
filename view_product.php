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

// Check for actions (Remove or Delivered)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $orderId = $_POST['id'];
        if ($_POST['action'] === 'remove') {
            // Remove order
            $removeSql = "DELETE FROM productorder WHERE id = ?";
            $stmt = $conn->prepare($removeSql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] === 'delivered') {
            // Update order status to delivered
            $updateSql = "UPDATE productorder SET status = 'Delivered' WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Retrieve orders from the database
$sql = "SELECT * FROM productorder";
$result = $conn->query($sql);

// HTML structure for displaying orders
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - Saraswati Aqua Neer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }
        .header {
            padding: 20px;
            background-color: #0066cc;
            color: white;
            text-align: center;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #0066cc;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .action-button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin: 2px;
        }
        .remove {
            background-color: #dc3545; /* Bootstrap danger color */
        }
        .delivered {
            background-color: #28a745; /* Bootstrap success color */
        }
        .back-button {
            padding: 10px 15px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Saraswati Aqua Neer - View Orders</h1>
</div>

<div class="container">
    <h2>Order List</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Pincode</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['product']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['pincode']; ?></td>
                    <td>₹<?php echo number_format($row['totalPrice'], 2); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="delivered" class="action-button delivered">Delivered</button>
                            <button type="submit" name="action" value="remove" class="action-button remove">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

    <a href="index.html" class="back-button">Go Back</a>
</div>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
