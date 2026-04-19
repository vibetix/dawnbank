<?php
require 'connect.php';
session_start();

// Debugging: Log incoming POST data
error_log("Received POST Data: " . json_encode($_POST));

// Check if 'account_id' exists in POST
if (!isset($_POST['account_id'])) {
    echo json_encode(["error" => "Unauthorized access. Account ID is missing."]);
    exit;
}

// Validate and sanitize account ID
$account_id = intval($_POST['account_id']);

if ($account_id <= 0) {
    echo json_encode(["error" => "Invalid account ID."]);
    exit;
}

// Function to generate a unique transaction ID
function generateTransactionId($length = 9) {
    return "Txn" . strtoupper(bin2hex(random_bytes($length / 2))); 
}

// Validate and sanitize input
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$type = isset($_POST['type']) ? strtolower(trim($_POST['type'])) : '';

// Convert "withdraw" to "withdrawal" (for consistency)
if ($type === 'withdraw') {
    $type = 'withdrawal';
}

// Debugging: Log received type
error_log("Received Type: " . $type);

if ($amount <= 0 || !in_array($type, ['deposit', 'withdrawal'])) {
    echo json_encode(['error' => 'Invalid amount or transaction type.']);
    exit;
}

// Fetch current balance
$query = "SELECT balance FROM accounts WHERE account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

if (!$account) {
    echo json_encode(['error' => 'Account not found.']);
    exit;
}

// Check for insufficient funds if withdrawing
if ($type === 'withdrawal' && $amount > $account['balance']) {
    echo json_encode(['error' => 'Insufficient funds.']);
    exit;
}

// Calculate new balance
$new_balance = ($type === 'deposit') ? $account['balance'] + $amount : $account['balance'] - $amount;

// Update balance in database
$query = "UPDATE accounts SET balance = ? WHERE account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("di", $new_balance, $account_id);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Failed to update balance.', 'sql_error' => $stmt->error]);
    exit;
}

// Generate unique transaction ID
$transaction_id = generateTransactionId();

// Debugging: Check if values are correct before inserting transaction
error_log("Inserting Transaction: ID=$transaction_id, Account=$account_id, Type=$type, Amount=$amount");

// Insert transaction into database
$query = "INSERT INTO transactions (transaction_id, account_id, transaction_date, description, amount, transaction_type, status) 
          VALUES (?, ?, NOW(), ?, ?, ?, 'Completed')";
$description = ucfirst($type) . " funds";
$stmt = $conn->prepare($query);
$stmt->bind_param("sisss", $transaction_id, $account_id, $description, $amount, $type);

// Debugging: Log SQL execution status
if ($stmt->execute()) {
    error_log("Transaction inserted successfully: ID=$transaction_id, Type=$type");

    echo json_encode([
        'success' => 'Transaction successful',
        'transaction_id' => $transaction_id,
        'new_balance' => $new_balance
    ]);
} else {
    error_log("Transaction insert failed: " . $stmt->error);
    
    echo json_encode([
        'error' => 'Transaction failed.',
        'sql_error' => $stmt->error
    ]);
}
?>
