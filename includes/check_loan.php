<?php
include "connect.php"; // Ensure database connection
session_start();

header("Content-Type: application/json");
// Check if user is authenticated
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch active loan (not PAID)
$sql = "SELECT loan_amount, remaining_balance, interest_rate, loan_term, status 
        FROM Loans WHERE user_id = ? AND status != 'PAID' LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $loan = $result->fetch_assoc();
    echo json_encode(["status" => "success", "loan" => $loan]);
} else {
    echo json_encode(["status" => "no_loan", "message" => "No active loan found."]);
}

$stmt->close();
$conn->close();
?>