<?php
require_once 'connect.php';

$query = "
    SELECT 
        t.transaction_id, 
        a.account_number, 
        a.account_type, 
        t.description, 
        t.amount, 
        t.transaction_type, 
        t.transaction_date, 
        t.status
    FROM transactions t
    JOIN accounts a ON t.account_id = a.account_id
    ORDER BY t.transaction_date DESC
";

$result = $conn->query($query);

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

// Ensure we return a valid JSON
header('Content-Type: application/json');
echo json_encode(["transactions" => $transactions], JSON_PRETTY_PRINT);
?>
