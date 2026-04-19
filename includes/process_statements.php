<?php
session_start();
require_once "connect.php";
require_once "fpdf/fpdf.php"; // Ensure this path is correct
header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

// Handle PDF generation request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accountId = $_POST['statementAccount'] ?? 0;
    $monthYear = $_POST['statementMonth'] ?? "";

    if (!$accountId || !$monthYear) {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
        exit();
    }

    // Validate month format (YYYY-MM)
    if (!preg_match("/^\d{4}-\d{2}$/", $monthYear)) {
        echo json_encode(["status" => "error", "message" => "Invalid date format."]);
        exit();
    }

    // Fetch transactions
    $stmt = $conn->prepare("
        SELECT transaction_date, description, amount, transaction_type 
        FROM Transactions
        WHERE account_id = ? 
        AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
        ORDER BY transaction_date DESC
    ");
    $stmt->bind_param("is", $accountId, $monthYear);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "No transactions found for this period."]);
        exit();
    }

    // Ensure the "statements" folder exists
    $folderPath = __DIR__ . "/statements/";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont("Arial", "B", 16);
    $pdf->Cell(190, 10, "Account Statement - $monthYear", 0, 1, "C");
    $pdf->Ln(10);

    // Table Headers
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Cell(40, 10, "Date", 1);
    $pdf->Cell(80, 10, "Description", 1);
    $pdf->Cell(30, 10, "Amount", 1);
    $pdf->Cell(40, 10, "Type", 1);
    $pdf->Ln();

    // Table Data
    $pdf->SetFont("Arial", "", 12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row["transaction_date"], 1);
        $pdf->Cell(80, 10, $row["description"], 1);
        $pdf->Cell(30, 10, "$" . number_format($row["amount"], 2), 1);
        $pdf->Cell(40, 10, ucfirst(strtolower($row["transaction_type"])), 1);
        $pdf->Ln();
    }

    // Save PDF file
    $pdfFileName = "statement_" . $accountId . "_" . str_replace("-", "", $monthYear) . ".pdf";
    $pdfFilePath = $folderPath . $pdfFileName;

    $pdf->Output("F", $pdfFilePath); // Save file to server

    // Return JSON response with the PDF URL
    echo json_encode(["status" => "success", "pdf" => "statements/" . $pdfFileName]);
    exit();
}
?>
