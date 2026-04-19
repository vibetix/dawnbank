<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $system_id = $_POST["system_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];

    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        $image = "uploads/" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    } else {
        $image = $_POST["current_image"];
    }

    // Prepare update query
    $sql = "UPDATE system_managment SET name=?, email=?, contact=?, address=?, image=? WHERE system_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $contact, $address, $image, $system_id);
    $stmt->execute();

    echo json_encode(["success" => true]);
}
?>
