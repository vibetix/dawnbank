<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

try {
    // Begin database transaction
    $conn->begin_transaction();

    // Fetch matured investments
    $stmt = $conn->prepare("SELECT id, user_id, current_value FROM investments WHERE maturity_date <= NOW() AND status = 'active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $maturedInvestments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($maturedInvestments)) {
        echo json_encode(["status" => "success", "message" => "No matured investments found."]);
        exit;
    }

    foreach ($maturedInvestments as $investment) {
        $investmentId = $investment["id"];
        $userId = $investment["user_id"];
        $maturityAmount = $investment["current_value"];

        // Fetch a user account to deposit the money
        $stmt = $conn->prepare("SELECT account_id FROM Accounts WHERE user_id = ? ORDER BY account_id ASC LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($accountId);
        $stmt->fetch();
        $stmt->close();

        if (!$accountId) {
            throw new Exception("No account found for user $userId.");
        }

        // Deposit maturity amount into the selected account
        $stmt = $conn->prepare("UPDATE Accounts SET balance = balance + ? WHERE account_id = ?");
        $stmt->bind_param("di", $maturityAmount, $accountId);
        $stmt->execute();
        $stmt->close();

        // Generate a unique transaction ID
        function generateTransactionId($length = 9) {
            return "Txn" . strtoupper(bin2hex(random_bytes($length / 2)));
        }
        $transactionId = generateTransactionId();

        // Record the transaction
        $stmt = $conn->prepare("INSERT INTO Transactions (transaction_id, account_id, transaction_type, amount, transaction_date, description) VALUES (?, ?, 'DEPOSIT', ?, NOW(), 'Investment Maturity Payout')");
        $stmt->bind_param("sids", $transactionId, $accountId, $maturityAmount);
        $stmt->execute();
        $stmt->close();

        // Mark investment as completed
        $stmt = $conn->prepare("UPDATE investments SET status = 'completed', withdrawal_date = NOW() WHERE id = ?");
        $stmt->bind_param("i", $investmentId);
        $stmt->execute();
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Matured investments processed successfully."]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Close connection
$conn->close();
?>
