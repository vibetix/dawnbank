<?php
include "connect.php"; // Ensure database connection
session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $accountName = trim($_POST["accountName"]);
    $accountType = trim($_POST["accountType"]);

    if (empty($accountName) || empty($accountType)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }


    // Generate a unique 10-digit account number
    do {
        $accountNumber = mt_rand(1000000000, 9999999999);
        $stmt = $conn->prepare("SELECT account_id FROM Accounts WHERE account_number = ?");
        $stmt->bind_param("s", $accountNumber);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

   // Insert into database
    $stmt = $conn->prepare("INSERT INTO Accounts (user_id, account_name, account_type, account_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $accountName, $accountType, $accountNumber);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "accountNumber" => $accountNumber]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}  else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
