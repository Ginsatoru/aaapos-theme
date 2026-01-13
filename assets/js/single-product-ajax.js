/**
 * AJAX Add to Cart for Single Product Pages
 * Prevents page refresh and enables cart notification
 * 
 * @package AAAPOS_Prime
 * @version 1.0.1 - FIXED
 */

(function($) {
    'use strict';

    // Skip on My Account pages
    if ($('body').hasClass('woocommerce-account')) {
        return;
    }

    /**
     * AJAX Add to Cart for Single Products
     */
    $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function(e) {
        const $button = $(this);
        const $form = $button.closest('form.cart');
        
        // Skip if it's not a simple product or if product has required options
        if ($form.hasClass('variations_form')) {
            return; // Let WooCommerce handle variable products
        }

        // Check if product is purchasable
        if ($button.hasClass('disabled') || $button.hasClass('wc-variation-selection-needed')) {
            return;
        }

        e.preventDefault();

        // Disable button and show loading state
        $button.prop('disabled', true).addClass('loading');
        
        const originalText = $button.text();
        $button.text('Adding...');

        // Get form data
        const formData = new FormData($form[0]);
        
        // Make sure we have product_id
        let productId = formData.get('add-to-cart') || formData.get('product_id');
        
        if (!productId) {
            productId = $form.find('[name="add-to-cart"]').val() || 
                        $form.find('[name="product_id"]').val();
        }

        const quantity = formData.get('quantity') || 1;

        // Send AJAX request using standard WooCommerce method
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                if (!response) {
                    // Fallback to regular form submission
                    $form.off('submit').submit();
                    return;
                }

                if (response.error && response.product_url) {
                    window.location = response.product_url;
                    return;
                }

                // Success! Trigger WooCommerce fragments refresh
                $(document.body).trigger('wc_fragment_refresh');
                
                // Trigger added_to_cart event
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                // Button success state
                $button.text('Added!').removeClass('loading').addClass('added');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.text(originalText).removeClass('added').prop('disabled', false);
                }, 2000);
            },
            error: function() {
                // On error, just submit the form normally
                $button.text(originalText).removeClass('loading').prop('disabled', false);
                $form.off('submit').submit();
            }
        });

        return false;
    });

})(jQuery);