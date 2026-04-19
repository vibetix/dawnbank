$(document).ready(function () {
function fetchAccounts(search = '', sortColumn = 'account_id', sortOrder = 'ASC') {
    $.ajax({
        url: '../includes/fetch_accounts_list.php',
        type: 'GET',
        data: {
            search: search,
            sortColumn: sortColumn,
            sortOrder: sortOrder
        },
        dataType: 'json',
        success: function (data) {
            let tableBody = $('#accountsTableBody');
            tableBody.empty(); // Clear the table before adding new data

            if (data.length === 0) {
                tableBody.append('<tr><td colspan="9">No records found</td></tr>');
                return;
            }

            data.forEach((account, index) => {
                let accountId = account.account_id; // Ensure account_id is passed correctly

                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${account.client_id}</td>
                    <td>${account.full_name}</td>
                    <td>${account.email}</td>
                    <td>${account.account_type}</td>
                    <td>${account.account_number}</td>
                    <td>$${parseFloat(account.balance).toFixed(2)}</td>
                    <td>
                        <button class="button-primary manage-account" data-id="${accountId}" title="Manage">
                            <img src="assets/images/icons/edit.png">
                        </button>
                        <button class="button-danger delete-account" data-id="${accountId}" title="Delete">
                            <img src="assets/images/icons/trash.png">
                        </button>
                    </td>
                </tr>`;
                tableBody.append(row);
            });

            // Add event listener to manage account button
            $(".manage-account").on("click", function () {
                let accountId = $(this).data("id");
                window.location.href = `account_details.html?id=${accountId}`;
            });
        },
        error: function () {
            alert('Error loading account data.');
        }
    });
}


// Handle Delete Button Click (Delegated Event Binding)
$(document).on('click', '.delete-account', function () {
    let accountId = $(this).data('id');
    if (confirm('Are you sure you want to delete this account?')) {
        $.ajax({
            url: '../includes/delete_account.php',
            type: 'POST',
            data: { account_id: accountId },
            success: function (response) {
                alert('Account deleted successfully.');
                fetchAccounts(); // Refresh the account list
            },
            error: function () {
                alert('Error deleting account.');
            }
        });
    }
});

    // Initial fetch
    fetchAccounts();

    // Search functionality
    $('.search-input').on('input', function () {
        let searchQuery = $(this).val();
        fetchAccounts(searchQuery);
    });

    // Sorting functionality
    $('.sort-button').on('click', function () {
        let sortColumn = $(this).data('sort');
        let currentOrder = $(this).attr('data-order') || 'ASC';
        let newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
        $(this).attr('data-order', newOrder);

        fetchAccounts($('.search-input').val(), sortColumn, newOrder);
    });

    // // Delete account functionality with success message alert
    // $(document).on('click', '.delete-account', function () {
    //     let accountId = $(this).data('id');

    //     if (confirm('Are you sure you want to delete this account?')) {
    //         $.ajax({
    //             url: '../includes/delete_account.php',
    //             type: 'POST',
    //             data: { account_id: accountId },
    //             success: function (response) {
    //                 if (response.trim() === 'success') {
    //                     alert('Account deleted successfully!');
    //                     fetchAccounts(); // Refresh data
    //                 } else {
    //                     alert('Error: ' + response);
    //                 }
    //             },
    //             error: function () {
    //                 alert('Error deleting account.');
    //             }
    //         });
    //     }
    // });
});
