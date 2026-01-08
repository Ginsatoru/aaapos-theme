/**
 * Hero Single WebM Video Handling
 * Manages single video background in hero section
 */
(function () {
  "use strict";

  class HeroVideoManager {
    constructor() {
      this.video = null;
      this.isMobile = window.innerWidth <= 768;
      this.heroSection = document.querySelector(".hero-section");

      // Only initialize if we have a hero section
      if (!this.heroSection) return;

      this.useMobileFallback =
        this.heroSection.dataset.mobileFallback === "true";
      this.videoLoop = this.heroSection.dataset.videoLoop === "true";
      this.videoMute = this.heroSection.dataset.videoMute === "true";
      this.videoUrl = this.heroSection.dataset.videoUrl;
      this.fallbackImage = this.heroSection.dataset.fallbackImage;

      this.init();
    }

    init() {
      this.cacheElements();

      // Only initialize if we're in video mode and not on mobile fallback
      if (this.isVideoMode() && (!this.isMobile || !this.useMobileFallback)) {
        this.createVideoElement();
        this.bindEvents();
      }
    }

    isVideoMode() {
      return (
        this.heroSection &&
        this.heroSection.classList.contains("hero-video-mode")
      );
    }

    cacheElements() {
      this.videoContainer = this.heroSection.querySelector(
        ".hero-video-container",
      );
    }

    bindEvents() {
      // Handle window resize for mobile detection
      window.addEventListener("resize", this.handleResize.bind(this));
    }

    handleResize() {
      const wasMobile = this.isMobile;
      this.isMobile = window.innerWidth <= 768;

      // If mobile state changed and we use mobile fallback
      if (
        wasMobile !== this.isMobile &&
        this.useMobileFallback &&
        this.isVideoMode()
      ) {
        if (this.isMobile) {
          this.pauseVideo();
          this.hideVideo();
        } else {
          this.showVideo();
          this.playVideo();
        }
      }
    }

    createVideoElement() {
      if (!this.videoContainer || !this.videoUrl) {
        console.warn("Missing video container or URL");
        return;
      }

      // Create video element
      this.video = document.createElement("video");
      this.video.className = "hero-background-video";
      this.video.playsInline = true;
      this.video.preload = "metadata";
      this.video.muted = this.videoMute;
      this.video.loop = this.videoLoop;

      // Set poster if available
      if (this.fallbackImage) {
        this.video.poster = this.fallbackImage;
      }

      // Add WebM source
      const source = document.createElement("source");
      source.src = this.videoUrl;
      source.type = "video/webm";
      this.video.appendChild(source);

      // Add error handling
      this.video.addEventListener("error", this.handleVideoError.bind(this));
      this.video.addEventListener(
        "canplay",
        this.handleVideoCanPlay.bind(this),
      );

      // Add to container
      const fallbackImage = this.videoContainer.querySelector(
        ".video-fallback-image",
      );
      if (fallbackImage) {
        fallbackImage.style.display = "none";
      }
      this.videoContainer.appendChild(this.video);

      // Setup controls
      this.setupVideoControls();
    }

    setupVideoControls() {
      const playBtn = this.videoContainer?.querySelector(".video-play-btn");
      const pauseBtn = this.videoContainer?.querySelector(".video-pause-btn");
      const muteBtn = this.videoContainer?.querySelector(".video-mute-btn");

      if (playBtn && this.video) {
        playBtn.addEventListener("click", () => {
          this.playVideo();
          playBtn.style.display = "none";
          if (pauseBtn) pauseBtn.style.display = "flex";
        });
      }

      if (pauseBtn && this.video) {
        pauseBtn.addEventListener("click", () => {
          this.pauseVideo();
          pauseBtn.style.display = "none";
          if (playBtn) playBtn.style.display = "flex";
        });
      }

      if (muteBtn && this.video) {
        muteBtn.addEventListener("click", () => this.toggleMute(muteBtn));
      }

      // Auto hide/show controls based on video state
      if (this.video) {
        this.video.addEventListener("play", () => {
          playBtn?.style.setProperty("display", "none", "important");
          pauseBtn?.style.setProperty("display", "flex", "important");
        });

        this.video.addEventListener("pause", () => {
          pauseBtn?.style.setProperty("display", "none", "important");
          playBtn?.style.setProperty("display", "flex", "important");
        });
      }
    }

    playVideo() {
      if (!this.video) return;

      const playPromise = this.video.play();

      if (playPromise !== undefined) {
        playPromise
          .then(() => {
            // Video started playing - hide fallback image
            const fallbackImage = this.videoContainer?.querySelector(
              ".video-fallback-image",
            );
            if (fallbackImage) {
              fallbackImage.style.display = "none";
            }
          })
          .catch((error) => {
            console.warn("Video autoplay failed:", error);
            // Show play button if autoplay fails
            const playBtn =
              this.videoContainer?.querySelector(".video-play-btn");
            if (playBtn) {
              playBtn.style.display = "flex";
            }
          });
      }
    }

    pauseVideo() {
      if (!this.video) return;
      this.video.pause();
    }

    toggleMute(muteBtn) {
      if (!this.video || !muteBtn) return;

      this.video.muted = !this.video.muted;

      // Update button icon
      const isMuted = this.video.muted;
      muteBtn.innerHTML = isMuted
        ? '<svg class="mute-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>'
        : '<svg class="unmute-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>';

      muteBtn.setAttribute(
        "aria-label",
        isMuted ? "Unmute video" : "Mute video",
      );
    }

    handleVideoCanPlay() {
      // Start playing video
      setTimeout(() => {
        this.playVideo();
      }, 500);
    }

    handleVideoError() {
      console.error("Failed to load video");

      // Show fallback image
      const fallbackImage = this.videoContainer?.querySelector(
        ".video-fallback-image",
      );
      if (fallbackImage) {
        fallbackImage.style.display = "block";
        if (this.video) {
          this.video.style.display = "none";
        }
      }

      // Hide video controls
      const controls = this.videoContainer?.querySelector(".video-controls");
      if (controls) {
        controls.style.display = "none";
      }
    }

    hideVideo() {
      if (this.video) {
        this.video.style.display = "none";
      }

      // Show fallback image
      const fallbackImage = this.videoContainer?.querySelector(
        ".video-fallback-image",
      );
      if (fallbackImage) {
        fallbackImage.style.display = "block";
      }

      const controls = this.videoContainer?.querySelector(".video-controls");
      if (controls) {
        controls.style.display = "none";
      }
    }

    showVideo() {
      if (this.video) {
        this.video.style.display = "block";
      }

      // Hide fallback image
      const fallbackImage = this.videoContainer?.querySelector(
        ".video-fallback-image",
      );
      if (fallbackImage) {
        fallbackImage.style.display = "none";
      }

      const controls = this.videoContainer?.querySelector(".video-controls");
      if (controls) {
        controls.style.display = "flex";
      }
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      new HeroVideoManager();
    });
  } else {
    new HeroVideoManager();
  }
})();
