$(document).ready(function () {
    $(".login-form").submit(function (e) {
        e.preventDefault(); // Prevent form submission

        $("#message-container").html("<p style='color: white; background:#FFBF00; height:40px;padding: 5px; border-radius:10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>Logging in...</p>"); // Show loading message

        // Get input values
        var email = $("#email").val().trim();
        var password = $("#password").val();

        // Validate fields before sending AJAX request
        if (email === "" || password === "") {
            showMessage("Email and password are required.", "red");
            return;
        }

        var formData = { email: email, password: password };

        console.log("Sending AJAX Request:", formData); // Debugging

        $.ajax({
            url: "includes/signin.php", // PHP script path
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.log("Server Response:", response); // Debugging

                if (response.success) {
                    let firstName = response.first_name; // Get first name from response

                    sessionStorage.setItem("welcomeMessage", "Welcome, " + firstName + "!");

                    showMessage("Login successful! Redirecting...", "#3CB371");

                    // Redirect after 2 seconds
                    setTimeout(function () {
                        window.location.href = "index.html"; // Redirect to dashboard
                    }, 2000);
                } else {
                    showMessage(response.message, "red");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown, jqXHR.responseText); // Debugging
                showMessage("An unexpected error occurred. Please try again.", "red");
            }
        });
    });

    function showMessage(message, bgColor) {
        $("#message-container").html(`<p style='color:white; background: ${bgColor}; padding: 5px; border-radius:10px;height:40px;box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>${message}</p>`);
        
        // Hide message after 5 seconds
        setTimeout(function () {
            $("#message-container").html("");
        }, 5000);
    }
});
