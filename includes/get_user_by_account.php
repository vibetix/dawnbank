<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "connect.php";

// Log raw POST data for debugging
file_put_contents("debug.log", "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if (isset($_POST["account_id"])) {
    $account_id = $_POST["account_id"];

    // Fetch user_id along with account details
    $query = "SELECT user_id, account_number, account_type, balance FROM accounts WHERE account_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Account not found"]);
    }
} else {
    echo json_encode(["error" => "No account_id provided"]);
}
?>
