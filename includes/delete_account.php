<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['account_id'])) {
    $account_id = $_POST['account_id'];

    $query = "DELETE FROM accounts WHERE account_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $account_id);

    if ($stmt->execute()) {
        echo "success"; // This will trigger the success alert in jQuery
    } else {
        echo "Error deleting account.";
    }
}
?>
