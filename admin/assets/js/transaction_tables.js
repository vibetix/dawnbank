function fetchTransactions() {
    $.ajax({
        url: "../includes/get_transaction.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log("API Response:", response); // Debugging

            if (!response || !Array.isArray(response.transactions) || response.transactions.length === 0) {
                $("#transactionsTableBody").html("<tr><td colspan='6' class='text-center'>No transactions available.</td></tr>");
                return;
            }

            let transactionsHtml = response.transactions.map(transaction => {
                // Normalize status to lowercase for comparison
                let status = transaction.status ? transaction.status.toLowerCase() : "";

                // Assign status classes dynamically
                let statusClass = "status-pill";
                switch (status) {
                    case "pending": statusClass += " status-warning"; break;
                    case "completed": statusClass += " status-success"; break;
                    case "reversed": statusClass += " status-error"; break;
                    case "failed": statusClass += " status-failed"; break;
                }

                return `
                    <tr>
                        <td>${transaction.transaction_date}</td>
                        <td>${transaction.description}</td>
                        <td>$${parseFloat(transaction.amount).toFixed(2)}</td>
                        <td>${transaction.account_type}</td>
                        <td>${transaction.transaction_type}</td>
                        <td class="${statusClass}" style="text-align:left;">${transaction.status}</td>
                    </tr>
                `;
            }).join("");

            $("#transactionsTableBody").html(transactionsHtml);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching transactions:", status, error);
            alert("Error fetching transactions. Please try again.");
        }
    });
}

// Fetch transactions when the page loads
$(document).ready(fetchTransactions);
