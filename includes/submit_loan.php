<?php
include "connect.php"; // Ensure database connection
session_start();

header("Content-Type: application/json");

try {
    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    // Ensure user is authenticated
    if (!isset($_SESSION["user_id"])) {
        throw new Exception("User not authenticated.");
    }

    $user_id = $_SESSION["user_id"];

    // Retrieve and sanitize input values
    $loan_amount = filter_input(INPUT_POST, "loanAmount", FILTER_VALIDATE_FLOAT);
    $loan_term = filter_input(INPUT_POST, "loanTerm", FILTER_VALIDATE_INT);
    $loan_purpose = trim(htmlspecialchars($_POST["loanPurpose"] ?? '', ENT_QUOTES, 'UTF-8'));
    $interest_rate = filter_input(INPUT_POST, "interestRate", FILTER_VALIDATE_FLOAT);
    $total_repayment = filter_input(INPUT_POST, "totalRepayment", FILTER_VALIDATE_FLOAT);

    // Validate required fields
    if (!$loan_amount || $loan_amount <= 0 || !$loan_term || $loan_term <= 0 || empty($loan_purpose) || !$interest_rate || $interest_rate <= 0 || !$total_repayment || $total_repayment <= 0) {
        throw new Exception("All fields are required and must contain valid values.");
    }

    // Set initial remaining balance equal to total repayment amount
    $remaining_balance = $total_repayment;

    // Begin MySQL transaction
    $conn->begin_transaction();

    // Insert loan details into Loans table (Loan is PENDING)
    $stmt = $conn->prepare("
        INSERT INTO Loans (user_id, loan_amount, remaining_balance, interest_rate, loan_term, loan_purpose, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'PENDING')
    ");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    $stmt->bind_param("idddis", $user_id, $loan_amount, $remaining_balance, $interest_rate, $loan_term, $loan_purpose);
    $stmt->execute();

    // Check if the loan insertion was successful
    if ($stmt->affected_rows <= 0) {
        throw new Exception("Loan application submission failed.");
    }

    // Get the last inserted loan ID
    $loan_id = $stmt->insert_id;
    $stmt->close();

    // 🚨 REMOVED TRANSACTION INSERTION!  
    // Transactions should only be recorded AFTER admin approval.

    // Commit transaction
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Loan application submitted successfully and is pending approval."]);

} catch (Exception $e) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} finally {
    // Close database connection
    $conn->close();
}
?>
