<?php
session_start();
include("connect.php"); // Ensure your database connection file is correct

$response = [];

$sql = "SELECT * FROM system_managment";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$response["row"] = $result->fetch_assoc();

echo json_encode($response);
?>
