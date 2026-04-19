 document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-link");
    const mobileLinks = document.querySelectorAll(".mobile-link");
    navLinks.forEach(link => {
      link.addEventListener("click", function () {
        // Remove "active" from all links
        navLinks.forEach(link => link.removeAttribute("id"));

        // Add "active" to the clicked link
        this.id = "active";
      });
    });
    mobileLinks.forEach(link => {
      link.addEventListener("click", function(){
        // Remove "active" from all links
        mobileLinks.forEach(link => link.removeAttribute("id"));

        // Add "active" to the clicked link
        this.id = "active";
      });
    });
  });