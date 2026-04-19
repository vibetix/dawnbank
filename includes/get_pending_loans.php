<?php
include 'connect.php'; // Your DB connection

$sql = "SELECT loans.loan_id, users.first_name, users.last_name, loans.loan_amount, loans.loan_purpose, loans.status 
        FROM loans 
        INNER JOIN users ON loans.user_id = users.id 
        WHERE loans.status = 'pending' 
        ORDER BY loans.created_at DESC";

$result = $conn->query($sql);
$loans = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
         $row['full_name'] = $row['first_name'] . " " . $row['last_name']; // Combine first and last name
        $loans[] = $row;

    }
}

echo json_encode($loans);
?>
