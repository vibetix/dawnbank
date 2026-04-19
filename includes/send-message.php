<?php
require_once "connect.php"; // Ensure DB connection is included

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? null;
    $message = trim($_POST["message"] ?? "");
    $sender = $_POST["sender"] ?? "USER"; // Default to "USER"

    if (!$user_id || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Missing user ID or message"]);
        exit;
    }

    // ✅ Check if this is the user's first-ever message (EXCLUDING ADMIN messages)
    $checkFirstMessage = $conn->prepare("SELECT COUNT(*) AS count FROM LiveChat WHERE user_id = ? AND sender = 'USER'");
    if (!$checkFirstMessage) {
        echo json_encode(["status" => "error", "message" => "SQL Error (checkFirstMessage): " . $conn->error]);
        exit;
    }

    $checkFirstMessage->bind_param("i", $user_id);
    $checkFirstMessage->execute();
    $result = $checkFirstMessage->get_result();
    $row = $result->fetch_assoc();
    $message_count = $row['count'] ?? 0;
    $checkFirstMessage->close();

    $firstMessage = ($message_count == 0); // ✅ True if user has NEVER sent a message

    // ✅ Insert the user's message
    $insertMessage = $conn->prepare("INSERT INTO LiveChat (user_id, message, sender, type, created_at) VALUES (?, ?, ?, 'user-message', NOW())");
    if (!$insertMessage) {
        echo json_encode(["status" => "error", "message" => "SQL Error (insertMessage): " . $conn->error]);
        exit;
    }

    $insertMessage->bind_param("iss", $user_id, $message, $sender);
    if (!$insertMessage->execute()) {
        echo json_encode(["status" => "error", "message" => "Failed to send message: " . $insertMessage->error]);
        exit;
    }
    $insertMessage->close();

    $response = ["status" => "success", "first_message" => $firstMessage];

    // ✅ Only send auto-reply if this is the user's first message
    if ($firstMessage) {
        $autoReply = "Thank you for reaching out! Our support team will attend to you shortly.";
        $insertAutoReply = $conn->prepare("INSERT INTO LiveChat (user_id, message, sender, type, created_at) VALUES (?, ?, 'ADMIN', 'auto-reply', NOW())");
        if ($insertAutoReply) {
            $insertAutoReply->bind_param("is", $user_id, $autoReply);
            $insertAutoReply->execute();
            $insertAutoReply->close();
        }
    }

    echo json_encode($response);
}
?>
