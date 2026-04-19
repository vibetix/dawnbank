<?php
require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["transaction_id"])) {
    $transaction_id = $_POST["transaction_id"];

    // Validate transaction_id (allow only alphanumeric)
    if (!preg_match('/^[a-zA-Z0-9]+$/', $transaction_id)) {
        echo json_encode(["success" => false, "message" => "Invalid transaction ID format."]);
        exit;
    }

    // Fetch transaction details
    $query = "SELECT account_id, amount, status, transaction_type FROM transactions WHERE transaction_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $transaction = $result->fetch_assoc();
        $account_id = $transaction["account_id"];
        $amount = $transaction["amount"];
        $status = $transaction["status"];
        $transaction_type = strtolower($transaction["transaction_type"]); // Convert to lowercase for consistency

        if ($status === "reversed") {
            echo json_encode(["success" => false, "message" => "Transaction already reversed."]);
            exit;
        }

        $conn->begin_transaction();
        try {
            // Reverse transaction
            $updateTransaction = "UPDATE transactions SET status = 'reversed' WHERE transaction_id = ?";
            $stmt = $conn->prepare($updateTransaction);
            $stmt->bind_param("s", $transaction_id);
            $stmt->execute();

            // Adjust balance based on transaction type
            if ($transaction_type === "transfer" || $transaction_type === "withdrawal") {
                // If transfer or withdrawal, **add** back the amount
                $updateBalance = "UPDATE accounts SET balance = balance + ? WHERE account_id = ?";
            } elseif ($transaction_type === "deposit") {
                // If deposit, **deduct** the amount
                $updateBalance = "UPDATE accounts SET balance = balance - ? WHERE account_id = ?";
            } else {
                throw new Exception("Invalid transaction type.");
            }

            $stmt = $conn->prepare($updateBalance);
            $stmt->bind_param("di", $amount, $account_id);
            $stmt->execute();

            $conn->commit();
            echo json_encode(["success" => true, "message" => "Reversal successful."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid transaction ID."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
