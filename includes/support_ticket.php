<?php
require 'connect.php'; // Ensure database connection is included

header("Content-Type: application/json");

// Query the database for users with a pending status
$query = "
        SELECT 
            s.ticket_id,
            u.first_name,
            u.last_name,
            s.subject,
            s.status,
            s.created_at
        FROM supporttickets s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC
        ";
$result = $conn->query($query);


$supporttickets = [];
while ($row = $result->fetch_assoc()) {
    $row['full_name'] = $row['first_name'] . " " . $row['last_name']; // Combine first and last name
    $supporttickets[] = $row;
}

// Ensure we return a valid JSON
echo json_encode(["supporttickets" => $supporttickets], JSON_PRETTY_PRINT);
?>
