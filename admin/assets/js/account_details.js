$(document).ready(function () {
    // Load account details and transactions
   // Function to get URL parameters
function getUrlParam(param) {
    let urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Get the account ID from the URL (change 'id' to 'account_id')
let accountId = getUrlParam("id"); // Ensure we fetch 'id'

if (!accountId) {
    alert("Error: Account ID is missing.");
} else {
    console.log("Account ID:", accountId); // Debugging
    fetchAccountDetails(accountId);
}
// Function to fetch account details
function fetchAccountDetails(accountId) {
    $.ajax({
        url: '../includes/account_details.php',
        type: 'GET',
        data: { account_id: accountId }, // Ensure we send 'account_id'
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                alert(response.error);
                return;
            }

            // Display account details
            $("#account-number").text(response.account.account_number);
            $("#account-balance").text("$" + parseFloat(response.account.balance).toFixed(2));

            // Clear existing table rows
            $("#usersTableBody").empty();

            // Populate transactions
            let transactionsHtml = "";
            response.transactions.forEach((transaction, index) => {
                // Determine status class
                let statusClass = "status-pill";
                if (transaction.status === "pending" || transaction.status === "Pending") {
                    statusClass += " status-warning";
                } else if (transaction.status === "completed"||transaction.status === "Completed") {
                    statusClass += " status-success";
                } else if (transaction.status === "Reversed" ||transaction.status === "reversed") {
                    statusClass += " status-error";
                }
                transactionsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${transaction.transaction_date}</td>
                        <td>${transaction.description}</td>
                        <td>$${parseFloat(transaction.amount).toFixed(2)}</td>
                        <td style="text-transform:capitalize;">${transaction.transaction_type}</td>
                        <td class="${statusClass}">${transaction.status}</td>
                        <td>
                            <button class="button-primary" title="Manage">
                                <a href="transaction_details.html?transaction_id=${transaction.transaction_id}">
                                    <img src="assets/images/icons/edit.png">
                                </a>
                            </button>
                        </td>
                    </tr>
                `;
            });

            // Insert into table
            $("#usersTableBody").html(transactionsHtml);
        },
        error: function () {
            alert("Error fetching account details.");
        }
    });
}

    // Handle deposit and withdrawal
  // Handle form submission for deposit/withdrawal
$('#fundsForm').submit(function (e) {
    e.preventDefault();
    
    let amount = $('#fundsAmount').val();
    let type = $('#fundsModalTitle').text().toLowerCase().includes('deposit') ? 'deposit' : 'withdraw';

// Ensure the value is always 'deposit' or 'withdraw'
    if (!['deposit', 'withdraw'].includes(type)) {
        console.error("Invalid transaction type detected:", type);
        type = 'withdraw'; // Default fallback
    }
    console.log("Final Transaction Type:", type);

    
    console.log("Submitting transaction:", { account_id: accountId, amount: amount, type: type });

    $.ajax({
        url: '../includes/funds_action.php',
        method: 'POST',
        data: { 
            amount: amount, 
            type: type, 
            account_id: accountId // Ensure account_id is passed as POST
        },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                alert("Error: " + data.error);
            } else {
                alert("Success: " + data.success);
                $('#account-balance').text("$" + data.new_balance);
                closeFundsModal();
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("An error occurred while processing the request.");
        }
    });
});


});
