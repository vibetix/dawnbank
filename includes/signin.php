<?php
header("Content-Type: application/json"); // Set response type to JSON
require "connect.php"; // Include database connection

$response = ["success" => false, "message" => ""]; // Default response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Get form data securely
        $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? "";

        // Validate fields
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required.");
        }

        // Check if the user exists and get all necessary details
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            throw new Exception("Invalid email.");
        }

        $stmt->bind_result($userId, $first_name, $last_name, $hashedPassword);
        $stmt->fetch();

        // Verify password
        if (!password_verify($password, $hashedPassword)) {
            throw new Exception("Incorrect password.");
        }

        // Login success - Start session and store user info
        session_start();
        $_SESSION["user_id"] = $userId;
        $_SESSION["email"] = $email;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;

        $response["success"] = true;
        $response["message"] = "Login successful!";
        $response["first_name"] = $first_name; // Send first name for welcome message

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    $response["message"] = "Invalid request.";
}

// Send JSON response
echo json_encode($response);
?>
