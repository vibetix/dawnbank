<?php
header("Content-Type: application/json");

session_start();
require_once "connect.php";

$response = ["status" => "error", "message" => "Unknown error"];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["user_id"])) {
    $user_id = intval($_GET["user_id"]);

    // ✅ Check if a welcome message exists for the user
    $stmt = $conn->prepare("SELECT message, created_at FROM LiveChat WHERE user_id = ? AND type = 'welcome' LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $welcome_message = $row["message"];
        $welcome_created_at = $row["created_at"];
    } else {
        // ✅ Insert a welcome message if it's the user's first time
        $welcome_message = "Welcome to DawnBank! How can we assist you?";
        $stmt = $conn->prepare("INSERT INTO LiveChat (user_id, message, sender, type) VALUES (?, ?, 'ADMIN', 'welcome')");
        $stmt->bind_param("is", $user_id, $welcome_message);
        $stmt->execute();
        $welcome_created_at = date("Y-m-d H:i:s"); // Current timestamp
    }
    $stmt->close();

    // ✅ Fetch all other chat messages (excluding welcome message)
    $stmt = $conn->prepare("SELECT message, sender, created_at FROM LiveChat WHERE user_id = ? AND type != 'welcome' ORDER BY created_at ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            "message" => $row["message"],
            "sender" => $row["sender"],
            "created_at" => $row["created_at"]
        ];
    }
    $stmt->close();

    // ✅ Return response with welcome message included
    $response = [
        "status" => "success",
        "welcome_message" => $welcome_message,
        "welcome_created_at" => $welcome_created_at,
        "messages" => $messages
    ];
} else {
    $response["message"] = "Invalid request";
}

$conn->close();
echo json_encode($response);
exit;
