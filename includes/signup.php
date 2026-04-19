<?php
header("Content-Type: application/json"); // Send JSON response
require "connect.php"; // Database connection file

$response = ["success" => false, "message" => ""]; // Default response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Securely get form data with null coalescing operator
        $firstName = htmlspecialchars(trim($_POST["firstName"] ?? ""));
        $lastName = htmlspecialchars(trim($_POST["lastName"] ?? ""));
        $dob = htmlspecialchars(trim($_POST["dob"] ?? ""));
        $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars(trim($_POST["phone"] ?? ""));
        $address = htmlspecialchars(trim($_POST["address"] ?? ""));
        $country = htmlspecialchars(trim($_POST["country"] ?? ""));
        $city = htmlspecialchars(trim($_POST["city"] ?? ""));
        $idType = htmlspecialchars(trim($_POST["idType"] ?? ""));
        $idNumber = htmlspecialchars(trim($_POST["idNumber"] ?? ""));
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirmPassword"] ?? "";

        // Required field validation
        if (!$firstName || !$lastName || !$email || !$phone || !$password || !$confirmPassword) {
            throw new Exception("All required fields must be filled.");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Validate password match
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        // Check if email is already registered
        $checkUser = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkUser->bind_param("s", $email);
        $checkUser->execute();
        $checkUser->store_result();

        if ($checkUser->num_rows > 0) {
            throw new Exception("Email is already registered.");
        }
        $checkUser->close();

        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Default client ID (NULL, will be assigned by admin)
        $clientId = NULL;

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users 
            (first_name, last_name, dob, email, phone, address, country, city, id_type, id_number, password, client_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", 
            $firstName, $lastName, $dob, $email, $phone, $address, 
            $country, $city, $idType, $idNumber, $hashedPassword, $clientId
        );

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Signup successful! Log in & await approval.";
        } else {
            throw new Exception("Error registering user. Please try again.");
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }
} else {
    $response["message"] = "Invalid request.";
}

// Send JSON response
echo json_encode($response);
?>
