<?php
include "connect.php";

header("Content-Type: application/json");

if (!isset($_POST["account_id"])) {
    echo json_encode(["error" => "Missing account_id"]);
    exit;
}

$account_id = $_POST["account_id"];

// Step 1: Get user ID using account_id
$query = "SELECT user_id FROM accounts WHERE account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo json_encode(["error" => "User ID not found"]);
    exit;
}

$user_id = $row["user_id"];

// Step 2: Get all accounts for the user, excluding the sender's account
$query = "SELECT account_id, account_number, account_type , balance
          FROM accounts 
          WHERE user_id = ? AND account_id != ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $account_id);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

echo json_encode($accounts);
?>
