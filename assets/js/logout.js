function logout() {
             if (confirm("Are you sure you want to log out?")) {
        $.ajax({
            url: 'includes/logout.php',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert("Logout failed. Please try again.");
                }
            },
            error: function () {
                alert("An error occurred while trying to log out.");
            }
        });
    }
}       