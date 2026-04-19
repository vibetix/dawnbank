<?php
session_start();
require_once "connect.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    if (!isset($_SESSION["user_id"])) {
        throw new Exception("User not authenticated. Please log in.");
    }

    $user_id = $_SESSION["user_id"];
    $repayAmount = isset($_POST["repayAmount"]) ? floatval($_POST["repayAmount"]) : 0;
    $repayMethod = $_POST["repayMethod"] ?? '';

    if ($repayAmount <= 0 || empty($repayMethod)) {
        throw new Exception("Invalid repayment details. Please check your inputs.");
    }

    $stmt = $conn->prepare("SELECT account_id, balance FROM Accounts WHERE user_id = ? AND account_type = ?");
    $stmt->bind_param("is", $user_id, $repayMethod);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if (!$account) {
        throw new Exception("Invalid account selection.");
    }

    $account_id = $account["account_id"];
    $currentBalance = $account["balance"];

    if ($repayAmount > $currentBalance) {
        throw new Exception("Insufficient funds in the selected account.");
    }

    $stmt = $conn->prepare("SELECT loan_id, remaining_balance FROM Loans WHERE user_id = ? AND remaining_balance > 0 ORDER BY loan_id LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loan = $result->fetch_assoc();
    $stmt->close();

    if (!$loan) {
        throw new Exception("No active loans found.");
    }

    $loanId = $loan["loan_id"];
    $remaining_balance = $loan["remaining_balance"];

    if ($repayAmount > $remaining_balance) {
        throw new Exception("Repayment amount exceeds the outstanding loan balance.");
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("UPDATE Accounts SET balance = balance - ? WHERE account_id = ? AND balance >= ?");
    $stmt->bind_param("dii", $repayAmount, $account_id, $repayAmount);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        throw new Exception("Insufficient funds or account issue.");
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO LoanRepayments (loan_id, account_id, amount_paid) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $loanId, $account_id, $repayAmount);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE Loans SET remaining_balance = remaining_balance - ? WHERE loan_id = ?");
    $stmt->bind_param("di", $repayAmount, $loanId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Transactions (user_id, account_id, transaction_type, amount, description, transaction_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $transactionType = "Loan Repayment";
    $description = "Loan repayment for Loan ID: " . $loanId;
    $stmt->bind_param("iisds", $user_id, $account_id, $transactionType, $repayAmount, $description);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Payment successful! Transaction recorded."]);
} catch (Exception $e) {
    if ($conn->in_transaction) {
        $conn->rollback();
    }
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
