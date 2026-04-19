<?php
require_once "connect.php"; // Database connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $loanId = $_POST['loan_id'] ?? null;

    if (!$loanId) {
        echo json_encode(["status" => "error", "message" => "Loan ID is required"]);
        exit;
    }
    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM loans WHERE loan_id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $loanId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete loan"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
?>
