document.addEventListener("DOMContentLoaded", () => {
  // --- Slideshow ---
  let slideIndex = 0;
  const slides = document.getElementsByClassName("slides");

  function showSlides() {
    for (let slide of slides) {
      slide.style.display = "none";
      slide.classList.remove("active");
    }

    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;

    const currentSlide = slides[slideIndex - 1];
    currentSlide.style.display = "block";
    setTimeout(() => currentSlide.classList.add("active"), 10);

    setTimeout(showSlides, 5000);
  }

  if (slides.length > 0) showSlides();

  // --- Navigation Buttons ---
  const navMap = {
    btn1: "../HTML/NIC_for_first_time.html",
    btn2: "../HTML/Form_Obtaining_NIC_for_a_lost_NIC.html",
    btn3: "../HTML/Form_Amendment_of_NIC.html",
  };

  Object.entries(navMap).forEach(([btnId, targetUrl]) => {
    const button = document.getElementById(btnId);
    if (button) {
      button.addEventListener("click", () => {
        window.location.href = targetUrl;
      });
    }
  });
});
