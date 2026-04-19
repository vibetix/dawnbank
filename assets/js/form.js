document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ JavaScript Loaded Successfully!");

    // ✅ Ensure password strength checker runs
    let passwordField = document.getElementById("password");
    if (passwordField) {
        passwordField.addEventListener("keyup", checkPasswordStrength);
    } else {
        console.error("⚠️ Password field not found!");
    }

    // ✅ Ensure password confirmation checker runs
    let confirmPasswordField = document.getElementById("confirmPassword");
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener("keyup", validatePasswordMatch);
    } else {
        console.error("⚠️ Confirm password field not found!");
    }

    // ✅ Call togglePassword function to enable click events
    togglePassword();

});

// ✅ Function to toggle password visibility
function togglePassword() {
    document.querySelectorAll(".password-toggle").forEach(icon => {
        icon.addEventListener("click", function () {
            let passwordField = this.previousElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    });
}

// ✅ Handle Next & Previous Step Navigation
function nextStep(step) {
    if (!validateStep(step - 1)) return; // Prevent navigation if step is incomplete

    document.querySelectorAll(".form-section").forEach(section => {
        section.classList.remove("active");
    });

    document.getElementById("step-" + step).classList.add("active");
    updateProgressBar(step);
}

function prevStep(step) {
    document.querySelectorAll(".form-section").forEach(section => {
        section.classList.remove("active");
    });

    document.getElementById("step-" + step).classList.add("active");
    updateProgressBar(step);
}

function updateProgressBar(step) {
    document.querySelectorAll(".progress-step").forEach((stepEl, index) => {
        stepEl.classList.toggle("active", index < step);
    });

    document.querySelectorAll(".progress-line").forEach((line, index) => {
        line.classList.toggle("active", index < step - 1);
    });
}

function validateStep(step) {
    let section = document.getElementById("step-" + step);
    let inputs = section.querySelectorAll("input, select");
    
    for (let input of inputs) {
        if (!input.value) {
            alert("Please complete all fields before proceeding.");
            return false;
        }
    }
    return true;
}

// ✅ Password Strength Checker
function checkPasswordStrength() {
    let password = document.getElementById("password").value;
    let bar = document.getElementById("password-bar");
    if (!bar) return console.error("⚠️ Password strength bar element missing!");

    // ✅ STRONG: Uppercase + Lowercase + Number + Special Character & length ≥ 8
    if (password.length >= 8 && password.match(/[A-Z]/) && password.match(/[a-z]/) && password.match(/[0-9]/) && password.match(/[!@#$%^&*]/)) {
        bar.style.width = "100%";
        bar.style.backgroundColor = "green"; // Strong
    } 
    // 🟠 MEDIUM: Uppercase + Lowercase + Number (No Special Character)
    else if (password.length >= 6 && password.match(/[A-Z]/) && password.match(/[a-z]/) && password.match(/[0-9]/)) {
        bar.style.width = "50%";
        bar.style.backgroundColor = "orange"; // Medium
    } 
    // 🔴 WEAK: Anything shorter or missing criteria
    else {
        bar.style.width = "20%";
        bar.style.backgroundColor = "red"; // Weak
    }
}

// ✅ Confirm Password Match
function validatePasswordMatch() {
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let errorText = document.getElementById("password-error");

    if (!errorText) return console.error("⚠️ Password error message missing!");

    if (password !== confirmPassword) {
        errorText.innerHTML = "❌ Passwords do not match!";
        errorText.style.color = "red";
    } else {
        errorText.innerHTML = "✅ Passwords match!";
        errorText.style.color = "green";
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const openTerms = document.getElementById("openTerms");
    const termsPopup = document.getElementById("termsPopup");
    const closePopup = document.querySelector(".close");
    const acceptTerms = document.getElementById("acceptTerms");
    const termsCheckbox = document.getElementById("terms");
    const content = document.querySelector(".popup-content");
    const submitBtn = document.getElementById("submitBtn");

    // Ensure elements exist before adding event listeners
    if (openTerms && termsPopup && closePopup && acceptTerms && termsCheckbox && submitBtn) {
        // Initially disable submit button
        submitBtn.disabled = true;

        // Show popup when clicking the link
        openTerms.addEventListener("click", function (event) {
            event.preventDefault();
            termsPopup.style.display = "block";
            content.style.display = "block";
        });

        // Close popup
        closePopup.addEventListener("click", function () {
            termsPopup.style.display = "none";
            content.style.display = "none";
        });

        // Accept terms and check the checkbox
        acceptTerms.addEventListener("click", function () {
            termsCheckbox.checked = true;
            submitBtn.disabled = false; // Enable submit button
            termsPopup.style.display = "none";
        });

        // Disable submit if checkbox is not checked
        termsCheckbox.addEventListener("change", function () {
            submitBtn.disabled = !termsCheckbox.checked;
        });
    } else {
        console.error("One or more elements are missing. Check your HTML.");
    }
});

