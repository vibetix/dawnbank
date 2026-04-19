<?php
require_once "connect.php"; // Include database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountNumber = $_POST["accountNumber"];
    $accountType = $_POST["accountType"];
    $status = $_POST["status"];

    if (empty($accountNumber) || empty($accountType) || empty($status)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE accounts SET status = ? WHERE account_number = ? AND account_type = ?");
    $stmt->bind_param("sss", $status, $accountNumber, $accountType);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Account status updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Account not found or no changes made."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
