/**
 * Footer JavaScript
 * Back to Top button and footer interactions
 * 
 * @package aaapos-prime
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    /**
     * Back to Top Button Functionality
     */
    class BackToTop {
        constructor() {
            this.button = document.querySelector('.back-to-top');
            
            if (!this.button) return;
            
            this.scrollThreshold = 300; // Show button after scrolling 300px
            this.isVisible = false;
            
            this.init();
        }
        
        init() {
            // Initial check
            this.toggleVisibility();
            
            // Listen to scroll events (throttled)
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                if (scrollTimeout) {
                    window.cancelAnimationFrame(scrollTimeout);
                }
                
                scrollTimeout = window.requestAnimationFrame(() => {
                    this.toggleVisibility();
                });
            }, { passive: true });
            
            // Click event
            this.button.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollToTop();
            });
            
            // Keyboard accessibility
            this.button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.scrollToTop();
                }
            });
        }
        
        toggleVisibility() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > this.scrollThreshold && !this.isVisible) {
                this.show();
            } else if (scrollTop <= this.scrollThreshold && this.isVisible) {
                this.hide();
            }
        }
        
        show() {
            this.button.classList.add('visible');
            this.button.setAttribute('aria-hidden', 'false');
            this.isVisible = true;
        }
        
        hide() {
            this.button.classList.remove('visible');
            this.button.setAttribute('aria-hidden', 'true');
            this.isVisible = false;
        }
        
        scrollToTop() {
            // Smooth scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // Focus on skip link or main content after scroll
            setTimeout(() => {
                const skipLink = document.querySelector('.skip-link');
                const mainContent = document.querySelector('main, #main, .site-main');
                
                if (skipLink) {
                    skipLink.focus();
                } else if (mainContent) {
                    mainContent.setAttribute('tabindex', '-1');
                    mainContent.focus();
                }
            }, 500);
        }
    }
    
    /**
     * Footer Links Animation
     */
    class FooterLinks {
        constructor() {
            this.links = document.querySelectorAll('.footer-menu a, .footer-nav a');
            
            if (this.links.length === 0) return;
            
            this.init();
        }
        
        init() {
            // Add hover effect class for better control
            this.links.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.classList.add('is-hovered');
                });
                
                link.addEventListener('mouseleave', function() {
                    this.classList.remove('is-hovered');
                });
            });
        }
    }
    
    /**
     * Social Links Tracking (Optional)
     */
    class SocialTracking {
        constructor() {
            this.socialLinks = document.querySelectorAll('.social-link');
            
            if (this.socialLinks.length === 0) return;
            
            this.init();
        }
        
        init() {
            this.socialLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    const platform = this.getPlatform(link);
                    this.trackClick(platform, link.href);
                });
            });
        }
        
        getPlatform(link) {
            // Extract platform from class name
            const classes = link.className.split(' ');
            for (let cls of classes) {
                if (cls.startsWith('social-')) {
                    return cls.replace('social-', '');
                }
            }
            return 'unknown';
        }
        
        trackClick(platform, url) {
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', 'social_click', {
                    'platform': platform,
                    'url': url,
                    'location': 'footer'
                });
            }
            
            // Facebook Pixel
            if (typeof fbq !== 'undefined') {
                fbq('trackCustom', 'SocialClick', {
                    platform: platform,
                    location: 'footer'
                });
            }
            
            // Custom event for other tracking tools
            document.dispatchEvent(new CustomEvent('socialClick', {
                detail: {
                    platform: platform,
                    url: url,
                    location: 'footer'
                }
            }));
        }
    }
    
    /**
     * Footer Reveal Animation on Scroll
     */
    class FooterReveal {
        constructor() {
            this.footer = document.querySelector('.site-footer');
            this.widgets = document.querySelectorAll('.footer-widget');
            
            if (!this.footer || this.widgets.length === 0) return;
            
            this.init();
        }
        
        init() {
            // Check if Intersection Observer is supported
            if (!('IntersectionObserver' in window)) {
                // Fallback: just show everything
                this.widgets.forEach(widget => {
                    widget.classList.add('is-visible');
                });
                return;
            }
            
            const options = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, options);
            
            this.widgets.forEach((widget, index) => {
                widget.style.transitionDelay = `${index * 0.1}s`;
                observer.observe(widget);
            });
        }
    }
    
    /**
     * Newsletter Form Handler (if exists)
     */
    class NewsletterForm {
        constructor() {
            this.form = document.querySelector('.footer-newsletter-form, .newsletter-form');
            
            if (!this.form) return;
            
            this.init();
        }
        
        init() {
            this.form.addEventListener('submit', (e) => {
                this.handleSubmit(e);
            });
        }
        
        handleSubmit(e) {
            e.preventDefault();
            
            const email = this.form.querySelector('input[type="email"]');
            const submitBtn = this.form.querySelector('button[type="submit"]');
            
            if (!email || !email.value) return;
            
            // Disable button during submission
            submitBtn.disabled = true;
            submitBtn.textContent = submitBtn.getAttribute('data-loading-text') || 'Subscribing...';
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                this.showMessage('success', 'Thank you for subscribing!');
                email.value = '';
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Subscribe';
            }, 1000);
        }
        
        showMessage(type, message) {
            // Remove existing message
            const existingMessage = this.form.querySelector('.form-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Create new message
            const messageEl = document.createElement('div');
            messageEl.className = `form-message form-message-${type}`;
            messageEl.textContent = message;
            messageEl.setAttribute('role', type === 'success' ? 'status' : 'alert');
            
            this.form.appendChild(messageEl);
            
            // Remove message after 5 seconds
            setTimeout(() => {
                messageEl.remove();
            }, 5000);
        }
    }
    
    /**
     * Smooth Scroll for Footer Links
     */
    function initSmoothScroll() {
        const internalLinks = document.querySelectorAll('.footer-menu a[href^="#"], .footer-nav a[href^="#"]');
        
        internalLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Only handle internal anchor links
                if (href && href.startsWith('#') && href !== '#') {
                    const target = document.querySelector(href);
                    
                    if (target) {
                        e.preventDefault();
                        
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Update URL without jumping
                        history.pushState(null, null, href);
                        
                        // Focus on target for accessibility
                        target.setAttribute('tabindex', '-1');
                        target.focus();
                    }
                }
            });
        });
    }
    
    /**
     * Initialize all footer functionality
     */
    function initFooter() {
        // Core functionality
        new BackToTop();
        new FooterLinks();
        new SocialTracking();
        new FooterReveal();
        new NewsletterForm();
        
        // Additional features
        initSmoothScroll();
        
        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('footerInitialized'));
    }
    
    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFooter);
    } else {
        initFooter();
    }
    
})();