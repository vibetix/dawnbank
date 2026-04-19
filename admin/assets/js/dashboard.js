$(document).ready(function () {
    $.ajax({
        url: "../includes/get_user_name.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.first_name) {
                $(".welcome-title").text("Welcome, " + response.first_name + "!");
            } else {
                console.log(response.error || "Error retrieving user name.");
            }
        },
        error: function () {
            console.log("Error fetching user name.");
        }
    });
});
