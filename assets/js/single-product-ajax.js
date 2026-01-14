/**
 * AJAX Add to Cart for Single Product Pages
 * Prevents page refresh and enables cart notification
 * FULLY WORKING FOR SIMPLE AND VARIABLE PRODUCTS
 * 
 * @package AAAPOS_Prime
 * @version 2.2.0 - COMPLETE FIX FOR VARIABLE PRODUCTS
 */

(function($) {
    'use strict';

    // Skip on My Account pages
    if ($('body').hasClass('woocommerce-account')) {
        return;
    }

    /**
     * Handle both simple and variable product add to cart
     */
    $(document).on('submit', 'form.cart', function(e) {
        const $form = $(this);
        const $button = $form.find('.single_add_to_cart_button');
        
        // Skip if already processing
        if ($form.data('processing')) {
            e.preventDefault();
            return false;
        }
        
        // Skip if button is disabled
        if ($button.hasClass('disabled') || $button.prop('disabled')) {
            return true;
        }
        
        // Check if it's a variable product
        const isVariableProduct = $form.hasClass('variations_form');
        
        // For variable products, check if variation is selected
        if (isVariableProduct) {
            const variationId = $form.find('input[name="variation_id"]').val();
            
            // If no variation selected, let WooCommerce show the error
            if (!variationId || variationId === '0' || variationId === '') {
                return true; // Allow default form submission (shows error)
            }
        }
        
        // Prevent default form submission
        e.preventDefault();
        e.stopPropagation();
        
        // Mark as processing to prevent double submission
        $form.data('processing', true);
        
        // Show loading state
        $button.prop('disabled', true).addClass('loading');
        const originalText = $button.html();
        $button.html('Adding...');
        
        // Prepare form data
        const formData = $form.serialize();
        
        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: formData + '&action=woocommerce_ajax_add_to_cart',
            dataType: 'json',
            success: function(response) {
                if (!response) {
                    return;
                }
                
                if (response.error) {
                    // Show error message
                    if (response.product_url) {
                        window.location = response.product_url;
                    } else {
                        alert(response.message || 'An error occurred');
                        $button.html(originalText).removeClass('loading').prop('disabled', false);
                    }
                    return;
                }
                
                // Success! Update cart fragments
                if (response.fragments) {
                    // Method 1: Direct replacement
                    $.each(response.fragments, function(key, value) {
                        var $element = $(key);
                        if ($element.length) {
                            $element.replaceWith(value);
                        }
                    });
                    
                    // Method 2: Store in sessionStorage for WooCommerce
                    try {
                        sessionStorage.setItem('wc_fragments', JSON.stringify(response.fragments));
                        sessionStorage.setItem('wc_cart_hash', response.cart_hash);
                    } catch(e) {
                        // sessionStorage not available
                    }
                }
                
                // Trigger WooCommerce events in correct order
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                $(document.body).trigger('wc_fragment_refresh');
                $(document.body).trigger('wc_fragments_refreshed');
                
                // Show success state
                $button.html('Added!').removeClass('loading').addClass('added');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.html(originalText).removeClass('added').prop('disabled', false);
                    $form.data('processing', false); // Reset processing flag
                }, 2000);
                
                // Optional: Reset variations after adding (uncomment if needed)
                /*
                if (isVariableProduct) {
                    setTimeout(function() {
                        $form.find('.variations select').val('').trigger('change');
                        $('.color-swatches .color-swatch').removeClass('selected');
                        $('.size-buttons .size-button').removeClass('selected');
                        $form.data('processing', false);
                    }, 2500);
                }
                */
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                
                // Reset processing flag
                $form.data('processing', false);
                
                // Reset button
                $button.html(originalText).removeClass('loading').prop('disabled', false);
                
                // Fallback: submit form normally
                $form.off('submit').submit();
            }
        });
        
        return false;
    });

})(jQuery);