<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

try {
    // Ensure request method is POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request.");
    }

    // Check if user is logged in
    if (!isset($_SESSION["user_id"])) {
        throw new Exception("Please log in.");
    }

    $user_id = $_SESSION["user_id"];

    // Function to generate a unique transaction ID
    function generateTransactionId($length = 9) {
        return "Txn" . strtoupper(bin2hex(random_bytes($length / 2))); 
    }

    // Validate input values
    $investmentAmount = filter_input(INPUT_POST, "investmentAmount", FILTER_VALIDATE_FLOAT);
    $investmentType = filter_input(INPUT_POST, "investmentType", FILTER_SANITIZE_STRING);
    $investmentDuration = filter_input(INPUT_POST, "investmentDuration", FILTER_VALIDATE_INT);
    $accountId = filter_input(INPUT_POST, "selectedAccount", FILTER_VALIDATE_INT);
    $returnRate = filter_input(INPUT_POST, "returnRate", FILTER_VALIDATE_FLOAT);
    $maturityDate = filter_input(INPUT_POST, "maturityDate", FILTER_SANITIZE_STRING); // Receive maturity date from AJAX

    if (!$investmentAmount || $investmentAmount <= 0 || !$investmentType || !$investmentDuration || $investmentDuration <= 0 || !$accountId || !$returnRate || $returnRate <= 0 || !$maturityDate) {
        throw new Exception("Invalid input values.");
    }

    // Fetch account balance
    $stmt = $conn->prepare("SELECT balance FROM Accounts WHERE user_id = ? AND account_id = ?");
    $stmt->bind_param("ii", $user_id, $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if (!$account) {
        throw new Exception("Invalid account.");
    }

    if ($investmentAmount > $account["balance"]) {
        throw new Exception("Insufficient funds.");
    }

    // Begin transaction
    $conn->begin_transaction();

    // Deduct funds from the account
    $stmt = $conn->prepare("UPDATE Accounts SET balance = balance - ? WHERE account_id = ?");
    $stmt->bind_param("di", $investmentAmount, $accountId);
    $stmt->execute();
    $stmt->close();

    $transaction_id = generateTransactionId();

    // Insert transaction record
    $stmt = $conn->prepare("INSERT INTO Transactions (transaction_id, account_id, transaction_type, amount, transaction_date, description) VALUES (?, ?, 'WITHDRAWAL', ?, NOW(), ?)");
    $description = "Investment in $investmentType";
    $stmt->bind_param("sids", $transaction_id, $accountId, $investmentAmount, $description);
    $stmt->execute();
    $stmt->close();

    // Insert investment record (WITH MATURITY DATE AS WITHDRAWAL DATE)
    $stmt = $conn->prepare("
        INSERT INTO investments 
        (user_id, initial_value, current_value, investment_type, return_rate, investment_duration, maturity_date, withdrawal_date, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
    ");

    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    // Bind parameters (withdrawal_date = maturity_date)
    $stmt->bind_param("iddsdiss", $user_id, $investmentAmount, $investmentAmount, $investmentType, $returnRate, $investmentDuration, $maturityDate, $maturityDate);

    if (!$stmt->execute()) {
        throw new Exception("Execution Error: " . $stmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Investment added successfully"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Close connection
$conn->close();
?>
