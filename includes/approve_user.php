<?php
require 'connect.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        exit;
    }

    // Generate a random Client ID
    $clientId = "CL" . strtoupper(bin2hex(random_bytes(5)));

    // Update the user's status and insert Client ID
    $stmt = $conn->prepare("UPDATE users SET status = 'approved', client_id = ? WHERE id = ?");
    $stmt->bind_param("si", $clientId, $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User approved successfully", "client_id" => $clientId]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to approve user"]);
    }

    $stmt->close();
    $conn->close();
}
?>
