<?php
header('Content-Type: application/json');

require 'connect.php'; // Include database connection
// Determine which chart data to fetch
$chartType = isset($_GET['chart']) ? $_GET['chart'] : '';

if ($chartType == 'user_growth') {
    // Fetch user growth data from the database
    $query = "SELECT MONTHNAME(created_at) AS month, COUNT(id) AS user_count FROM users 
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY MONTH(created_at) ORDER BY created_at";
    $result = $conn->query($query);

    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['month'];
        $data[] = $row['user_count'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);
}

elseif ($chartType == 'revenue_breakdown') {
    // Fetch total transaction amount
    $totalQuery = "SELECT SUM(amount) AS grand_total FROM transactions 
                   WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
    $totalResult = $conn->query($totalQuery);
    $totalRow = $totalResult->fetch_assoc();
    $grandTotal = $totalRow['grand_total'];

    if ($grandTotal == 0 || !$grandTotal) {
        echo json_encode(['error' => 'No transaction data available']);
        exit;
    }

    // Fetch transaction breakdown data
    $query = "SELECT transaction_type, SUM(amount) AS total FROM transactions 
              WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY transaction_type";
    $result = $conn->query($query);

    $labels = [];
    $data = [];
    $formattedData = [];

    while ($row = $result->fetch_assoc()) {
        $transactionType = ucfirst($row['transaction_type']);
        $percentage = ($row['total'] / $grandTotal) * 100;
        
        $labels[] = $transactionType;
        $data[] = round($percentage, 2); // Store percentage values
        
        // Format for display (e.g., "Deposits: 45%")
        $formattedData[] = [
            'label' => $transactionType,
            'percentage' => round($percentage, 2) . '%',
            'amount' => '$' . number_format($row['total'], 2) // Format total in dollars
        ];
    }

    echo json_encode([
        'labels' => $labels, 
        'data' => $data, 
        'total_transactions' => '$' . number_format($grandTotal, 2), // Total amount in $
        'formatted_data' => $formattedData // Data with percentages & $ amounts
    ]);
}

else {
    echo json_encode(['error' => 'Invalid chart request']);
}

$conn->close();
?>
