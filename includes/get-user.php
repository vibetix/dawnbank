<?php
require_once "connect.php"; // Ensure this file properly connects to the database

header("Content-Type: application/json");

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize input

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $row['full_name'] = $row['first_name'] . " " . $row['last_name']; // Combine first and last name
        echo json_encode(["success" => true, "user" => $row]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "User ID is required."]);
}

$conn->close();
?>
