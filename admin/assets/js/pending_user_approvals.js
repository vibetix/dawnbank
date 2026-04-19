$(document).ready(function () {
    function loadPendingUsers() {
        $.ajax({
            url: "../includes/get_pending_users.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                let tableBody = $("#approvalTableBody");
                tableBody.empty(); // Clear existing data

                if (response.length > 0) {
                    response.forEach((user, index) => {
                        let row = `
                            <tr data-id="${user.id}">
                                <td>${index + 1}</td>
                                <td>${user.id}</td>
                                <td>${user.full_name}</td>
                                <td class="status-pill status-warning">${user.status}</td>
                                <td>${user.created_at}</td>
                                <td>
                                    <button class="button-primary approve-btn" data-id="${user.id}" title="Approve">
                                        <img src="assets/images/icons/approve.png">
                                    </button>
                                    <button class="button-danger decline-btn" data-id="${user.id}" title="Decline">
                                        <img src="assets/images/icons/cross.png">
                                    </button>
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append(`<tr><td colspan="6">No pending approvals.</td></tr>`);
                }
            },
            error: function () {
                console.log("Error fetching pending users.");
            }
        });
    }

    loadPendingUsers(); // Load data on page load

    // Approve User
    $(document).on("click", ".approve-btn", function () {
        let userId = $(this).data("id");

        if (confirm("Are you sure you want to approve this user?")) {
            $.ajax({
                url: "../includes/approve_user.php",
                type: "POST",
                data: { user_id: userId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        alert("User approved! Client ID: " + response.client_id);
                        loadPendingUsers(); // Reload the table
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function () {
                    alert("Failed to approve user.");
                }
            });
        }
    });

    // Decline User
    $(document).on("click", ".decline-btn", function () {
        let userId = $(this).data("id");

        if (confirm("Are you sure you want to decline this user?")) {
            $.ajax({
                url: "../includes/delete_user.php",
                type: "POST",
                data: { user_id: userId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        alert("User declined and removed.");
                        loadPendingUsers(); // Reload the table
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function () {
                    alert("Failed to decline user.");
                }
            });
        }
    });
});
