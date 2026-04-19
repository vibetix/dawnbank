<?php
require_once "connect.php"; // Ensure this file properly connects to the database

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['user_id'])) {
        echo json_encode(["success" => false, "message" => "User ID is missing."]);
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $first_name = trim($_POST['firstname']);
    $last_name = trim($_POST['lastname']);
    $full_name = $first_name . " " . $last_name;
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $id_type = trim($_POST['id_type']);
    $id_number = trim($_POST['id_number']);
    $current_profile_pic = $_POST['current_profile_pic'];

    // Ensure uploads directory exists
    $upload_dir = __DIR__ . "/uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle profile picture upload
    $profile_pic = $current_profile_pic; // Default to current picture
    if (!empty($_FILES["image"]["name"])) {
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $profile_pic = $file_name; // Update profile picture filename
        } else {
            echo json_encode(["success" => false, "message" => "Failed to upload profile picture."]);
            exit;
        }
    }

    // Update user data in the database
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=?, id_type=?, id_number=?, profile_pic=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone, $address, $id_type, $id_number, $profile_pic, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update profile."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
