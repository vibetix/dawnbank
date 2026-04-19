<?php
include "connect.php";
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT account_id, account_name, account_type, account_number, balance, created_at FROM Accounts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

echo json_encode($accounts);
$stmt->close();
$conn->close();
?>
