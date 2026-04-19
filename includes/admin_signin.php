<?php
session_start();
require 'connect.php'; // Include the database connection

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Query to check user credentials
$stmt = $conn->prepare("SELECT admin_id, password, username FROM admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin && password_verify($password, $admin['password'])) {
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);
    
    // Store session data
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['username']; // Store admin name for easy retrieval
    $_SESSION['logged_in'] = true;

    echo json_encode(["status" => "success", "message" => "Login successful"]);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}

$stmt->close();
$conn->close();
?>
