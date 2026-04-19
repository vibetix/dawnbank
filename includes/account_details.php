<?php
header('Content-Type: application/json');
include 'connect.php';

// Debugging: Check if 'account_id' is received
if (!isset($_GET['account_id'])) {
    echo json_encode(["error" => "Unauthorized access. Account ID is missing.", "debug" => $_GET]);
    exit;
}

$account_id = intval($_GET['account_id']); // Ensure it's an integer

// Debugging: Check if conversion worked
if ($account_id <= 0) {
    echo json_encode(["error" => "Invalid account ID.", "debug" => $_GET['account_id']]);
    exit;
}

// Fetch account details
$query = "SELECT account_number, balance FROM accounts WHERE account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

if (!$account) {
    echo json_encode(["error" => "Account not found."]);
    exit;
}

// Fetch transaction history
$transactions = [];
$query = "SELECT transaction_id, transaction_type, amount, transaction_date, description, status 
          FROM transactions 
          WHERE account_id = ? 
          ORDER BY transaction_date DESC"; // Order by latest first

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row; // Store each row in the transactions array
}

// Return JSON response
echo json_encode([
    "account" => $account,
    "transactions" => $transactions
]);
?>
