<?php
require 'connect.php'; // Include database connection

// Fetch total users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];

// Fetch total transactions
$totalTransactionsQuery = "SELECT SUM(amount) AS total_transactions FROM transactions";
$totalTransactionsResult = $conn->query($totalTransactionsQuery);
$totalTransactions = $totalTransactionsResult->fetch_assoc()['total_transactions'];

// Fetch loan requests
$loanRequestsQuery = "SELECT COUNT(*) AS loan_requests FROM loans WHERE status = 'pending'";
$loanRequestsResult = $conn->query($loanRequestsQuery);
$loanRequests = $loanRequestsResult->fetch_assoc()['loan_requests'];

// Fetch revenue
$revenueQuery = "SELECT SUM(amount) AS revenue FROM transactions WHERE transaction_type = 'credit'";
$revenueResult = $conn->query($revenueQuery);
$revenue = $revenueResult->fetch_assoc()['revenue'];

// Fetch total balance
$totalBalanceQuery = "SELECT SUM(balance) AS total_balance FROM accounts";
$totalBalanceResult = $conn->query($totalBalanceQuery);
$totalBalance = $totalBalanceResult->fetch_assoc()['total_balance'];

// Fetch total deposits
$totalDepositsQuery = "SELECT SUM(amount) AS total_deposits FROM transactions WHERE transaction_type = 'deposit'";
$totalDepositsResult = $conn->query($totalDepositsQuery);
$totalDeposits = $totalDepositsResult->fetch_assoc()['total_deposits'];

// Fetch total withdrawals
$totalWithdrawalsQuery = "SELECT SUM(amount) AS total_withdrawals FROM transactions WHERE transaction_type = 'withdrawal'";
$totalWithdrawalsResult = $conn->query($totalWithdrawalsQuery);
$totalWithdrawals = $totalWithdrawalsResult->fetch_assoc()['total_withdrawals'];

// Fetch total transfers
$totalTransfersQuery = "SELECT SUM(amount) AS total_transfers FROM transactions WHERE transaction_type = 'transfer'";
$totalTransfersResult = $conn->query($totalTransfersQuery);
$totalTransfers = $totalTransfersResult->fetch_assoc()['total_transfers'];

// Prepare JSON response
$response = [
    "total_users" => number_format($totalUsers),
    "total_transactions" => "$" . number_format($totalTransactions, 2),
    "loan_requests" => number_format($loanRequests),
    "revenue" => "$" . number_format($revenue, 2),
    "total_balance" => "$" . number_format($totalBalance, 2),
    "total_deposits" => "$" . number_format($totalDeposits, 2),
    "total_withdrawals" => "$" . number_format($totalWithdrawals, 2),
    "total_transfers" => "$" . number_format($totalTransfers, 2)
];

echo json_encode($response);
$conn->close();
?>
