<?php
require 'connect.php'; // Ensure database connection is included

header("Content-Type: application/json");

// Query the database for users with a pending status
$stmt = $conn->prepare("SELECT id,client_id, first_name, last_name, email, id_type, id_number, address FROM users WHERE status = 'approved'");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $row['full_name'] = $row['first_name'] . " " . $row['last_name']; // Combine first and last name
    $users[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($users);
?>
