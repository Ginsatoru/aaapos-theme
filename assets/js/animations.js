/**
 * Scroll Animations & Interactions
 * animations.js
 * High-performance animation system using IntersectionObserver.
 * No scroll event listeners for better performance.
 * 
 * @package Macedon_Ranges
 */

class MRAnimations {
    constructor() {
        this.config = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px',
            once: true // Animate only once
        };
        
        this.init();
    }

    init() {
        // Always initialize header animations on page load
        this.initHeaderAnimations();
        
        // Initialize other animations only if enabled
        if (document.body.classList.contains('animations-enabled')) {
            this.initScrollAnimations();
            this.initBackToTop();
            this.initCountdowns();
        }
    }

    /**
     * Initialize header animations on page load
     * These run immediately when the page loads
     */
    initHeaderAnimations() {
        // Wait for DOM to be fully ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.animateHeaderElements();
            });
        } else {
            this.animateHeaderElements();
        }
    }

    /**
     * Animate header elements immediately on page load
     */
    animateHeaderElements() {
        // Select all header elements with data-animate attribute
        const headerElements = document.querySelectorAll('.site-header [data-animate]');
        
        headerElements.forEach(element => {
            const delay = parseInt(element.dataset.animateDelay) || 0;
            
            setTimeout(() => {
                element.classList.add('animated');
                
                // Clean up after animation completes
                element.addEventListener('animationend', () => {
                    element.style.willChange = 'auto';
                }, { once: true });
            }, delay);
        });
    }

    /**
     * Initialize IntersectionObserver for scroll animations
     */
    initScrollAnimations() {
        if (!('IntersectionObserver' in window)) {
            // Fallback: show all animated elements
            document.querySelectorAll('[data-animate]').forEach(el => {
                el.style.opacity = '1';
            });
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateElement(entry.target);
                    
                    if (this.config.once) {
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, this.config);

        // Observe all elements with data-animate attribute (except header elements)
        document.querySelectorAll('[data-animate]').forEach(el => {
            // Skip header elements as they're animated on page load
            if (!el.closest('.site-header')) {
                observer.observe(el);
            }
        });
    }

    /**
     * Animate element with optional delay
     */
    animateElement(element) {
        const delay = parseInt(element.dataset.animateDelay) || 0;
        
        setTimeout(() => {
            element.classList.add('animated');
            
            // Clean up after animation completes
            element.addEventListener('animationend', () => {
                element.style.willChange = 'auto';
            }, { once: true });
        }, delay);
    }

    /**
     * Back to top button
     */
    initBackToTop() {
        const button = document.querySelector('.back-to-top');
        if (!button) return;

        let ticking = false;

        const toggleButton = () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    if (window.pageYOffset > 300) {
                        button.classList.add('visible');
                    } else {
                        button.classList.remove('visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        };

        window.addEventListener('scroll', toggleButton, { passive: true });
        toggleButton();

        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Countdown timers
     */
    initCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');
        if (!timers.length) return;

        timers.forEach(timer => {
            const endDate = timer.dataset.endDate;
            if (endDate) {
                this.startCountdown(timer, new Date(endDate).getTime());
            }
        });
    }

    startCountdown(element, endTime) {
        const update = () => {
            const now = Date.now();
            const distance = endTime - now;

            if (distance < 0) {
                element.innerHTML = '<div class="countdown-expired">Offer expired</div>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const daysEl = element.querySelector('.countdown-value.days');
            const hoursEl = element.querySelector('.countdown-value.hours');
            const minutesEl = element.querySelector('.countdown-value.minutes');
            const secondsEl = element.querySelector('.countdown-value.seconds');

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        };

        update();
        setInterval(update, 1000);
    }

    /**
     * Show notification message
     */
    static showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `cart-message cart-message--${type}`;
        notification.innerHTML = `
            <div class="cart-message__content">
                <span>${message}</span>
                <button class="cart-message__close" aria-label="Close">&times;</button>
            </div>
        `;

        document.body.appendChild(notification);

        // Trigger reflow for animation
        notification.offsetHeight;
        notification.classList.add('visible');

        const closeBtn = notification.querySelector('.cart-message__close');
        const close = () => {
            notification.classList.remove('visible');
            setTimeout(() => notification.remove(), 300);
        };

        closeBtn.addEventListener('click', close);

        if (duration > 0) {
            setTimeout(close, duration);
        }
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new MRAnimations());
} else {
    new MRAnimations();
}

// Export for use in other scripts
window.MRAnimations = MRAnimations;