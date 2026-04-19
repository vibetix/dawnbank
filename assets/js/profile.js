$(document).ready(function() {
    $.ajax({
        url: "includes/get_user.php",
        method: "GET",
        dataType: "json",
        success: function(response) {
            if (response.loggedIn) {
                let initials = response.firstName.charAt(0).toUpperCase() + response.lastName.charAt(0).toUpperCase();
                let statusIcon = response.status === "approved"
                    ? '<i class="fa-solid fa-check-circle approved-icon"></i>'
                    : '<i class="fa-solid fa-clock pending-icon"></i>';

                $("#user-section").html(`
                    <div class="user-profile">
                        <div class="avatar-container">
                            <div class="avatar">${initials}</div>
                            <div class="status-icon">${statusIcon}</div>
                        </div>
                        <div class="user-info">
                            <span>${response.firstName} ${response.lastName}</span>
                        </div>
                        <div class="user-buttons">
                            <button class="dashboard-btn" onclick="goToDashboard()" disabled>Dashboard</button>
                            <button class="logout-btn" onclick="logout();">Logout</button>
                        </div>
                    </div>
                `);

                $("#mobile-section").html(`
                    <div class="mobile-buttons" style="display:flex; flex-direction: column; width:99%;height:100px ; margin-bottom:10px;gap:10px;">
                            <button class="dashboard-btn" id="dash" onclick="goToDashboard()" disabled style="padding: 12px 16px; font-size:20px">Dashboard</button>
                            <button class="logout-btn" onclick="logout();" style="padding: 12px 16px; font-size:20px">Logout</button>
                        </div>
                    <div class="user-profile" style="margin-top:220px; padding-left:100px; padding-top:10px; padding-bottom:10px; background: lightgray;">
                        <div class="avatar-container">
                            <div class="avatar">${initials}</div>
                            <div class="status-icon">${statusIcon}</div>
                        </div>
                        <div class="user-info">
                            <span>${response.firstName} ${response.lastName}</span>
                        </div>
                        
                    </div>
                `);
                
                // Enable dashboard button if user is approved
                if (response.status === "approved") {
                    document.querySelector(".dashboard-btn").disabled = false;
                    document.getElementById("dash").disabled = false;
                }
                
                // Hide sign-up buttons
                const signup = document.querySelector(".hero-buttons .button-primary");
                if(signup) {
                    signup.style.display = "none";
                }
                // Hide the contact section if it exists
                const signupSection = document.getElementById("contact");
                if (signupSection) {
                    signupSection.style.display = "none";
                }
                const cta = document.querySelector(".cta");
                if (cta) {
                    cta.style.display = "none";
                }
                // Display about button
                const about = document.getElementById("about-btn");
                if (about) {
                    about.style.display = "block";
                }

            } else {
                $("#user-section").html(`
                    <div class="user-profile">
                        <button class="sign-in-btn"><a href="login.html">Sign In</a></button>
                        <button class="button-primary"><a href="index.html#signup">Open Account</a></button>
                    </div>
                `);
                 $("#mobile-section").html(`
                    <button class="mobile-signin-btn"><a href="login.html">Sign In</a></button>
                    <button class="button-primary mobile-primary-btn">
                    <a href="index.html#signup" onclick="closeMenu()">Open Account</a></button>
                `);
            }
        }
    });
});

function goToDashboard() {
    window.location.href = "users/index.html"; // Redirects to dashboard
}

function logoutUser() {
    window.location.href = "logout.php"; // Redirects to logout script
}
