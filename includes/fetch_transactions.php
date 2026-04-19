<?php
include "connect.php"; // Database connection
session_start();

header("Content-Type: application/json");

// Ensure user is authenticated
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    // Get all account IDs linked to the user
    $sql_accounts = "SELECT account_id FROM Accounts WHERE user_id = ?";
    $stmt_accounts = $conn->prepare($sql_accounts);
    $stmt_accounts->bind_param("i", $user_id);
    $stmt_accounts->execute();
    $result_accounts = $stmt_accounts->get_result();

    $account_ids = [];
    while ($row = $result_accounts->fetch_assoc()) {
        $account_ids[] = $row["account_id"];
    }
    $stmt_accounts->close();

    if (empty($account_ids)) {
        echo json_encode(["status" => "error", "message" => "No accounts found for this user."]);
        exit;
    }

    // Fetch transactions for all accounts
    $placeholders = implode(",", array_fill(0, count($account_ids), "?"));
    $sql = "SELECT transaction_id, description, transaction_type, amount, status,
                   DATE_FORMAT(transaction_date, '%b %e, %l:%i %p') AS transaction_date 
            FROM transactions WHERE account_id IN ($placeholders) 
            ORDER BY transaction_date DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL preparation failed: " . $conn->error);
    }

    $stmt->bind_param(str_repeat("i", count($account_ids)), ...$account_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            "transaction_id"   => $row["transaction_id"],
            "description"      => $row["description"],
            "transaction_type" => strtolower($row["transaction_type"]), // Ensure consistency with frontend
            "amount"           => number_format(abs($row["amount"]), 2), // Format amount
            "transaction_date" => $row["transaction_date"],
            "status"           => strtolower($row["status"]) // Pass status instead of amount sign
        ];
    }

    echo json_encode(["status" => "success", "transactions" => $transactions], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}
?>
