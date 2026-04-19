<?php
include 'connect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'account_id';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

// Prevent SQL injection by allowing only specific columns for sorting
$allowedColumns = ['account_id', 'account_name', 'account_type', 'account_number', 'balance', 'full_name', 'email', 'phone', 'client_id'];
if (!in_array($sortColumn, $allowedColumns)) {
    $sortColumn = 'account_id'; // Default sorting column
}

// Ensure sort order is either ASC or DESC
$sortOrder = ($sortOrder === 'DESC') ? 'DESC' : 'ASC';

// Prepare SQL query to join accounts and users tables
$query = "SELECT 
            a.account_id, a.account_name, a.account_type, a.account_number, a.balance, 
            CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.client_id, u.email, u.phone 
          FROM accounts a
          JOIN users u ON a.user_id = u.id
          WHERE a.account_name LIKE ? OR a.account_number LIKE ? OR u.client_id LIKE ? OR
                CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ?
          ORDER BY $sortColumn $sortOrder";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error); // Debugging output
}

$searchParam = "%$search%";
$stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam); // Binding 5 parameters

$stmt->execute();
$result = $stmt->get_result();

$accounts = [];

while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

echo json_encode($accounts);
?>
