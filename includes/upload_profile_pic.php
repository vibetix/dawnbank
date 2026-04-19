<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profilePic'])) {
    $file = $_FILES['profilePic'];
    $fileName = time() . "_" . basename($file['name']);
    $targetDir = "../uploads/";
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        // Update profile picture in the database
        $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $fileName, $user_id);

        if ($stmt->execute()) {
            echo $targetFile; // Return new profile picture path
        } else {
            echo "Error updating profile picture.";
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}

$conn->close();
?>
