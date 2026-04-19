$(document).ready(function () {
    function getUrlParam(param) {
        let urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    

    function fetchAccountDetails(accountId) {
    console.log("Fetching account details for Account ID:", accountId); // Debugging

    if (!accountId) {
        console.error("Error: accountId is missing before sending AJAX request.");
        alert("Error: Account ID is missing.");
        return;
    }

    $.ajax({
        url: "../includes/get_user_by_account.php",
        type: "POST",
        data: { account_id: accountId },
        success: function (response) {
            console.log("Raw Response:", response); // Debugging the raw response
            
            try {
                let user = JSON.parse(response);
                console.log("Parsed Response:", user); // Debugging parsed response
                
                if (user.error) {
                    alert("Error: " + user.error);
                    return;
                }

                if (!user.user_id) {
                    alert("Error: User ID not found for this account.");
                    return;
                }

                console.log("User ID:", user.user_id);
                console.log("Account Number:", user.account_number);
                console.log("Account Type:", user.account_type);
                console.log("Balance:", user.balance);

                // Store the user ID correctly
                $("#userId").val(user.user_id);

                // Populate 'fromAccount' select field
                let fromAccountOption = `<option value="${accountId}" selected>
                    ${user.account_number} (${user.account_type}) - Balance: $${user.balance}
                </option>`;

                $("#fromAccount").html(fromAccountOption);
            } catch (error) {
                console.error("Error parsing JSON:", error);
                alert("An error occurred while processing account details.");
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("Failed to fetch account details.");
        }
    });
}

// Get the account ID from URL
// Run this when the page loads to fetch account details
let accountId = getUrlParam("id"); // Get account ID from URL
if (!accountId) {
    alert("Error: Account ID is missing.");
} else {
    fetchAccountDetails(accountId); // Call this function first
}
 $(document).ready(function () {
        let transferType = "internal";
  
        $("#transferType").val(transferType);

        if (transferType === "internal") {
            let accountId = getUrlParam("id"); // Fetch from URL
            console.log("Selected Account ID:", accountId);

            if (!accountId) {
                alert("Error: Account ID is missing.");
                return;
            }

            fetchRecipientAccounts(accountId);
        }
    });

    function fetchRecipientAccounts(accountId) {
    console.log("Fetching recipient accounts for Account ID:", accountId);

    $.ajax({
        url: "../includes/fetch_internal_accounts.php",
        type: "POST",
        data: { account_id: accountId },
        dataType: "json", // ✅ This ensures automatic JSON parsing
        success: function (accounts) {
            console.log("Parsed Response:", accounts);

            if (!Array.isArray(accounts) || accounts.length === 0) {
                alert("No recipient accounts found.");
                return;
            }

            let options = '<option value="" disabled selected>Select recipient account</option>';
            accounts.forEach(account => {
                options += `<option value="${account.account_id}">
                    ${account.account_number} (${account.account_type}) - Balance: $${account.balance}
                </option>`;
            });

            $("#toAccount").html(options);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error, xhr.responseText);
            alert("Failed to fetch accounts. Please check your connection.");
        }
    });
}

   });
