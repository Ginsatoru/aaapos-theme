/**
 * Quantity Selector Enhancement
 * 
 * Adds plus/minus buttons to quantity inputs on single product pages
 * Works with both simple and variable products
 * 
 * @package AAAPOS_Prime
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Initialize Quantity Selectors
     */
    function initQuantitySelectors() {
        // Find all quantity inputs that don't already have buttons
        $('.quantity:not(.buttons-added)').each(function() {
            const $qty = $(this);
            const $input = $qty.find('.qty');
            
            // Skip if no input found
            if ($input.length === 0) {
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
        if (val < min) {
            val = min;
        }
        if (val > max) {
            val = max;
        }
        
        // Update input
        $input.val(val);
    }

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        // Initialize quantity selectors
        initQuantitySelectors();
        
        // Event delegation for plus/minus buttons
        $(document).on('click', '.quantity .plus', handlePlusClick);
        $(document).on('click', '.quantity .minus', handleMinusClick);
        
        // Validate input on change
        $(document).on('change', '.quantity .qty', validateQuantityInput);
        
        // Re-initialize after AJAX updates (for variations)
        $(document.body).on('updated_cart_totals', initQuantitySelectors);
        $(document.body).on('wc_fragments_loaded', initQuantitySelectors);
        $(document.body).on('wc_fragments_refreshed', initQuantitySelectors);
    });

    /**
     * Initialize after variation is selected
     */
    $(document).on('found_variation', function() {
        setTimeout(initQuantitySelectors, 100);
    });

    /**
     * Re-initialize when variations are reset
     */
    $(document).on('reset_data', function() {
        setTimeout(initQuantitySelectors, 100);
    });

})(jQuery);