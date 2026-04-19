$(document).ready(function () {
    $("#signUpForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission
        const load = document.getElementById("load");
        load.style.display = "flex";
        let name = $("#name").val().trim();
        let email = $("#email").val().trim();
        let password = $("#password").val();

        // Show a loading message while processing
        $("#message-container").html("<p style='color: white; background:#FFBF00; height:40px;padding: 5px; border-radius:10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);'>Processing your request...</p>");
        if (!name || !email || !password) {
            showToast("All fields are required.");
            return;
        }

        $.ajax({
            url: "../includes/admin_signup.php",
            type: "POST",
            data: { name: name, email: email, password: password },
            dataType: "json",
            success: function (response) {
                showMessage("Admin account created successfully.", "#3CB371");
                if (response.status === "success") {
                    setTimeout(() => {
                        load.style.display = "none";
                        window.location.href = "index.html"; // Redirect to dashboard
                    }, 2000);
                }else{
                    showMessage(response.message, "red");
                }
            },
            error: function () {
                showMessage("An error occurred. Please try again.");
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
