/**
 * Category Filter - Drag Scroll Functionality with Gradient Overlays
 * File: category-filter-drag.js
 * Location: assets/js/
 * Version: 4.0.0
 * 
 * Features:
 * - Drag to scroll with smooth gesture detection
 * - Dynamic gradient overlays (left/right) based on scroll position
 * - Both drag and click work perfectly
 * - Pointer-events: none on overlays to allow clicking through
 */

(function() {
    'use strict';
    
    function initDragScroll() {
        const container = document.querySelector('.category-filter-buttons');
        
        if (!container) {
            return;
        }
        
        let isDown = false;
        let isDragging = false;
        let startX;
        let scrollLeft;
        let moveDistance = 0;
        
        // Drag threshold
        const DRAG_THRESHOLD = 5;
        
        // Create gradient overlays
        createGradientOverlays(container);
        
        // Update overlays on initial load
        updateGradientOverlays(container);
        
        // Prevent drag ghost images on links
        const links = container.querySelectorAll('a');
        links.forEach(function(link) {
            link.addEventListener('dragstart', function(e) {
                e.preventDefault();
                return false;
            });
        });
        
        // Mouse down - DON'T add is-dragging class yet!
        container.addEventListener('mousedown', function(e) {
            isDown = true;
            isDragging = false;
            moveDistance = 0;
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
            
            // Change cursor but DON'T add is-dragging class yet
            container.style.cursor = 'grabbing';
            
            // Prevent text selection
            e.preventDefault();
        });
        
        // Mouse leave
        container.addEventListener('mouseleave', function() {
            isDown = false;
            isDragging = false;
            moveDistance = 0;
            container.classList.remove('is-dragging');
            container.style.cursor = 'grab';
        });
        
        // Mouse up
        container.addEventListener('mouseup', function() {
            isDown = false;
            container.classList.remove('is-dragging');
            container.style.cursor = 'grab';
            
            // Delay reset to allow click handler to check isDragging
            setTimeout(function() {
                isDragging = false;
                moveDistance = 0;
            }, 10);
        });
        
        // Mouse move - ONLY add is-dragging when actually moving
        container.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            
            e.preventDefault();
            
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 1.5;
            moveDistance = Math.abs(x - startX);
            
            // Only when moved beyond threshold
            if (moveDistance > DRAG_THRESHOLD) {
                if (!isDragging) {
                    isDragging = true;
                    // NOW add the class - this enables pointer-events: none
                    container.classList.add('is-dragging');
                }
                
                // Perform scroll
                container.scrollLeft = scrollLeft - walk;
                
                // Update gradient overlays during scroll
                updateGradientOverlays(container);
            }
        });
        
        // Scroll event - Update overlays when scrolling via other means
        container.addEventListener('scroll', function() {
            updateGradientOverlays(container);
        });
        
        // Click handler - block if dragged
        container.addEventListener('click', function(e) {
            if (isDragging && moveDistance > DRAG_THRESHOLD) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
        
        // Touch support
        let touchStartX = 0;
        let touchStartY = 0;
        let touchScrollLeft = 0;
        let touchIsDragging = false;
        let touchMoveDistance = 0;
        
        container.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].pageX;
            touchStartY = e.touches[0].pageY;
            touchScrollLeft = container.scrollLeft;
            touchIsDragging = false;
            touchMoveDistance = 0;
        }, { passive: true });
        
        container.addEventListener('touchmove', function(e) {
            const touchX = e.touches[0].pageX;
            const touchY = e.touches[0].pageY;
            const walkX = touchStartX - touchX;
            const walkY = Math.abs(touchStartY - touchY);
            
            touchMoveDistance = Math.abs(walkX);
            
            // Horizontal scroll if moving more horizontally
            if (touchMoveDistance > walkY && touchMoveDistance > DRAG_THRESHOLD) {
                touchIsDragging = true;
                container.scrollLeft = touchScrollLeft + walkX;
                
                // Update gradient overlays during touch scroll
                updateGradientOverlays(container);
            }
        }, { passive: true });
        
        container.addEventListener('touchend', function() {
            setTimeout(function() {
                touchIsDragging = false;
                touchMoveDistance = 0;
            }, 10);
        }, { passive: true });
        
        // Touch click prevention
        container.addEventListener('click', function(e) {
            if (touchIsDragging && touchMoveDistance > DRAG_THRESHOLD) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
        
        // Update overlays on window resize
        window.addEventListener('resize', function() {
            updateGradientOverlays(container);
        });
    }
    
    /**
     * Create gradient overlay elements
     */
    function createGradientOverlays(container) {
        const wrapper = container.parentElement;
        
        // Check if overlays already exist
        if (wrapper.querySelector('.category-filter-overlay')) {
            return;
        }
        
        // Create left overlay
        const leftOverlay = document.createElement('div');
        leftOverlay.className = 'category-filter-overlay category-filter-overlay--left';
        leftOverlay.setAttribute('aria-hidden', 'true');
        
        // Create right overlay
        const rightOverlay = document.createElement('div');
        rightOverlay.className = 'category-filter-overlay category-filter-overlay--right';
        rightOverlay.setAttribute('aria-hidden', 'true');
        
        // Append overlays to wrapper
        wrapper.appendChild(leftOverlay);
        wrapper.appendChild(rightOverlay);
    }
    
    /**
     * Update gradient overlays based on scroll position
     */
    function updateGradientOverlays(container) {
        const wrapper = container.parentElement;
        const leftOverlay = wrapper.querySelector('.category-filter-overlay--left');
        const rightOverlay = wrapper.querySelector('.category-filter-overlay--right');
        
        if (!leftOverlay || !rightOverlay) {
            return;
        }
        
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;
        const maxScroll = scrollWidth - clientWidth;
        
        // Tolerance for floating point calculations (1px)
        const tolerance = 1;
        
        // Check if content overflows
        const hasOverflow = scrollWidth > clientWidth;
        
        if (!hasOverflow) {
            // No overflow - hide both overlays
            leftOverlay.classList.remove('visible');
            leftOverlay.classList.add('hidden');
            rightOverlay.classList.remove('visible');
            rightOverlay.classList.add('hidden');
            return;
        }
        
        // Left overlay: Show when scrolled away from start
        if (scrollLeft > tolerance) {
            leftOverlay.classList.add('visible');
            leftOverlay.classList.remove('hidden');
        } else {
            leftOverlay.classList.remove('visible');
            leftOverlay.classList.add('hidden');
        }
        
        // Right overlay: Show when not at end
        if (scrollLeft < (maxScroll - tolerance)) {
            rightOverlay.classList.add('visible');
            rightOverlay.classList.remove('hidden');
        } else {
            rightOverlay.classList.remove('visible');
            rightOverlay.classList.add('hidden');
        }
    }
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDragScroll);
    } else {
        initDragScroll();
    }
})();