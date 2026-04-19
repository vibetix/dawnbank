$(document).ready(function () {
    function loadTransactions() {
        $.ajax({
            url: "../includes/get_transactions.php",
            type: "GET",
            dataType: "json",
            success: function (transactions) {
                let tableBody = $("#transactionsTableBody");
                tableBody.empty(); // Clear existing rows

                if (transactions.length > 0) {
                    transactions.forEach((transaction) => {
                        // Determine status class
                        let statusClass = "status-pill";
                        if (transaction.status.toLowerCase() === "pending") {
                            statusClass += " status-warning";
                        } else if (transaction.status.toLowerCase() === "completed") {
                            statusClass += " status-success";
                        } else if (transaction.status.toLowerCase() === "reversed") {
                            statusClass += " status-error";
                        }

                        let row = `
                            <tr>
                                <td>#${transaction.id}</td>
                                <td>${transaction.user}</td>
                                <td>$${transaction.amount}</td>
                                <td class="${statusClass}">${transaction.status}</td>
                                <td>${transaction.type}</td>
                                <td>${transaction.date}</td>
                                <td>
                                    <button class="view-btn" data-id="${transaction.id}">
                                    <a href="transaction_details.html?transaction_id=${transaction.id}" style="text-decoration:none; color:black;">View
                                    </a></button>
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append(`<tr><td colspan="7">No transactions found.</td></tr>`);
                }
            },
            error: function () {
                console.log("Error fetching transactions.");
            }
        });
    }

    loadTransactions(); // Load transactions on page load
});
