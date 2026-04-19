<?php
require_once 'connect.php'; // Ensure DB connection

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $investment_amount = isset($_POST["investmentAmount"]) ? floatval($_POST["investmentAmount"]) : 0;
    $investment_type = isset($_POST["investmentType"]) ? $_POST["investmentType"] : "";
    $months = isset($_POST["months"]) ? intval($_POST["months"]) : 0;

    // Define return rates for investment types
    $return_rates = [
        "stocks" => 8.00,        // 8% per month
        "bonds" => 5.00,         // 5% per month
        "mutual-funds" => 6.00   // 6% per month
    ];

    // Validate input
    if ($investment_amount <= 0 || empty($investment_type) || !isset($return_rates[$investment_type]) || $months <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
        exit;
    }

    // Get the return rate for the investment type
    $return_rate = $return_rates[$investment_type] / 100; // Convert percentage to decimal

    // Apply compound interest formula: A = P(1 + r)^n
    $final_value = $investment_amount * pow((1 + $return_rate), $months);

    echo json_encode([
        "status" => "success",
        "initial_amount" => $investment_amount,
        "final_value" => number_format($final_value, 2),
        "months" => $months,
        "investment_type" => ucfirst(str_replace("-", " ", $investment_type))
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
