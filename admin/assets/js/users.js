$(document).ready(function () {
    loadPendingUsers();
  function loadPendingUsers() {
    $.ajax({
        url: "../includes/get_approved_users.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            let tableBody = $("#usersTableBody");
            tableBody.empty(); // Clear existing data

            if (response.length > 0) {
                response.forEach((user, index) => {
                    let row = `
                        <tr data-id="${user.id}">
                            <td>${index + 1}</td>
                            <td>${user.client_id}</td>
                            <td>${user.full_name}</td>
                            <td>${user.email}</td>
                            <td>${user.id_type}</td>
                            <td>${user.id_number}</td>
                            <td>${user.address}</td>
                            <td>
                                <button class="button-primary manage-user" data-id="${user.id}" title="Manage">
                                    <img src="assets/images/icons/edit.png">
                                </button>
                                <button class="button-danger decline-btn" data-id="${user.id}" title="Delete">
                                    <img src="assets/images/icons/trash.png">
                                </button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });

            } else {
                tableBody.append(`<tr><td colspan="8">No pending approvals.</td></tr>`);
            }
        },
        error: function () {
            console.log("Error fetching pending users.");
        }
    });
}

// Event Delegation for dynamically added elements
$(document).on("click", ".manage-user", function () {
    let userId = $(this).data("id");
    window.location.href = `edit-user.html?id=${userId}`;
});

$(document).on("click", ".decline-btn", function () {
    let userId = $(this).data("id");
    if (confirm("Are you sure you want to delete this user?")) {
        deleteUser(userId);
    }
});

// Function to delete a user
function deleteUser(userId) {
    $.ajax({
        url: "../includes/delete_user.php",
        type: "POST",
        data: { id: userId },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                loadPendingUsers(); // Reload the list after deletion
            } else {
                console.error("Failed to delete user:", response.error);
            }
        },
        error: function () {
            console.error("Error deleting user.");
        }
    });
}

});
