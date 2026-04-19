$(document).ready(function () {
    $.ajax({
        url: "../includes/get_admin.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.error) {
                console.log(response.error);
            } else {
                let username = response.username || "Unknown User"; // Default value
                let role = response.role || "Admin"; // Default role is Admin
                let avatar = response.avatar || "https://github.com/shadcn.png"; // Default avatar

                $(".user-name").text(username);
                $(".user-name-small").text(username);
                $(".user-role").text(role);
                $(".avatar img").attr("src", avatar);

                // Generate initials for avatar fallback
                let initials = username
                    .split(" ")
                    .map(word => word.charAt(0)) // Get first letter of each word
                    .join("")
                    .toUpperCase();

                $(".avatar-fallback").text(initials);
            }
        },
        error: function () {
            console.log("Error fetching user data.");
        }
    });
});
