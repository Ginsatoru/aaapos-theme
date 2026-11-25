/**
 * Universal Slider Component - SMOOTH SLIDE TRANSITIONS
 * Handles all slider types with slide-in/slide-out animations
 *
 * @version 2.4.0 - Added directional slide animations
 */

(function () {
  "use strict";

  // Track initialized sliders
  const initializedSliders = new WeakSet();

  /**
   * Main Slider Class
   */
  class UniversalSlider {
    constructor(container, options = {}) {
      // Validate container
      if (!container) {
        return;
      }

      // Prevent duplicate initialization
      if (initializedSliders.has(container)) {
        return;
      }

      this.container = container;

      // Find slides container
      this.slidesContainer = container.querySelector(
        ".testimonial-slides, .hero-slides, .slider-track",
      );

      if (!this.slidesContainer) {
        return;
      }

      // Find all slides
      this.slides = Array.from(
        this.slidesContainer.querySelectorAll(
          ".testimonial-slide, .hero-slide, .product-slide, .slider-item",
        ),
      );

      if (this.slides.length === 0) {
        return;
      }

      // Get or create dots container
      this.dotsContainer = container.querySelector(".slider-dots");

      // Configuration
      this.config = {
        autoPlay: options.autoPlay !== false,
        autoPlayDelay: options.autoPlayDelay || 5000,
        animationSpeed: options.animationSpeed || 700,
        ...options,
      };

      // State
      this.currentIndex = 0;
      this.isAnimating = false;
      this.autoPlayInterval = null;
      this.direction = "next";

      // Mark as initialized
      initializedSliders.add(container);

      // Initialize
      this.init();
    }

    init() {
      // Setup initial styles for all slides
      this.slides.forEach((slide, index) => {
        // Make all slides absolutely positioned for smooth sliding
        slide.style.position = "absolute";
        slide.style.top = "0";
        slide.style.left = "0";
        slide.style.width = "100%";
        slide.style.transition = `transform ${this.config.animationSpeed}ms cubic-bezier(0.4, 0, 0.2, 1), opacity ${this.config.animationSpeed}ms ease`;

        if (index === 0) {
          // First slide visible in center
          slide.style.transform = "translateX(0)";
          slide.style.opacity = "1";
          slide.style.zIndex = "2";
          slide.classList.add("active");
        } else {
          // Other slides hidden to the right
          slide.style.transform = "translateX(100%)";
          slide.style.opacity = "0";
          slide.style.zIndex = "1";
          slide.classList.remove("active");
        }
      });

      // Set container height based on first slide
      this.updateContainerHeight();

      // Create dots
      this.createDots();

      // Setup event listeners
      this.setupEventListeners();

      // Start autoplay
      if (this.config.autoPlay && this.slides.length > 1) {
        this.startAutoPlay();
      }

      // Mark container as initialized
      this.container.classList.add("slider-initialized");
    }

    showSlide(index, direction = "next") {
      // Prevent multiple animations at once
      if (this.isAnimating) {
        return;
      }

      // Validate index
      if (index < 0 || index >= this.slides.length) return;

      // If same slide, do nothing
      if (index === this.currentIndex) return;

      this.isAnimating = true;

      const currentSlide = this.slides[this.currentIndex];
      const nextSlide = this.slides[index];

      // Step 1: Position next slide off-screen to the RIGHT
      nextSlide.style.transform = "translateX(100%)";
      nextSlide.style.opacity = "1";
      nextSlide.style.zIndex = "3";
      nextSlide.style.pointerEvents = "none";

      // Force reflow
      void nextSlide.offsetHeight;

      // Step 2: Current slide fades out in place, next slide enters from right
      currentSlide.style.opacity = "0";
      currentSlide.style.zIndex = "2";
      currentSlide.style.pointerEvents = "none";

      // Next slide slides in from right to center
      nextSlide.style.transform = "translateX(0)";
      nextSlide.style.pointerEvents = "auto";

      // Update classes
      currentSlide.classList.remove("active");
      nextSlide.classList.add("active");

      // Update state and UI
      this.currentIndex = index;
      this.updateDots();

      // Update container height
      this.updateContainerHeight();

      // Step 3: Clean up after animation
      setTimeout(() => {
        currentSlide.style.zIndex = "1";
        currentSlide.style.transform = "translateX(100%)";

        this.isAnimating = false;
      }, this.config.animationSpeed);
    }

    updateContainerHeight() {
      const activeSlide = this.slides[this.currentIndex];
      if (!activeSlide) return;

      // Get the natural height of the active slide
      const height = activeSlide.scrollHeight;

      if (height > 0) {
        this.slidesContainer.style.height = `${height}px`;
        this.slidesContainer.style.transition = `height ${this.config.animationSpeed}ms ease-in-out`;
      }
    }

    createDots() {
      if (!this.dotsContainer) return;
      if (this.slides.length <= 1) return;

      // Clear existing dots
      this.dotsContainer.innerHTML = "";

      // Create new dots
      this.slides.forEach((_, index) => {
        const dot = document.createElement("button");
        dot.className = "slider-dot";
        dot.type = "button";
        dot.setAttribute("aria-label", `Go to slide ${index + 1}`);

        if (index === 0) {
          dot.classList.add("active");
        }

        dot.addEventListener("click", () => {
          const direction = index > this.currentIndex ? "next" : "prev";
          this.goToSlide(index, direction);
        });

        this.dotsContainer.appendChild(dot);
      });

      this.dots = Array.from(
        this.dotsContainer.querySelectorAll(".slider-dot"),
      );
    }

    updateDots() {
      if (!this.dots) return;

      this.dots.forEach((dot, i) => {
        if (i === this.currentIndex) {
          dot.classList.add("active");
        } else {
          dot.classList.remove("active");
        }
      });
    }

    goToSlide(index, direction = null) {
      if (index === this.currentIndex) return;

      // Auto-determine direction if not provided
      if (!direction) {
        direction = index > this.currentIndex ? "next" : "prev";
      }

      this.stopAutoPlay();
      this.showSlide(index, direction);

      if (this.config.autoPlay) {
        this.startAutoPlay();
      }
    }

    nextSlide() {
      const nextIndex = (this.currentIndex + 1) % this.slides.length;
      this.showSlide(nextIndex, "next");
    }

    prevSlide() {
      const prevIndex =
        (this.currentIndex - 1 + this.slides.length) % this.slides.length;
      this.showSlide(prevIndex, "prev");
    }

    startAutoPlay() {
      if (!this.config.autoPlay || this.slides.length <= 1) return;

      this.stopAutoPlay();
      this.autoPlayInterval = setInterval(() => {
        this.nextSlide();
      }, this.config.autoPlayDelay);
    }

    stopAutoPlay() {
      if (this.autoPlayInterval) {
        clearInterval(this.autoPlayInterval);
        this.autoPlayInterval = null;
      }
    }

    setupEventListeners() {
      // Pause on hover
      this.container.addEventListener("mouseenter", () => {
        this.stopAutoPlay();
      });

      this.container.addEventListener("mouseleave", () => {
        if (this.config.autoPlay) {
          this.startAutoPlay();
        }
      });

      // Pause when tab hidden
      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          this.stopAutoPlay();
        } else if (this.config.autoPlay) {
          this.startAutoPlay();
        }
      });

      // Touch support with direction detection
      let touchStartX = 0;
      let touchEndX = 0;

      this.slidesContainer.addEventListener(
        "touchstart",
        (e) => {
          touchStartX = e.touches[0].clientX;
        },
        { passive: true },
      );

      this.slidesContainer.addEventListener("touchend", (e) => {
        touchEndX = e.changedTouches[0].clientX;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > 50) {
          this.stopAutoPlay();
          if (diff > 0) {
            this.nextSlide(); // Swipe left = next
          } else {
            this.prevSlide(); // Swipe right = prev
          }
          if (this.config.autoPlay) {
            this.startAutoPlay();
          }
        }
      });

      // Keyboard navigation
      document.addEventListener("keydown", (e) => {
        if (!this.isInViewport()) return;
        if (e.target.matches("input, textarea, select")) return;

        if (e.key === "ArrowLeft") {
          e.preventDefault();
          this.stopAutoPlay();
          this.prevSlide();
          if (this.config.autoPlay) {
            this.startAutoPlay();
          }
        } else if (e.key === "ArrowRight") {
          e.preventDefault();
          this.stopAutoPlay();
          this.nextSlide();
          if (this.config.autoPlay) {
            this.startAutoPlay();
          }
        }
      });

      // Update height on window resize
      let resizeTimeout;
      window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
          this.updateContainerHeight();
        }, 250);
      });
    }

    isInViewport() {
      const rect = this.container.getBoundingClientRect();
      return (
        rect.top < window.innerHeight &&
        rect.bottom > 0 &&
        rect.left < window.innerWidth &&
        rect.right > 0
      );
    }
  }

  /**
   * Extend slider to handle video slides
   */
  if (typeof UniversalSlider !== "undefined") {
    // Override or extend slide change method
    const originalGoToSlide = UniversalSlider.prototype.goToSlide;

    UniversalSlider.prototype.goToSlide = function (index) {
      // Call original method
      originalGoToSlide.call(this, index);

      // Handle video for the new slide
      const newSlide = this.slides[index];
      this.handleVideoForSlide(newSlide, index);
    };

    UniversalSlider.prototype.handleVideoForSlide = function (slide, index) {
      const videoContainer = slide.querySelector(".hero-video-container");
      if (!videoContainer) return;

      const video = videoContainer.querySelector("video");
      if (!video) return;

      // Pause videos in other slides
      this.slides.forEach((s, i) => {
        if (i !== index) {
          const otherVideo = s.querySelector("video");
          if (otherVideo) {
            otherVideo.pause();
          }
        }
      });

      // Try to play video in current slide
      if (video.paused) {
        const playPromise = video.play();
        if (playPromise !== undefined) {
          playPromise.catch((error) => {
            console.log("Video autoplay prevented:", error);
          });
        }
      }
    };
  }

  /**
   * Auto-initialize all sliders
   */
  function initSliders() {
    const selectors = [
      ".testimonial-slider",
      ".hero-slider",
      ".product-slider",
    ];

    selectors.forEach((selector) => {
      const containers = document.querySelectorAll(selector);

      containers.forEach((container) => {
        // Skip if already initialized
        if (initializedSliders.has(container)) {
          return;
        }

        // Get config from data attributes
        const config = {
          autoPlay: container.dataset.autoplay !== "false",
          autoPlayDelay: parseInt(container.dataset.delay) || 5000,
          animationSpeed: parseInt(container.dataset.speed) || 700,
        };

        // Initialize
        new UniversalSlider(container, config);
      });
    });
  }

  /**
   * Initialize when DOM is ready
   */
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSliders);
  } else {
    initSliders();
  }

  // Expose for manual initialization
  window.UniversalSlider = UniversalSlider;
})();
