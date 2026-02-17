/**
 * Page Loading Animation - Dynamic Mode
 * Shows while page loads, hides when ready
 * 
 * @package aaapos-prime
 */

(function () {
  "use strict";

  /**
   * Dynamic Page Loader Class
   */
  class PageLoader {
    constructor() {
      // Check if loader should be enabled
      if (!document.body.classList.contains("page-loading")) {
        return;
      }

      this.loader = document.querySelector(".page-loader-overlay");
      if (!this.loader) {
        return;
      }

      this.progressBar = this.loader.querySelector(".page-loader-progress-bar");
      this.startTime = Date.now();
      this.minDisplayTime = 500; // Minimum time to show loader (prevents flash)
      this.maxWaitTime = 5000; // Maximum wait time (5 seconds fallback)
      this.fadeOutDuration = 500; // Fade out animation duration
      
      // Track loading progress
      this.resourcesLoaded = false;
      this.imagesLoaded = false;
      this.minTimeElapsed = false;

      this.init();
    }

    init() {
      // Start progress animation immediately
      this.startProgressAnimation();

      // Set minimum display time
      setTimeout(() => {
        this.minTimeElapsed = true;
        this.checkIfReadyToHide();
      }, this.minDisplayTime);

      // Listen for page load events
      this.setupLoadListeners();

      // Fallback timeout (hide after max wait time)
      setTimeout(() => {
        console.log('Page loader: Maximum wait time reached, hiding loader');
        this.forceHide();
      }, this.maxWaitTime);
    }

    startProgressAnimation() {
      if (!this.progressBar) return;

      // Animate to 30% quickly (resources loading)
      setTimeout(() => {
        this.progressBar.style.transition = 'width 0.3s ease-out';
        this.progressBar.style.width = '30%';
      }, 100);

      // Then to 60% (DOM ready)
      setTimeout(() => {
        this.progressBar.style.transition = 'width 0.5s ease-out';
        this.progressBar.style.width = '60%';
      }, 400);
    }

    setupLoadListeners() {
      // Check if DOM is already loaded
      if (document.readyState === 'interactive' || document.readyState === 'complete') {
        this.onDOMReady();
      } else {
        document.addEventListener('DOMContentLoaded', () => {
          this.onDOMReady();
        });
      }

      // Check if page is fully loaded (including images)
      if (document.readyState === 'complete') {
        this.onPageFullyLoaded();
      } else {
        window.addEventListener('load', () => {
          this.onPageFullyLoaded();
        });
      }
    }

    onDOMReady() {
      console.log('Page loader: DOM ready');
      this.resourcesLoaded = true;

      // Progress to 80%
      if (this.progressBar) {
        this.progressBar.style.transition = 'width 0.4s ease-out';
        this.progressBar.style.width = '80%';
      }

      // Check if we can hide
      this.checkIfReadyToHide();
    }

    onPageFullyLoaded() {
      console.log('Page loader: Page fully loaded');
      this.imagesLoaded = true;

      // Progress to 100%
      if (this.progressBar) {
        this.progressBar.style.transition = 'width 0.3s ease-out';
        this.progressBar.style.width = '100%';
      }

      // Check if we can hide
      this.checkIfReadyToHide();
    }

    checkIfReadyToHide() {
      // Hide loader when:
      // 1. Minimum time has elapsed (prevents flash)
      // 2. Page is fully loaded (all resources including images)
      if (this.minTimeElapsed && this.imagesLoaded) {
        console.log('Page loader: All conditions met, hiding loader');
        this.hideLoader();
      }
    }

    hideLoader() {
      if (!this.loader) return;

      // Small delay for smooth UX
      setTimeout(() => {
        // Add fade-out class
        this.loader.classList.add('fade-out');

        // Remove loader from DOM after fade completes
        setTimeout(() => {
          if (this.loader && this.loader.parentNode) {
            this.loader.remove();
          }
          // Enable scrolling
          document.body.classList.remove('page-loading');
          
          console.log('Page loader: Removed from DOM');
        }, this.fadeOutDuration);
      }, 150);
    }

    forceHide() {
      // Force hide if taking too long
      if (this.progressBar) {
        this.progressBar.style.transition = 'width 0.2s ease-out';
        this.progressBar.style.width = '100%';
      }
      
      setTimeout(() => {
        this.hideLoader();
      }, 200);
    }
  }

  /**
   * Initialize when script loads
   */
  function init() {
    new PageLoader();
  }

  // Start immediately
  init();
})();