$(document).ready(function () {
    $(".signup-form").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        // Show a loading message while processing
        $("#message-container").html("<p style='color: white; background:#FFBF00; height:40px;padding: 5px; border-radius:10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>Processing your request...</p>");

        var formData = {
            firstName: $("#firstName").val(),
            lastName: $("#lastName").val(),
            dob: $("#dob").val(),
            email: $("#email").val(),
            phone: $("#phone").val(),
            address: $("#address").val(),
            country: $("#country").val(),
            city: $("#city").val(),
            idType: $("#idType").val(),
            idNumber: $("#idNumber").val(),
            password: $("#password").val(),
            confirmPassword: $("#confirmPassword").val(),
        };

        $.ajax({
            url: "includes/signup.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Store the success message in sessionStorage before redirecting
                    sessionStorage.setItem("signupSuccessMessage", response.message);
                    
                    // Redirect to login page after a short delay
                    setTimeout(function () {
                        window.location.href = "login.html";
                    }, 1000); // Redirect after 1 second
                } else {
                    // Show error message and hide it after 5 seconds
                    showMessage(response.message, "red");
                }
            },
            error: function () {
                showMessage("An error occurred. Please try again.", "red");
            }
        });
    });

    // Function to display and auto-hide messages
    function showMessage(message, color) {
        $("#message-container").html(`<p style='color: white; background:${color}; padding: 5px; border-radius:10px;height:40px;box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>${message}</p>`);
        setTimeout(function () {
            $("#message-container").fadeOut();
        }, 5000); // Hide message after 5 seconds
    }

    // Check if there's a success message stored after redirection
    if (sessionStorage.getItem("signupSuccessMessage")) {
        showMessage(sessionStorage.getItem("signupSuccessMessage"), "#3CB371");
        sessionStorage.removeItem("signupSuccessMessage"); // Clear message after showing
    }
});
