/**
 * AJAX Add to Cart for Single Product Pages
 * FIXED VERSION - Cart count now updates properly
 * 
 * @package AAAPOS_Prime
 * @version 2.3.0 - CART COUNT UPDATE FIX
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
                // Trigger WooCommerce validation
                $button.removeClass('loading');
                return true; // Allow default form submission (shows error)
            }
            
            // Additional check: make sure all required attributes are selected
            let allSelected = true;
            $form.find('.variations select').each(function() {
                if ($(this).prop('required') && !$(this).val()) {
                    allSelected = false;
                }
            });
            
            if (!allSelected) {
                return true; // Let WooCommerce show error
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
        
        // Prepare form data - USE CORRECT ENDPOINT
        const formData = new FormData($form[0]);
        
        // Get product ID
        const productId = $form.find('input[name="product_id"]').val() || 
                         $form.find('button[name="add-to-cart"]').val();
        
        // Build data object for WooCommerce AJAX endpoint
        const data = {
            product_id: productId,
            quantity: $form.find('input[name="quantity"]').val() || 1
        };
        
        // Add variation data if variable product
        if (isVariableProduct) {
            data.variation_id = $form.find('input[name="variation_id"]').val();
            
            // Add variation attributes
            $form.find('select[name^="attribute_"]').each(function() {
                const attrName = $(this).attr('name');
                data[attrName] = $(this).val();
            });
        }
        
        // Send AJAX request using WooCommerce's proper endpoint
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
            data: data,
            dataType: 'json',
            success: function(response) {
                if (!response) {
                    resetButton();
                    return;
                }
                
                if (response.error) {
                    // Show error message
                    if (response.product_url) {
                        window.location = response.product_url;
                    } else {
                        alert(response.message || 'An error occurred');
                        resetButton();
                    }
                    return;
                }
                
                // SUCCESS! Now properly update cart fragments
                if (response.fragments) {
                    // Update each fragment on the page
                    $.each(response.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                    
                    // Store fragments in sessionStorage for persistence
                    try {
                        sessionStorage.setItem('wc_fragments', JSON.stringify(response.fragments));
                        sessionStorage.setItem('wc_cart_hash', response.cart_hash || '');
                    } catch(e) {
                        console.log('sessionStorage not available');
                    }
                }
                
                // Trigger WooCommerce fragment refresh event
                $(document.body).trigger('wc_fragments_refreshed');
                
                // Trigger added_to_cart event for notifications
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                // Show success state
                $button.html('Added!').removeClass('loading').addClass('added');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    resetButton();
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                resetButton();
                
                // Fallback: submit form normally
                $form.off('submit').submit();
            }
        });
        
        function resetButton() {
            $button.html(originalText).removeClass('loading added').prop('disabled', false);
            $form.data('processing', false);
        }
        
        return false;
    });

})(jQuery);