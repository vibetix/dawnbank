<?php
header('Content-Type: application/json');
include "connect.php"; // Ensure database connection

// Get user input from AJAX request
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Validate inputs
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// Check if email already exists
$checkEmailSQL = "SELECT admin_id FROM admin WHERE email = ?";
$stmt = $conn->prepare($checkEmailSQL);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "SQL Error (Check Email): " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists."]);
    exit();
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$insertSQL = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)"; // Ensure table name is correct
$stmt = $conn->prepare($insertSQL);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "SQL Error (Insert): " . $conn->error]);
    exit();
}

$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Admin account created successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Execution Error: " . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
