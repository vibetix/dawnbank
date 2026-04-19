<?php
require 'connect.php'; // Include database connection
session_start();

$user_id = $_SESSION['user_id']; // Ensure user is logged in

$sql = "SELECT id, initial_value, current_value, investment_type, investment_duration, maturity_date, status 
        FROM Investments 
        WHERE user_id = ? AND status = 'active'";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $investments = [];
    while ($row = $result->fetch_assoc()) {
        $investments[] = $row;
    }

    echo json_encode(["status" => "success", "investments" => $investments]);
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
}
$conn->close();
?>
