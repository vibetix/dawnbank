function fetchTickets() {
    $.ajax({
        url: "../includes/support_ticket.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log("API Response:", response); // Debugging

            if (!response || !Array.isArray(response.supporttickets) || response.supporttickets.length === 0) {
                $("#supportticketsTableBody").html("<tr><td colspan='6' class='text-center'>No support tickets available.</td></tr>");
                return;
            }

            let transactionsHtml = response.supporttickets.map(supportticket => {
                // Normalize status to lowercase for comparison
                let status = supportticket.status ? supportticket.status.toLowerCase() : "";

                // Assign status classes dynamically
                let statusClass = "status-pill";
                switch (status) {
                    case "pending": statusClass += " status-warning"; break;
                    case "open": statusClass += " status-success"; break;
                    case "close": statusClass += " status-error"; break;
                    case "failed": statusClass += " status-failed"; break;
                }

                return `
                    <tr>
                        <td>${supportticket.ticket_id}</td>
                        <td>${supportticket.full_name}</td>
                        <td>${supportticket.subject}</td>
                        <td class="${statusClass}" style="text-align:left;">${supportticket.status}</td>
                        <td>${supportticket.created_at}</td>
                        <td>
                            <button class="button-primary approve-btn" data-id="${supportticket.ticket_id}" title="Manage">
                                <img src="assets/images/icons/approve.png">
                            </button>
                            <button class="button-danger decline-btn" data-id="${supportticket.ticket_id}" title="Delete">
                                <img src="assets/images/icons/cross.png">
                            </button>
                        </td>
                    </tr>
                `;
            }).join("");

            $("#supportticketsTableBody").html(transactionsHtml);
        },
        error: function (xhr, status, error) {
            console.error("Error fetching support_tickets:", status, error);
            alert("Error fetching support_tickets. Please try again.");
        }
    });
}

// Fetch transactions when the page loads
$(document).ready(fetchTickets);