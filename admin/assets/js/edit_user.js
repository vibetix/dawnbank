$(document).ready(function () {
    function getUrlParam(param) {
    let urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}
    
    let userId = getUrlParam("id"); // Get user_id from URL

    if (!userId) {
        $("#updateFeedback").html('<span style="color: red;">User ID is missing from the URL.</span>');
        return;
    }

    // Function to fetch user data
    function loadUserData() {
        $.ajax({
            url: "../includes/get-user.php",
            type: "GET",
            data: { user_id: userId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let user = response.user;
                    
                    // Populate displayed user info
                    $("#profilePicture").attr("src", user.profile_pic ? "/uploads/" + user.profile_pic : "https://github.com/shadcn.png");
                    $("#fullNameDisplay").text(user.full_name);
                    $("#emailDisplay").text(user.email);
                    $("#clientDisplay").text(user.client_id)
                    $("#phoneDisplay").text(user.phone);
                    $("#addressDisplay").text(user.address);
                    $("#idTypeDisplay").text(user.id_type);
                    $("#idNumberDisplay").text(user.id_number);

                    // Populate form fields
                    $("#firstName").val(user.first_name);
                    $("#lastName").val(user.last_name);
                    $("#email").val(user.email);
                    $("#phone").val(user.phone);
                    $("#address").val(user.address);
                    $("#type").val(user.id_type)
                    $("#idType").val(user.id_type);
                    $("#idNumber").val(user.id_number);
                    $("#userId").val(user.id);
                } else {
                    $("#updateFeedback").html(`<span style="color: red;">${response.message}</span>`);
                }
            },
            error: function () {
                $("#updateFeedback").html('<span style="color: red;">Error fetching user data.</span>');
            }
        });
    }

    loadUserData(); // Load user data on page load

    // Handle form submission
    $("form").on("submit", function (e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        formData.append("user_id", userId);
        formData.append("current_profile_pic", $("#profilePicture").attr("src").replace("../uploads/", "")); 

        $.ajax({
            url: "../includes/update_user.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#updateFeedback").html(`<span style="color: green;">${response.message}</span>`);
                    loadUserData(); // Reload updated data
                } else {
                    $("#updateFeedback").html(`<span style="color: red;">${response.message}</span>`);
                }
            },
            error: function () {
                $("#updateFeedback").html('<span style="color: red;">Error updating profile.</span>');
            }
        });
    });
});
