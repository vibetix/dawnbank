<?php
session_start();
require_once "connect.php";

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$stmt = $conn->prepare("SELECT account_type, account_name FROM accounts WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

if (empty($accounts)) {
    echo json_encode(["status" => "error", "message" => "No accounts found."]);
} else {
    echo json_encode(["status" => "success", "accounts" => $accounts]);
}
exit();
?>
