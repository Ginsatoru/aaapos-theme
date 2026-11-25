/**
 * Special Deals Auto-Rotation Script
 * Handles automatic product rotation with countdown timers
 * 
 * @package AAAPOS
 */

(function () {
  "use strict";

  // Configuration
  const ROTATION_INTERVAL = 8000; // 8 seconds per product
  const FADE_DURATION = 500; // 0.5 seconds fade

  class DealsRotator {
    constructor(wrapperElement) {
      this.wrapper = wrapperElement;
      this.deals = this.wrapper.querySelectorAll(".special-deal-card");
      this.dots = this.wrapper.querySelectorAll(".deals-dot");
      this.totalDeals = parseInt(this.wrapper.dataset.totalDeals, 10) || 0;
      this.currentIndex = 0;
      this.rotationTimer = null;
      this.isPaused = false;
      this.countdownTimers = [];

      if (this.totalDeals > 0) {
        this.init();
      }
    }

    init() {
      // Initialize countdown timers for all deals
      this.initCountdowns();

      // Only start rotation if we have multiple deals
      if (this.totalDeals > 1) {
        this.setupNavigation();
        this.startRotation();
        this.setupHoverPause();
      }
    }

    /**
     * Initialize countdown timers for all product deals
     */
    initCountdowns() {
      this.deals.forEach((deal, index) => {
        const countdownElement = deal.querySelector(".countdown-timer");
        if (countdownElement) {
          const endDate = countdownElement.dataset.endDate;
          if (endDate) {
            this.startCountdown(countdownElement, endDate, index);
          }
        }
      });
    }

    /**
     * Start countdown timer for a specific deal
     */
    startCountdown(element, endDateStr, dealIndex) {
      const updateCountdown = () => {
        const now = new Date().getTime();
        const endDate = new Date(endDateStr).getTime();
        const distance = endDate - now;

        if (distance < 0) {
          // Sale has ended - clear timer
          clearInterval(this.countdownTimers[dealIndex]);
          element.innerHTML = '<p style="color: #fff; text-align: center;">Sale Ended</p>';
          return;
        }

        // Calculate time units
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Update DOM
        const daysEl = element.querySelector(".days");
        const hoursEl = element.querySelector(".hours");
        const minutesEl = element.querySelector(".minutes");
        const secondsEl = element.querySelector(".seconds");

        if (daysEl) daysEl.textContent = String(days).padStart(2, "0");
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, "0");
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, "0");
      };

      // Update immediately, then every second
      updateCountdown();
      this.countdownTimers[dealIndex] = setInterval(updateCountdown, 1000);
    }

    /**
     * Setup navigation (dots and arrows)
     */
    setupNavigation() {
      // Dots navigation
      this.dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
          this.goToSlide(index);
          this.resetRotation();
        });
      });

      // Arrow navigation
      const prevBtn = this.wrapper.querySelector(".deals-arrow-prev");
      const nextBtn = this.wrapper.querySelector(".deals-arrow-next");

      if (prevBtn) {
        prevBtn.addEventListener("click", () => {
          this.previousSlide();
          this.resetRotation();
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener("click", () => {
          this.nextSlide();
          this.resetRotation();
        });
      }
    }

    /**
     * Setup pause on hover functionality
     */
    setupHoverPause() {
      this.wrapper.addEventListener("mouseenter", () => {
        this.isPaused = true;
      });

      this.wrapper.addEventListener("mouseleave", () => {
        this.isPaused = false;
      });
    }

    /**
     * Start automatic rotation
     */
    startRotation() {
      this.rotationTimer = setInterval(() => {
        if (!this.isPaused) {
          this.nextSlide();
        }
      }, ROTATION_INTERVAL);
    }

    /**
     * Reset rotation timer (when user manually navigates)
     */
    resetRotation() {
      clearInterval(this.rotationTimer);
      this.startRotation();
    }

    /**
     * Go to next slide
     */
    nextSlide() {
      const nextIndex = (this.currentIndex + 1) % this.totalDeals;
      this.goToSlide(nextIndex);
    }

    /**
     * Go to previous slide
     */
    previousSlide() {
      const prevIndex = (this.currentIndex - 1 + this.totalDeals) % this.totalDeals;
      this.goToSlide(prevIndex);
    }

    /**
     * Go to specific slide
     */
    goToSlide(index) {
      if (index === this.currentIndex) return;

      // Remove active class from current slide and dot
      this.deals[this.currentIndex].classList.remove("active");
      if (this.dots[this.currentIndex]) {
        this.dots[this.currentIndex].classList.remove("active");
      }

      // Add active class to new slide and dot
      this.currentIndex = index;
      this.deals[this.currentIndex].classList.add("active");
      if (this.dots[this.currentIndex]) {
        this.dots[this.currentIndex].classList.add("active");
      }
    }

    /**
     * Cleanup - clear all timers
     */
    destroy() {
      if (this.rotationTimer) {
        clearInterval(this.rotationTimer);
      }
      this.countdownTimers.forEach((timer) => {
        if (timer) clearInterval(timer);
      });
    }
  }

  /**
   * Initialize on DOM ready
   */
  function initDealsRotator() {
    const dealsWrapper = document.querySelector(".deals-slider-wrapper");
    if (dealsWrapper) {
      new DealsRotator(dealsWrapper);
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initDealsRotator);
  } else {
    initDealsRotator();
  }
})();