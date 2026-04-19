<?php
session_start();
require 'connect.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "Admin not logged in"]);
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch the username
$stmt = $conn->prepare("SELECT username FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin) {
    // Extract first name (assuming first name is the first word in the username)
    $username_parts = preg_split('/[\s._-]+/', $admin['username']); // Split by space, dot, underscore, or dash
    $first_name = ucfirst(strtolower($username_parts[0])); // Capitalize first letter

    echo json_encode(["first_name" => $first_name]);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
