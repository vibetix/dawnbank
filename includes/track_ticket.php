<?php
require_once "connect.php"; // Ensure database connection is included

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ticket_id = $_POST["ticket_id"] ?? null;

    if (!$ticket_id) {
        echo json_encode(["status" => "error", "message" => "Ticket ID is required."]);
        exit;
    }

    // Query to fetch ticket status
    $query = $conn->prepare("SELECT subject, status, created_at FROM SupportTickets WHERE ticket_id = ?");
    
    if (!$query) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit;
    }

    $query->bind_param("i", $ticket_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "ticket" => $ticket
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Ticket not found."]);
    }

    $query->close();
}
?>
