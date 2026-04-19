<?php
require 'connect.php'; // Ensure database connection is included

$sql = "SELECT transaction_id, account_id, amount, status, transaction_type, transaction_date 
        FROM transactions 
        ORDER BY transaction_date DESC 
        LIMIT 5";

$result = $conn->query($sql);

$transactions = [];

if ($result === false) {
    die("Error fetching transactions: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = "Unknown User"; // Default user name
        
        // Step 1: Fetch user_id using account_id from accounts table
        $accountQuery = $conn->prepare("SELECT user_id FROM accounts WHERE account_id = ?");
        if ($accountQuery) {
            $accountQuery->bind_param("i", $row['account_id']);
            $accountQuery->execute();
            $accountResult = $accountQuery->get_result();
            $accountRow = $accountResult->fetch_assoc();
            $userId = $accountRow['user_id'] ?? null;
            
            // Step 2: Fetch first_name and last_name using user_id from users table
            if ($userId) {
                $userQuery = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = ?");
                if ($userQuery) {
                    $userQuery->bind_param("i", $userId);
                    $userQuery->execute();
                    $userResult = $userQuery->get_result();
                    $userRow = $userResult->fetch_assoc();
                    $fullName = $userRow['full_name'] ?? "Unknown User";
                }
            }
        }

        // Add transaction data to the array
        $transactions[] = [
            "id" => $row["transaction_id"],
            "user" => $fullName,
            "amount" => number_format($row["amount"], 2),
            "status" => ucfirst($row["status"]),
            "type" => ucfirst($row["transaction_type"]),
            "date" => date("M d, Y h:i A", strtotime($row["transaction_date"]))
        ];
    }
}

echo json_encode($transactions);
$conn->close();
?>
