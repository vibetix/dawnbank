<?php
session_start();
include 'connect.php'; // Ensure you have a database connection file

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT username FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Assign "Admin" as default role
    $user['role'] = "Admin";

    // Assign default avatar
    $user['avatar'] = "https://github.com/shadcn.png";

    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>
