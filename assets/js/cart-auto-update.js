/**
 * Auto-update Cart on Quantity Change
 * Automatically updates cart when quantity changes
 * 
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

    // Hide update cart button (optional - if you want to completely hide it)
    $(document).ready(function() {
        $('[name="update_cart"]').hide();
    });

    // Remove loading state after cart updates
    $(document.body).on('updated_cart_totals', function() {
        $('.woocommerce-cart-form').removeClass('cart-updating');
    });

})(jQuery);