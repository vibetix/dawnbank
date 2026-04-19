<?php
require_once "connect.php"; // Include database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Secure password hashing

    // Update password based on first name, last name, and email
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE first_name = ? AND last_name = ? AND email = ?");
    $stmt->bind_param("ssss", $hashedPassword, $firstName, $lastName, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "User password updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found or no changes made."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
