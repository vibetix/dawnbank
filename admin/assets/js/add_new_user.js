$(document).ready(function () {
    $(".signup-form").on("submit", function (e) {
        e.preventDefault(); // Stop default form submission

        let firstName = $("#firstName").val().trim();
        let lastName = $("#lastName").val().trim();
        let email = $("#email").val().trim();
        let password = $("#password").val().trim();

        if (firstName === "" || lastName === "" || email === "" || password === "") {
            alert("All fields are required!");
            return;
        }

        $("#submitBtn").prop("disabled", true).text("Processing...");

        // Send data using AJAX
        $.ajax({
            type: "POST",
            url: "../includes/add_new_user.php",
            data: {
                firstName: firstName,
                lastName: lastName,
                email: email,
                password: password
            },
            dataType: "json",
            success: function (response) {
                $("#submitBtn").prop("disabled", false).text("Create Account");

                if (response.success) {
                    alert("Account created successfully!");
                    $(".signup-form")[0].reset();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("Something went wrong. Try again.");
                $("#submitBtn").prop("disabled", false).text("Create Account");
            }
        });
    });

    // Toggle Password Visibility
    $("#togglePassword").on("click", function () {
        let passwordField = $("#password");
        let eye = $("#eye");
        let eyeOff = $("#eyeOff");

        if (passwordField.attr("type") === "password") {
            passwordField.attr("type", "text");
            eye.addClass("hidden");
            eyeOff.removeClass("hidden");
        } else {
            passwordField.attr("type", "password");
            eye.removeClass("hidden");
            eyeOff.addClass("hidden");
        }
    });
});
