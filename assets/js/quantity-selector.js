/**
 * Quantity Selector Enhancement
 * 
 * Adds plus/minus buttons to quantity inputs on single product pages ONLY
 * Cart page uses separate auto-update functionality
 * 
 * quantity-selector.js
 * @package AAAPOS_Prime
 * @version 1.0.1
 */

(function($) {
    'use strict';

    /**
     * Initialize Quantity Selectors
     * ONLY runs on non-cart pages
     */
    function initQuantitySelectors() {
        // CRITICAL: Skip entirely if on cart page
        const isCartPage = $('body').hasClass('woocommerce-cart');
        if (isCartPage) {
            return;
        }
        
        // Find all quantity inputs
        $('.quantity').each(function() {
            const $qty = $(this);
            const $input = $qty.find('.qty');
            
            // Skip if no input found or already processed
            if ($input.length === 0 || $qty.hasClass('buttons-added')) {
                return;
            }
            
            // Get min, max, and step values
            const min = parseFloat($input.attr('min')) || 1;
            const max = parseFloat($input.attr('max')) || 999;
            const step = parseFloat($input.attr('step')) || 1;
            
            // Wrap input if not already wrapped
            if (!$input.parent().hasClass('quantity-wrapper')) {
                $input.wrap('<div class="quantity-wrapper"></div>');
            }
            
            // Add minus button
            $input.before('<button type="button" class="minus qty-btn" aria-label="Decrease quantity">−</button>');
            
            // Add plus button
            $input.after('<button type="button" class="plus qty-btn" aria-label="Increase quantity">+</button>');
            
            // Mark as processed
            $qty.addClass('buttons-added');
            
            // Store values in data attributes
            $qty.data('min', min);
            $qty.data('max', max);
            $qty.data('step', step);
        });
    }

    /**
     * Handle Plus Button Click
     */
    function handlePlusClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $button = $(this);
        const $qty = $button.closest('.quantity');
        const $input = $qty.find('.qty');
        
        const currentVal = parseFloat($input.val()) || 0;
        const max = parseFloat($qty.data('max')) || 999;
        const step = parseFloat($qty.data('step')) || 1;
        
        // Calculate new value
        const newVal = currentVal + step;
        
        // Don't exceed max
        if (newVal <= max) {
            $input.val(newVal).trigger('change');
        }
    }

    /**
     * Handle Minus Button Click
     */
    function handleMinusClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $button = $(this);
        const $qty = $button.closest('.quantity');
        const $input = $qty.find('.qty');
        
        const currentVal = parseFloat($input.val()) || 0;
        const min = parseFloat($qty.data('min')) || 1;
        const step = parseFloat($qty.data('step')) || 1;
        
        // Calculate new value
        const newVal = currentVal - step;
        
        // Don't go below min
        if (newVal >= min) {
            $input.val(newVal).trigger('change');
        }
    }

    /**
     * Validate Input on Change
     */
    function validateQuantityInput() {
        const $input = $(this);
        const $qty = $input.closest('.quantity');
        
        let val = parseFloat($input.val());
        const min = parseFloat($qty.data('min')) || 1;
        const max = parseFloat($qty.data('max')) || 999;
        
        // Ensure value is a number
        if (isNaN(val) || val === '') {
            val = min;
        }
        
        // Clamp between min and max
        if (val < min) val = min;
        if (val > max) val = max;
        
        // Update input
        $input.val(val);
    }

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        // CRITICAL: Only initialize if NOT on cart page
        if (!$('body').hasClass('woocommerce-cart')) {
            initQuantitySelectors();
            
            // ONLY target .qty-btn buttons (our custom buttons)
            // This ensures we don't conflict with cart page buttons
            $(document).on('click.qtySelector', '.qty-btn.plus', handlePlusClick);
            $(document).on('click.qtySelector', '.qty-btn.minus', handleMinusClick);
            
            // Validate input on change
            $(document).on('change', '.quantity .qty', validateQuantityInput);
            
            // Re-initialize after AJAX updates (for variations on product page)
            $(document.body).on('found_variation', function() {
                setTimeout(initQuantitySelectors, 100);
            });
            
            $(document).on('reset_data', function() {
                setTimeout(initQuantitySelectors, 100);
            });
        }
    });

})(jQuery);