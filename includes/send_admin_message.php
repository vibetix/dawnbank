<?php
include 'connect.php'; // Ensure $conn is properly initialized

header("Content-Type: application/json"); // Set response header as JSON

$response = ["success" => false]; // Default response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? null;
    $message = $_POST["message"] ?? null;

    // Check if user_id and message are provided
    if (empty($user_id) || empty($message)) {
        $response["error"] = "User ID and message are required.";
        echo json_encode($response);
        exit;
    }

    // Insert message into the database
    $sql = "INSERT INTO livechat (user_id, message, sender, created_at) VALUES (?, ?, 'ADMIN', NOW())";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $user_id, $message);
        
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Message sent successfully.";
        } else {
            $response["error"] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["error"] = "Database error: " . $conn->error;
    }
}

echo json_encode($response);
$conn->close();
?>
