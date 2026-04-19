<?php
require_once "connect.php"; // Database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST["reportType"];
    $sortBy = $_POST["sortBy"] ?? "transaction_date";

    // Define the date filter
    $dateCondition = "1"; // Default (no filter)
    if ($reportType == "weekly") {
        $dateCondition = "DATE(t.transaction_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($reportType == "monthly") {
        $dateCondition = "MONTH(t.transaction_date) = MONTH(CURDATE())";
    } elseif ($reportType == "yearly") {
        $dateCondition = "YEAR(t.transaction_date) = YEAR(CURDATE())";
    }

    // Fetch transactions and include user details
    $query = "
        SELECT 
            t.transaction_id, 
            t.account_id, 
            u.first_name, 
            u.last_name, 
            t.amount, 
            t.transaction_type, 
            t.transaction_date
        FROM transactions t
        JOIN accounts a ON t.account_id = a.account_id
        JOIN users u ON a.user_id = u.id
        WHERE $dateCondition
        ORDER BY $sortBy DESC";

    $result = $conn->query($query);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row["full_name"] = $row["first_name"] . " " . $row["last_name"];
            unset($row["first_name"], $row["last_name"]); // Remove separate fields
            $data[] = $row;
        }
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        echo json_encode(["success" => false, "message" => "Database query error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
