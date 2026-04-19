<?php
include 'connect.php'; // Adjust the path if necessary

header('Content-Type: application/json');

if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Fetch transaction details
    $sql = "SELECT transaction_id, status, transaction_type, amount, description FROM transactions WHERE transaction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        echo json_encode(["transaction" => $transaction]);
    } else {
        echo json_encode(["error" => "Transaction not found."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Transaction ID is required."]);
}
?>
