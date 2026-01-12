/**
 * Quantity Selector Enhancement
 * 
 * Adds plus/minus buttons to quantity inputs on single product pages ONLY
 * Cart page uses separate auto-update functionality
 * 
 * quantity-selector.js
 * @package AAAPOS_Prime
 * @version 1.0.2
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
        $('.quantity:not(.buttons-added)').each(function() {
            const $qty = $(this);
            const $input = $qty.find('input.qty, input[type="number"]');
            
            // Skip if no input found or already processed
            if ($input.length === 0 || $qty.hasClass('buttons-added')) {
                return;
            }
            
            // Get min, max, and step values
            const min = parseFloat($input.attr('min')) || 1;
            const max = parseFloat($input.attr('max')) || 999;
            const step = parseFloat($input.attr('step')) || 1;
            
            // Create wrapper if it doesn't exist
            if (!$input.parent().hasClass('quantity-wrapper')) {
                $input.wrap('<div class="quantity-wrapper"></div>');
            }
            
            const $wrapper = $input.parent('.quantity-wrapper');
            
            // Add minus button (before input)
            if ($wrapper.find('.minus').length === 0) {
                $wrapper.prepend('<button type="button" class="minus qty-btn" aria-label="Decrease quantity">−</button>');
            }
            
            // Add plus button (after input)
            if ($wrapper.find('.plus').length === 0) {
                $wrapper.append('<button type="button" class="plus qty-btn" aria-label="Increase quantity">+</button>');
            }
            
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
        const $input = $qty.find('input.qty, input[type="number"]');
        
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
        const $input = $qty.find('input.qty, input[type="number"]');
        
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
            // Initial setup
            initQuantitySelectors();
            
            // Event delegation for dynamically added buttons
            $(document).on('click', '.qty-btn.plus', handlePlusClick);
            $(document).on('click', '.qty-btn.minus', handleMinusClick);
            
            // Validate input on change
            $(document).on('change', '.quantity input.qty, .quantity input[type="number"]', validateQuantityInput);
            
            // Re-initialize after AJAX updates (for variations on product page)
            $(document.body).on('found_variation', function() {
                setTimeout(initQuantitySelectors, 100);
            });
            
            $(document.body).on('reset_data', function() {
                setTimeout(initQuantitySelectors, 100);
            });
            
            // Also check after DOM updates
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        $(mutation.addedNodes).find('.quantity').each(function() {
                            if (!$(this).hasClass('buttons-added')) {
                                initQuantitySelectors();
                            }
                        });
                    }
                });
            });
            
            // Observe the product form for changes
            const $productForm = $('form.cart');
            if ($productForm.length) {
                observer.observe($productForm[0], {
                    childList: true,
                    subtree: true
                });
            }
        }
    });

})(jQuery);