<?php
require 'connect.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanId = $_POST['loan_id'] ?? null;

    if (!$loanId) {
        echo json_encode(["status" => "error", "message" => "Loan ID is required"]);
        exit;
    }

    function generateTransactionId($length = 9) {
        return "Txn" . strtoupper(bin2hex(random_bytes($length / 2)));
    }

    // Step 1: Fetch the loan details (loan amount & user ID)
    $stmt = $conn->prepare("SELECT user_id, loan_amount FROM loans WHERE loan_id = ? AND status = 'pending'");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL Prepare Failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $loanId);
    $stmt->execute();
    $result = $stmt->get_result();
    $loan = $result->fetch_assoc();
    $stmt->close();

    if (!$loan) {
        echo json_encode(["status" => "error", "message" => "Loan not found or already processed"]);
        exit;
    }

    $userId = $loan['user_id'];
    $loanAmount = $loan['loan_amount'];

    // Step 2: Find the user's first account (smallest account_id)
    $stmt = $conn->prepare("SELECT account_id FROM accounts WHERE user_id = ? ORDER BY account_id ASC LIMIT 1");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL Prepare Failed: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if (!$account) {
        echo json_encode(["status" => "error", "message" => "User has no active accounts"]);
        exit;
    }

    $accountId = $account['account_id'];

    // Step 3: Begin Transaction (Atomic Process)
    $conn->begin_transaction();

    try {
        // Step 4: Deposit loan amount into the user's first account
        $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
        if (!$stmt) {
            throw new Exception("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("di", $loanAmount, $accountId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to deposit loan amount: " . $stmt->error);
        }
        $stmt->close();

        // Step 5: Insert transaction record (Using NOW() for transaction_date)
        $transactionType = "deposit";
        $description = "Loan deposit for loan ID #" . $loanId;
        $status = "completed";
        $transactionId = generateTransactionId();

        $stmt = $conn->prepare("INSERT INTO transactions (transaction_id, account_id, transaction_type, amount, transaction_date, description, status) 
                                VALUES (?, ?, ?, ?, NOW(), ?, ?)");

        if (!$stmt) {
            throw new Exception("SQL Prepare Failed (Transactions): " . $conn->error);
        }

        $stmt->bind_param("sissss", $transactionId, $accountId, $transactionType, $loanAmount, $description, $status);
        if (!$stmt->execute()) {
            throw new Exception("Failed to record transaction: " . $stmt->error);
        }
        $stmt->close();

        // Step 6: Approve the loan (Using NOW() for approval_date)
        $stmt = $conn->prepare("UPDATE loans SET status = 'approved' WHERE loan_id = ?");
        if (!$stmt) {
            throw new Exception("SQL Prepare Failed (Loans): " . $conn->error);
        }

        $stmt->bind_param("i", $loanId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to approve loan: " . $stmt->error);
        }
        $stmt->close();

        // Step 7: Commit transaction (all operations succeed)
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Loan approved, amount deposited, and transaction recorded successfully"]);

    } catch (Exception $e) {
        $conn->rollback(); // Rollback on failure
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    $conn->close();
}
?>
