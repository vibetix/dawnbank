<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = [];

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$response["user"] = $result->fetch_assoc();

// Fetch all linked accounts from accounts table
$sql = "SELECT * FROM accounts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

$response["accounts"] = $accounts;

// Return JSON response
echo json_encode($response);
?>
