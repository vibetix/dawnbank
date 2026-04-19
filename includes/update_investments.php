<?php
require_once "connect.php"; // Database connection

header("Content-Type: application/json");

try {
    // Ensure the connection is working
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // Get active investments
    $query = "SELECT id, initial_value, current_value, return_rate, created_at FROM investments WHERE status = 'active'";
    $stmt = $conn->prepare($query);

    // Check if statement preparation failed
    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Query Execution Failed: " . $conn->error);
    }

    $investments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($investments as $investment) {
        $investment_id = $investment["id"];
        $current_value = $investment["current_value"];
        $return_rate = $investment["return_rate"];

        // Calculate new value (compounding interest per minute for testing)
        $new_value = $current_value * (1 + ($return_rate / (12 * 30 * 24 * 60))); 

        // Update investment value in database
        $stmt = $conn->prepare("UPDATE investments SET current_value = ? WHERE id = ?");
        
        if (!$stmt) {
            throw new Exception("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("di", $new_value, $investment_id);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(["status" => "success", "message" => "Investments updated successfully"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>
