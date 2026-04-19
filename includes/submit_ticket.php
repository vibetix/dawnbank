<?php
require_once "connect.php"; // Ensure database connection is included

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"] ?? null;
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (!$user_id || empty($subject) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // ✅ Insert the ticket into the SupportTickets table
    $insertTicket = $conn->prepare("INSERT INTO SupportTickets (user_id, subject, message, status, created_at) VALUES (?, ?, ?, 'OPEN', NOW())");

    if (!$insertTicket) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit;
    }

    $insertTicket->bind_param("iss", $user_id, $subject, $message);
    
    if ($insertTicket->execute()) {
        $ticket_id = $conn->insert_id; // ✅ Get the last inserted ticket ID
        echo json_encode(["status" => "success", "message" => "Ticket submitted successfully", "ticket_id" => $ticket_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to submit ticket"]);
    }
}
?>
