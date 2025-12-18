/**
 * Category Filter - Drag Scroll Functionality (FINAL FIX v3.2)
 * File: category-filter-drag.js
 * Location: assets/js/
 * Version: 3.2.0
 * 
 * BOTH drag and click work perfectly!
 * Key: Only add is-dragging class when actually dragging
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
            }
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
    }
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDragScroll);
    } else {
        initDragScroll();
    }
})();