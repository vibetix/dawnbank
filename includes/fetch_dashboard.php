<?php
require_once "connect.php";

session_start();
$userId = $_SESSION["user_id"] ?? 0;

if (!$userId) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Check if the connection is valid
if (!$conn) {
    echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
    exit;
}

// Function to execute queries safely
function executeQuery($conn, $sql, $types = "", ...$params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "SQL Prepare Error: " . $conn->error]);
        exit;
    }
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        echo json_encode(["error" => "SQL Execution Error: " . $stmt->error]);
        exit;
    }
    return $stmt->get_result();
}

// Get all accounts of the user
$result = executeQuery($conn, "SELECT account_id, balance FROM accounts WHERE user_id = ?", "i", $userId);

$accounts = [];
$totalBalance = 0;
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row["account_id"];
    $totalBalance += $row["balance"]; // Sum up balances of all accounts
}

if (empty($accounts)) {
    echo json_encode(["error" => "No accounts found for this user"]);
    exit;
}

// Convert account IDs into a comma-separated string for SQL IN clause
$placeholders = implode(",", array_fill(0, count($accounts), "?"));
$types = str_repeat("i", count($accounts)); // 'i' for each account_id

// Get total transfers
$sql = "SELECT SUM(amount) AS totalTransfer FROM transactions WHERE transaction_type = 'transfer' AND account_id IN ($placeholders)";
$result = executeQuery($conn, $sql, $types, ...$accounts);
$totalTransfer = $result->fetch_assoc()["totalTransfer"] ?? 0;

// Get total deposits
$sql = "SELECT SUM(amount) AS totalDeposit FROM transactions WHERE transaction_type = 'deposit' AND account_id IN ($placeholders)";
$result = executeQuery($conn, $sql, $types, ...$accounts);
$totalDeposit = $result->fetch_assoc()["totalDeposit"] ?? 0;

// Get total transactions in the last month
$lastMonth = date('Y-m-01', strtotime("-1 month"));
$sql = "SELECT SUM(amount) AS totalTransactions FROM transactions WHERE transaction_date >= ? AND account_id IN ($placeholders)";
$result = executeQuery($conn, $sql, "s" . $types, $lastMonth, ...$accounts);
$totalTransactions = $result->fetch_assoc()["totalTransactions"] ?? 0;

// Send JSON response
echo json_encode([
    "balance" => number_format($totalBalance, 2),
    "transfer" => number_format($totalTransfer, 2),
    "deposit" => number_format($totalDeposit, 2),
    "transactions" => number_format($totalTransactions, 2)
]);
?>
