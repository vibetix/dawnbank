<?php
session_start();
require_once "connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT account_id, account_type, balance FROM Accounts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$accounts = [];

while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

$stmt->close();
echo json_encode(["status" => "success", "accounts" => $accounts]);
?>
