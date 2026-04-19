<?php
// Use absolute path if necessary
include __DIR__ . '/connect.php'; // Change this path if your file is named differently

header('Content-Type: application/json');

if (!isset($conn)) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']); // Ensure it's an integer

    $query = "UPDATE livechat SET is_read = 1 WHERE user_id = ? AND sender = 'user' AND is_read = 0";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "User ID missing"]);
}

$conn->close();
?>
