<?php
session_start();
include 'connect.php'; // Include your database connection file

header('Content-Type: application/json');

$response = ["loggedIn" => false];

if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];

    // Prepare the SQL query to fetch user details
    $query = "SELECT first_name, last_name, status FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $response = [
            "loggedIn" => true,
            "firstName" => $row['first_name'],
            "lastName" => $row['last_name'],
            "status" => $row['status'] // Expected values: "Approved" or "Pending"
        ];
    }

    $stmt->close();
}

// Return the JSON response
echo json_encode($response);
?>
