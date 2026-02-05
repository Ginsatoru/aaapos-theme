/**
 * Simple Hero Product Carousel
 * hero-product-carousel.js
 * 
 * Auto-rotating product carousel with smooth transitions
 * No complex effects - just clean, smooth sliding
 * 
 * @package aaapos-prime
 */

(function () {
  "use strict";

  class HeroProductCarousel {
    constructor() {
      this.carousel = document.querySelector(".hero-product-carousel");

      if (!this.carousel) return;

      this.track = this.carousel.querySelector(".product-carousel-track");
      this.slides = Array.from(this.carousel.querySelectorAll(".product-slide"));
      this.indicators = Array.from(
        this.carousel.querySelectorAll(".indicator-dot")
      );

      this.currentSlide = 0;
      this.slideCount = this.slides.length;
      this.autoplayInterval = null;
      this.autoplayDelay = 4000; // 4 seconds per slide
      this.isTransitioning = false;

      if (this.slideCount === 0) return;

      this.init();
    }

    init() {
      // Set first slide as active
      this.showSlide(0);

      // Bind indicator clicks
      this.indicators.forEach((indicator, index) => {
        indicator.addEventListener("click", () => {
          this.goToSlide(index);
        });
      });

      // Start autoplay
      this.startAutoplay();

      // Pause on hover
      this.carousel.addEventListener("mouseenter", () => {
        this.stopAutoplay();
      });

      this.carousel.addEventListener("mouseleave", () => {
        this.startAutoplay();
      });

      // Pause on visibility change (tab switching)
      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          this.stopAutoplay();
        } else {
          this.startAutoplay();
        }
      });
    }

    showSlide(index) {
      if (this.isTransitioning) return;
      if (index < 0 || index >= this.slideCount) return;

      this.isTransitioning = true;

      // Update slides
      this.slides.forEach((slide, i) => {
        if (i === index) {
          slide.classList.add("active");
        } else {
          slide.classList.remove("active");
        }
      });

      // Update indicators
      this.indicators.forEach((indicator, i) => {
        if (i === index) {
          indicator.classList.add("active");
        } else {
          indicator.classList.remove("active");
        }
      });

      this.currentSlide = index;

      // Allow next transition after animation completes
      setTimeout(() => {
        this.isTransitioning = false;
      }, 600); // Match CSS transition duration
    }

    goToSlide(index) {
      if (index === this.currentSlide) return;

      this.showSlide(index);

      // Reset autoplay timer when manually changing slides
      this.stopAutoplay();
      this.startAutoplay();
    }

    nextSlide() {
      const next = (this.currentSlide + 1) % this.slideCount;
      this.showSlide(next);
    }

    previousSlide() {
      const prev =
        (this.currentSlide - 1 + this.slideCount) % this.slideCount;
      this.showSlide(prev);
    }

    startAutoplay() {
      this.stopAutoplay(); // Clear any existing interval

      this.autoplayInterval = setInterval(() => {
        this.nextSlide();
      }, this.autoplayDelay);
    }

    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      new HeroProductCarousel();
    });
  } else {
    new HeroProductCarousel();
  }
})();