<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get updated data from AJAX request
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$address = $_POST['address'];
$city = $_POST['city'];

$sql = "UPDATE users SET first_name = ?, last_name = ?, address = ?, city = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $first_name, $last_name, $address, $city, $user_id);

if ($stmt->execute()) {
    echo "Profile updated successfully!";
} else {
    echo "Error updating profile.";
}

$stmt->close();
$conn->close();
?>
