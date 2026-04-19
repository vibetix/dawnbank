document.addEventListener('DOMContentLoaded', function() {
  // Update copyright year
  document.getElementById('current-year').textContent = new Date().getFullYear();
  
  // Navbar scroll effect
  const navbar = document.querySelector('.navbar');
  const scrollToTopBtn = document.getElementById('scroll-to-top');
  
  function handleScroll() {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
    
    if (window.scrollY > 500) {
      scrollToTopBtn.classList.add('active');
    } else {
      scrollToTopBtn.classList.remove('active');
    }
  }
  
  window.addEventListener('scroll', handleScroll);
  

  
  
  // Scroll to top button
  scrollToTopBtn.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      if (this.getAttribute('href') !== '#') {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          const navbarHeight = navbar.offsetHeight;
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      }
    });
  });
  
  // Animation on scroll
  const animatedElements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .fade-in-up, .fade-in-down');
  
  function checkAnimation() {
    animatedElements.forEach(element => {
      const elementPosition = element.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      
      if (elementPosition.top < windowHeight * 0.85) {
        const delay = element.classList.contains('delay-1') ? 100 :
                      element.classList.contains('delay-2') ? 200 :
                      element.classList.contains('delay-3') ? 300 :
                      element.classList.contains('delay-4') ? 400 :
                      element.classList.contains('delay-5') ? 500 : 0;
                      
        setTimeout(() => {
          element.style.animationPlayState = 'running';
        }, delay);
      }
    });
  }
  
  window.addEventListener('scroll', checkAnimation);
  window.addEventListener('resize', checkAnimation);
  
  // Initial animation check
  setTimeout(checkAnimation, 100);
  
});
