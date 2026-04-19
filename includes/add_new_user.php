<?php
require_once "connect.php"; // Database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $status = "approved";
    $clientId = "CL" . strtoupper(bin2hex(random_bytes(5)));

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required!"]);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email is already registered!"]);
        exit;
    }
    $stmt->close();

    // Hash password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, status, client_id) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $status, $clientId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Account created successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating account: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
