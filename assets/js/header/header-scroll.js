/**
 * Header Scroll Behavior
 * Simplified - CSS now handles all positioning
 * JavaScript only manages scrolled class and topbar visibility
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        
        const body = document.body;
        const header = document.querySelector('.site-header');
        const topbar = document.querySelector('.top-bar');
        
        // Check if sticky header is enabled
        const isStickyEnabled = body.classList.contains('has-sticky-header');
        
        // If sticky is disabled, don't run any scroll logic
        if (!isStickyEnabled) {
            console.log('Sticky header disabled - scroll behavior inactive');
            return;
        }
        
        // Check if topbar exists
        const hasTopbar = topbar !== null;
        
        if (!header) return;
        
        let lastScrollTop = 0;
        let ticking = false;
        
        function updateHeader(scrollTop) {
            // Add scrolled class after 50px
            if (scrollTop > 50) {
                body.classList.add('scrolled');
                header.classList.add('is-sticky');
            } else {
                body.classList.remove('scrolled');
                header.classList.remove('is-sticky');
            }
            
            lastScrollTop = scrollTop;
        }
        
        function onScroll() {
            lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateHeader(lastScrollTop);
                    ticking = false;
                });
                
                ticking = true;
            }
        }
        
        // Listen to scroll events
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Check initial state
        updateHeader(window.pageYOffset || document.documentElement.scrollTop);
        
    });
    
})();