<?php
require_once "connect.php"; // Database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["id"])) {
        echo json_encode(["success" => false, "error" => "User ID missing"]);
        exit;
    }

    $userId = intval($_POST["id"]); // Ensure ID is an integer

    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete user"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
?>
