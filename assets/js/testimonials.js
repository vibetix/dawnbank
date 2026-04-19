// Testimonials carousel
  const testimonialsContainer = document.getElementById('testimonials-container');
  const prevBtn = document.getElementById('prev-testimonial');
  const nextBtn = document.getElementById('next-testimonial');
  let scrollAmount = 0;
  
  function checkScrollability() {
    const isScrollLeft = testimonialsContainer.scrollLeft > 0;
    const isScrollRight = testimonialsContainer.scrollLeft < testimonialsContainer.scrollWidth - testimonialsContainer.clientWidth - 10;
    
    prevBtn.disabled = !isScrollLeft;
    nextBtn.disabled = !isScrollRight;
  }
  
  function scrollTestimonials(direction) {
    const cardWidth = testimonialsContainer.querySelector('.testimonial-card').offsetWidth;
    const scrollAmount = direction === 'left' ? -cardWidth - 24 : cardWidth + 24;
    
    testimonialsContainer.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    });
    
    setTimeout(checkScrollability, 400);
  }
  
  prevBtn.addEventListener('click', () => scrollTestimonials('left'));
  nextBtn.addEventListener('click', () => scrollTestimonials('right'));
  
  testimonialsContainer.addEventListener('scroll', checkScrollability);
  
  // Initial check
  checkScrollability();
  