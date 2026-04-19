<?php
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "connect.php";

// ==== Clean Buffer Function ====
function cleanOutput() {
    while (ob_get_level()) {
        ob_end_clean();
    }
}

// ==== JSON Output Helper ====
function jsonResponse($data) {
    cleanOutput();
    echo json_encode($data);
    exit;
}

// ==== Generate Transaction ID ====
function generateTransactionId($length = 9) {
    return "Txn" . strtoupper(bin2hex(random_bytes($length / 2)));
}

// ==== Check request method ====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["status" => "error", "message" => "Invalid request method."]);
}

// ==== Check Authentication ====
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    jsonResponse(["status" => "error", "message" => "User not authenticated."]);
}

// ==== Sanitize inputs ====
$fromAccount = $_POST['fromAccount'] ?? '';
$transferType = $_POST['transferType'] ?? '';
$amount = floatval($_POST['transferAmount'] ?? 0);

// ==== Validate inputs ====
if ($amount <= 0) {
    jsonResponse(["status" => "error", "message" => "Invalid transfer amount."]);
}

// ==== Check sender account ====
$stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ? AND user_id = ?");
$stmt->bind_param("ii", $fromAccount, $userId);
$stmt->execute();
$stmt->bind_result($sender_balance);
$stmt->fetch();
$stmt->close();

if ($sender_balance === null) {
    jsonResponse(["status" => "error", "message" => "Sender account not found."]);
}

if ($sender_balance < $amount) {
    jsonResponse(["status" => "error", "message" => "Insufficient funds."]);
}

// ==== Begin transaction ====
$conn->begin_transaction();

try {

    if ($transferType === "internal") {
        $toAccount = $_POST['toAccount'] ?? '';

        // ==== Check receiver account ====
        $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ?");
        $stmt->bind_param("i", $toAccount);
        $stmt->execute();
        $stmt->bind_result($receiver_balance);
        $stmt->fetch();
        $stmt->close();

        if ($receiver_balance === null) {
            jsonResponse(["status" => "error", "message" => "Recipient account not found."]);
        }

        // ==== Update balances ====
        $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
        $stmt->bind_param("di", $amount, $fromAccount);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
        $stmt->bind_param("di", $amount, $toAccount);
        $stmt->execute();
        $stmt->close();

        $status = "completed";

    } elseif ($transferType === "user") {

        $receiverEmail = $_POST['receiverEmail'] ?? '';

        // ==== Get receiver user ====
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $receiverEmail);
        $stmt->execute();
        $stmt->bind_result($receiverUserId);
        $stmt->fetch();
        $stmt->close();

        if (!$receiverUserId) {
            jsonResponse(["status" => "error", "message" => "No user found with this email."]);
        }

        // ==== Get receiver account ====
        $stmt = $conn->prepare("SELECT account_id FROM accounts WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $receiverUserId);
        $stmt->execute();
        $stmt->bind_result($receiverAccountId);
        $stmt->fetch();
        $stmt->close();

        if (!$receiverAccountId) {
            jsonResponse(["status" => "error", "message" => "User has no account in our system."]);
        }

        // ==== Update balances ====
        $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
        $stmt->bind_param("di", $amount, $fromAccount);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
        $stmt->bind_param("di", $amount, $receiverAccountId);
        $stmt->execute();
        $stmt->close();

        $status = "completed";

    } elseif ($transferType === "external") {

        $bankName = $_POST['bankName'] ?? '';
        $externalAccount = $_POST['externalAccount'] ?? '';
        $swiftCode = $_POST['swiftCode'] ?? '';

        if (empty($bankName) || empty($externalAccount) || empty($swiftCode)) {
            jsonResponse(["status" => "error", "message" => "External transfer details missing."]);
        }

        // ==== External only deduct ====
        $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
        $stmt->bind_param("di", $amount, $fromAccount);
        $stmt->execute();
        $stmt->close();

        $status = "pending";

    } else {
        jsonResponse(["status" => "error", "message" => "Invalid transfer type."]);
    }

    // ==== Record transaction ====
    $transactionId = generateTransactionId();
    $transactionType = "transfer";
    $description = ucfirst($transferType) . " transfer of $$amount ";

    $stmt = $conn->prepare("INSERT INTO transactions (transaction_id, account_id, transaction_type, amount, transaction_date, description, status)
                            VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("sisdss", $transactionId, $fromAccount, $transactionType, $amount, $description, $status);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // ==== Success response ====
    jsonResponse([
        "status" => "success",
        "message" => "Transfer successful!",
        "transferType" => $transferType,
        "transactionId" => $transactionId
    ]);

} catch (Exception $e) {
    $conn->rollback();
    jsonResponse(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
}

?>
