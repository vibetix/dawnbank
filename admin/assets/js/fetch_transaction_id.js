$(document).ready(function () {
       function getUrlParam(param) {
    let urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Get the Transaction ID from the URL
let transactionId = getUrlParam("transaction_id");

if (!transactionId) {
    alert("Error: Transaction ID is missing.");
} else {
    console.log("Transaction ID:", transactionId); // Debugging
    fetchTransactionDetails(transactionId);
}

// Function to fetch transaction details and update the HTML
function fetchTransactionDetails(transactionId) {
    $.ajax({
        url: '../includes/transaction_details.php',
        type: 'GET',
        data: { transaction_id: transactionId }, // Ensure transaction_id is sent
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                alert(response.error);
                return;
            }

            // Populate HTML elements with transaction details
            $("#transaction_type").text(response.transaction.transaction_type);
            $("#amount").text("$" + parseFloat(response.transaction.amount).toFixed(2));
            $("#transaction").text(response.transaction.transaction_id);
            $("#status").text(response.transaction.status);
            $("#description").text(response.transaction.description);
        },
        error: function () {
            alert("Error fetching transaction details.");
        }
    });
}

// Function to handle transaction reversal
$(".export-button").on("click", function () {
    let transactionId = getUrlParam("transaction_id");
    console.log("Transaction ID being sent:", transactionId); // 🔎 Debugging

    if (!transactionId) {
        alert("Error: Transaction ID is missing.");
        return;
    }

    if (confirm("Are you sure you want to reverse this transaction? The amount will be re-transferred to the original account.")) {
        $.ajax({
            url: "../includes/reverse_transaction.php",
            type: "POST",
            data: { transaction_id: transactionId },
            dataType: "json",
            success: function (response) {
                console.log("Server Response:", response); // 🔎 Debugging

                if (response && response.success) {
                    alert(response.message);
                   
                } else {
                    alert("Error: " + (response.message || "Unexpected response"));
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert("AJAX Error: " + error);
            }
        });
    }
});






// // Attach event listener to the "Make Reversal" button
// $(document).ready(function () {
//     $(".export-button").on("click", function () {
//         reverseTransaction(transactionId);
//     });
// });

});
