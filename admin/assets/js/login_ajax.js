$(document).ready(function () {
    $("#signInForm").submit(function (e) {
        e.preventDefault();
         const load = document.getElementById("load");
        load.style.display = "flex";
        let email = $("#email").val().trim();
        let password = $("#password").val().trim();
        let button = $("#signInButton");
        let buttonText = $("#buttonText");
        // Show a loading message while processing
        $("#message-container").html("<p style='color: white; background:#FFBF00; height:40px;padding: 5px; border-radius:10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>Signing in ...</p>");
        if (email === "" || password === "") {
            showToast("All fields are required");
            return;
        }

        button.prop("disabled", true);
        buttonText.text("Signing in...");

        $.ajax({
            url: "../includes/admin_signin.php",
            type: "POST",
            data: { email: email, password: password },
            dataType: "json",
            success: function (response) {
                showMessage("Login successful. Redirecting...", "#3CB371");
                if (response.status === "success") {
                    setTimeout(() => {
                        window.location.href = "admin-index.html"; // Redirect on success
                    }, 2000);
                } else {
                    button.prop("disabled", false);
                    buttonText.text("Sign in");
                }
            },
            error: function () {
                showMessage("Something went wrong. Try again.", "red");
                button.prop("disabled", false);
                buttonText.text("Sign in");
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
});
