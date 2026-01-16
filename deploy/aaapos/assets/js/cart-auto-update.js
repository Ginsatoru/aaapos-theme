/**
 * Auto-update Cart on Quantity Change
 * Automatically updates cart when quantity changes
 * UPDATED: Extended notification timing without page fade
 * cart-auto-update.js
 * @package Macedon_Ranges
 */

(function($) {
    'use strict';

    // Auto-update cart when quantity changes
    let updateTimer;
    
    $(document.body).on('change', 'input.qty', function() {
        clearTimeout(updateTimer);
        
        // Show loading state
        const $form = $(this).closest('form.woocommerce-cart-form');
        $form.addClass('cart-updating');
        
        // Add a small delay to avoid too many requests
        updateTimer = setTimeout(function() {
            $('[name="update_cart"]').prop('disabled', false);
            $('[name="update_cart"]').trigger('click');
        }, 500);
    });

    // Auto-apply coupon when user presses Enter or clicks apply
    $(document.body).on('click', '[name="apply_coupon"]', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const $input = $('#coupon_code');
        const couponCode = $input.val();
        
        if (!couponCode) {
            return;
        }
        
        // Show loading state
        $button.prop('disabled', true).text('Applying...');
        
        // Submit the form
        $button.closest('form').submit();
    });

    // Auto-apply coupon on Enter key
    $(document.body).on('keypress', '#coupon_code', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            $('[name="apply_coupon"]').trigger('click');
        }
    });

    // Hide update cart button
    $(document).ready(function() {
        $('[name="update_cart"]').hide();
        
        // Check if we're on empty cart page
        if ($('.cart-empty-wrapper').length > 0 || 
            $('.wc-empty-cart-message').length > 0 || 
            $('.cart-empty').length > 0) {
            
            // Mark body as having empty cart
            $('body').addClass('cart-is-currently-empty');
            
            // Prevent any fragments from updating by intercepting them
            $(document.body).on('wc_fragment_refresh wc_fragments_refreshed', function(e) {
                if ($('body').hasClass('cart-is-currently-empty')) {
                    e.stopImmediatePropagation();
                    return false;
                }
            });
        }
    });

    // Remove loading state after cart updates
    $(document.body).on('updated_cart_totals', function() {
        $('.woocommerce-cart-form').removeClass('cart-updating');
    });

    // CRITICAL: Reload page when product added to empty cart
    // Extended timing to let notification complete its full display cycle
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        if ($('body').hasClass('cart-is-currently-empty') && $('body').hasClass('woocommerce-cart')) {
            // Stop the event from propagating
            event.stopImmediatePropagation();
            
            // Wait for notification to fully display and complete its animation cycle
            // Notification appears (0s) -> displays (2s) -> auto-fade starts (2s) -> completes (2.3s)
            setTimeout(function() {
                window.location.reload();
            }, 2500);
            
            // Return false to prevent any further processing
            return false;
        }
    });

})(jQuery);