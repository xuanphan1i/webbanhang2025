let currentIndex = 0;
const slides = document.querySelectorAll(".slide");
const totalSlides = slides.length;

function updateSlide() {
  document.querySelector(".sliders").style.transform = `translateX(-${
    currentIndex * 100
  }%)`;
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % totalSlides;
  updateSlide();
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
  updateSlide();
}

// Tự động chuyển ảnh mỗi 3 giây
setInterval(nextSlide, 3000);
