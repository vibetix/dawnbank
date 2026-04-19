<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "Admin not logged in"]);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(["error" => "select a chat to start"]);
    exit;
}

// Check if user exists
$user_check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$user_check_stmt->bind_param("i", $user_id);
$user_check_stmt->execute();
$user_check_result = $user_check_stmt->get_result();

if ($user_check_result->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

// ✅ Now also select the `id` of the message
$stmt = $conn->prepare("SELECT chat_id, message, sender, created_at FROM livechat WHERE user_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        "id" => $row['chat_id'],   // <-- Added
        "message" => $row['message'],
        "sender" => $row['sender'],
        "created_at" => $row['created_at']
    ];
}

echo json_encode($messages);
?>
