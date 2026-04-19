$(document).ready(function () {
    // Update User Account Settings
    $("#update-user").click(function () {
         let firstName = $("#first-name").val();
        let lastName = $("#last-name").val();
        let email = $("#email").val();
        let password = $("#password").val();

        $.ajax({
            url: "../includes/update_users.php",
            type: "POST",
            data: { first_name: firstName, last_name: lastName, email, password },
            dataType: "json",
            success: function (response) {
                $("#user-message").text(response.message).css("color", response.success ? "green" : "red");
            },
            error: function () {
                $("#user-message").text("Error updating user account.").css("color", "red");
            }
        });
    });

    // Update Bank Account Settings
    $("#update-account").click(function () {
        let accountNumber = $("#account-number").val();
        let accountType = $("#account-type").val();
        let status = $("#status").val();

        $.ajax({
            url: "../includes/update_bank.php",
            type: "POST",
            data: { accountNumber, accountType, status },
            dataType: "json",
            success: function (response) {
                $("#account-message").text(response.message).css("color", response.success ? "green" : "red");
            },
            error: function () {
                $("#account-message").text("Error updating account status.").css("color", "red");
            }
        });
    });
});