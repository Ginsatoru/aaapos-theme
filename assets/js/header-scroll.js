/**
 * Header Scroll Behavior
 * Hides topbar and adjusts header position on scroll
 * Handles admin bar spacing for logged-in users
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        
        const body = document.body;
        const header = document.querySelector('.site-header');
        const topbar = document.querySelector('.top-bar');
        const isAdminBar = body.classList.contains('admin-bar');
        
        // CRITICAL: Check if topbar actually exists and is visible
        const hasTopbar = topbar !== null;
        
        if (!header) return;
        
        let lastScrollTop = 0;
        let ticking = false;
        
        function updateHeader(scrollTop) {
            const isMobile = window.innerWidth < 768;
            const isMobileAdminBar = window.innerWidth <= 782;
            
            // Add scrolled class after 50px
            if (scrollTop > 50) {
                body.classList.add('scrolled');
                if (header) {
                    header.classList.add('is-sticky');
                }
                
                // Hide topbar on scroll (desktop only) - only if it exists
                if (hasTopbar && topbar && !isMobile) {
                    topbar.style.transform = 'translateY(-100%)';
                }
                
                // Adjust header position when scrolled
                if (isMobile) {
                    // MOBILE BEHAVIOR
                    if (isAdminBar) {
                        // Mobile with admin bar - stick below admin bar
                        header.style.top = '46px';
                    } else {
                        // Mobile without admin bar - stick to top
                        header.style.top = '0';
                    }
                } else {
                    // DESKTOP BEHAVIOR
                    if (isAdminBar) {
                        // Desktop admin bar (32px)
                        header.style.top = '32px';
                    } else {
                        // No admin bar - stick to top
                        header.style.top = '0';
                    }
                }
                
            } else {
                // AT TOP OF PAGE
                body.classList.remove('scrolled');
                if (header) {
                    header.classList.remove('is-sticky');
                }
                
                // Show topbar when at top (desktop only) - only if it exists
                if (hasTopbar && topbar && !isMobile) {
                    topbar.style.transform = 'translateY(0)';
                }
                
                // Reset header position to original
                if (isMobile) {
                    // MOBILE - No topbar
                    if (isAdminBar) {
                        header.style.top = '46px';
                    } else {
                        header.style.top = '0';
                    }
                } else {
                    // DESKTOP
                    if (isAdminBar && hasTopbar) {
                        // Desktop: admin bar (32px) + topbar (45px)
                        header.style.top = '77px';
                    } else if (isAdminBar && !hasTopbar) {
                        // Desktop: only admin bar (32px)
                        header.style.top = '32px';
                    } else if (!isAdminBar && hasTopbar) {
                        // Desktop: only topbar (45px)
                        header.style.top = '45px';
                    } else {
                        // Desktop: no admin bar, no topbar
                        header.style.top = '0';
                    }
                }
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
        
        function onResize() {
            // Recalculate header position on resize
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
        
        // Listen to resize events (for responsive adjustments)
        window.addEventListener('resize', onResize, { passive: true });
        
        // Check initial state
        updateHeader(window.pageYOffset || document.documentElement.scrollTop);
        
    });
    
})();