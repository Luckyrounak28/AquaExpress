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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Get the ID of the complaint to delete
    $delete_sql = "DELETE FROM complaints WHERE id = ?";

    if ($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            // Redirect to the same page to refresh
            header("Location: view_complaint.php");
            exit();
        } else {
            echo "Error deleting record: " . $stmt->error; // Debugging message
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error; // Debugging message
    }
}

// Fetch complaints
$sql = "SELECT * FROM complaints ORDER BY submission_date DESC"; // Fetching complaints ordered by submission date
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints - Saraswati Aqua Neer</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #e0f7fa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #c27f47;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        footer {
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, #c27f47, #00221e);
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .delete-button {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<h1>Complaints List</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Type of Complaint</th>
            <th>Description</th>
            <th>Submission Date</th>
            <th>Action</th> <!-- New Action column for Delete button -->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['complaint_type']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['submission_date']}</td>
                        <td><a href='?delete_id={$row['id']}' class='delete-button'>Delete</a></td> <!-- Delete button -->
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No complaints found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<footer>
    &copy; 2024 Saraswati Aqua Neer
</footer>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
