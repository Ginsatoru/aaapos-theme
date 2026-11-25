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
            this.splitHeadings();
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
        const containers = Array.from(document.querySelectorAll('.top-bar [data-animate], .site-header [data-animate]'));
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduce || !Element.prototype.animate) {
            containers.forEach(el => el.classList.add('animated'));
            return;
        }

        containers.forEach(el => this.prepareHeaderItem(el));

        // One left-to-right sequence across the top bar and header: every word
        // and icon gets the next slot, so nothing shows up out of order
        const STEP = 55;
        const DURATION = 700;
        const EASE = 'cubic-bezier(0.77, 0, 0.175, 1)';
        const HEADER_START = 100;
        const units = Array.from(document.querySelectorAll('.top-bar .w-word, .top-bar .w-atom, .site-header .w-word, .site-header .w-atom'))
            .filter(u => u.closest('[data-animate]') && !u.closest('.sub-menu, .account-dropdown, .cart-dropdown'));

        const play = (el, frames, delay, pseudoElement) => {
            if (!pseudoElement) el.style.transition = 'none';
            const anim = el.animate(frames, { duration: DURATION, delay, easing: EASE, fill: 'backwards', pseudoElement });
            if (!pseudoElement) anim.onfinish = () => { el.style.transition = ''; };
        };

        // Top bar and header run side by side, each in its own left-to-right order
        let topIndex = 0;
        let headerIndex = 0;
        units.forEach(unit => {
            const delay = unit.closest('.top-bar') ? topIndex++ * STEP : HEADER_START + headerIndex++ * STEP;
            if (unit.classList.contains('w-word')) {
                play(unit, [{ transform: 'translateY(110%)', opacity: 0 }, { transform: 'translateY(0)', opacity: 1 }], delay);
            } else {
                play(unit, [
                    { transform: 'translateY(110%)', clipPath: 'inset(0 0 110% 0)' },
                    { transform: 'translateY(0)', clipPath: 'inset(0 0 0 0)' }
                ], delay);
            }
            unit.dataset.wDelay = delay;
        });

        // Dropdown arrows rise with the last word of their menu item
        document.querySelectorAll('.site-header .nav-menu > li.menu-item-has-children > a').forEach(link => {
            const words = link.querySelectorAll('.w-word');
            if (!words.length) return;
            const delay = parseInt(words[words.length - 1].dataset.wDelay, 10) || 0;
            try {
                play(link, [{ transform: 'translateY(8px)', opacity: 0 }, { transform: 'translateY(0)', opacity: 1 }], delay, '::after');
            } catch (e) {}
        });

        containers.forEach(el => el.classList.add('animated'));
    }

    /**
     * Wrap each word of root's text in a mask so it rises out of its own line
     * (the Work.jsx SliceText mechanic). Returns the next word index.
     */
    wrapWords(root, skip, index = 0, step = 0.055) {
        Array.from(root.childNodes).forEach(child => {
            if (child.nodeType === Node.TEXT_NODE) {
                if (!child.textContent.trim()) return;
                const frag = document.createDocumentFragment();
                child.textContent.split(/(\s+)/).forEach(part => {
                    if (!part) return;
                    if (/^\s+$/.test(part)) {
                        frag.appendChild(document.createTextNode(part));
                        return;
                    }
                    const wrap = document.createElement('span');
                    const word = document.createElement('span');
                    wrap.className = 'w-word-wrap';
                    word.className = 'w-word';
                    word.style.transitionDelay = (index++ * step) + 's';
                    word.textContent = part;
                    wrap.appendChild(word);
                    frag.appendChild(wrap);
                });
                child.replaceWith(frag);
            } else if (child.nodeType === Node.ELEMENT_NODE && !child.matches(skip)) {
                index = this.wrapWords(child, skip, index, step);
            }
        });
        return index;
    }

    /**
     * Header and top bar: words rise from their own baseline, icons, logo,
     * search box and buttons rise from their own bottom edge
     */
    prepareHeaderItem(element) {
        if (element.classList.contains('w-split')) return;
        const atoms = '.icon-roll, img, .search-form, .btn-header-cta, .btn-auth, .mobile-menu-toggle, .announce-icon, .top-bar-item > svg';
        const skip = atoms + ', svg, script, style, .sub-menu, .cart-count, .screen-reader-text, .sr-only, .account-dropdown, .cart-dropdown';

        this.wrapWords(element, skip, 0, 0.05);
        (element.matches(atoms) ? [element] : element.querySelectorAll(atoms)).forEach(atom => {
            if (!atom.closest('.sub-menu, .account-dropdown, .cart-dropdown')) atom.classList.add('w-atom');
        });
        element.classList.add('w-split');
    }

    /**
     * Word-slice headings (same as Work.jsx)
     */
    splitHeadings() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        document.querySelectorAll('h1[data-animate], h2[data-animate], h3[data-animate]').forEach(heading => {
            // .error-title (404 digits) uses background-clip:text, which word-wrapping breaks
            if (heading.closest('.site-header, .top-bar') || heading.classList.contains('w-split') || heading.classList.contains('error-title')) return;
            this.wrapWords(heading, 'br, svg, img');
            heading.classList.add('w-split');
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
            if (!el.closest('.site-header, .top-bar')) {
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