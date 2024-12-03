<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "saraswati_aqua_neer";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$phone = $_GET['phone'] ?? '';

if (empty($phone)) {
    echo json_encode(["error" => "Phone number is required."]);
    exit;
}

$sql = "SELECT name, phone, address, quantity, tap, total_price, delivery_date, delivery_time, created_at FROM orders WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
} else {
    echo json_encode(["error" => "No orders found for this phone number."]);
}

$stmt->close();
$conn->close();
?>
