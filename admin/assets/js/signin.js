document.addEventListener('DOMContentLoaded', function() {
  // Get form elements
  const form = document.getElementById('signInForm') || document.getElementById('signUpForm');
  const passwordInput = document.getElementById('password');
  const togglePasswordButton = document.getElementById('togglePassword');
  const eyeIcon = document.getElementById('eye');
  const eyeOffIcon = document.getElementById('eyeOff');
  const submitButton = document.getElementById('signInButton') || document.getElementById('signUpButton');
  const buttonText = document.getElementById('buttonText');
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toastMessage');
  
  // Toggle password visibility
  if (togglePasswordButton) {
    togglePasswordButton.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle eye icons
      eyeIcon.classList.toggle('hidden');
      eyeOffIcon.classList.toggle('hidden');
    });
  }
});
