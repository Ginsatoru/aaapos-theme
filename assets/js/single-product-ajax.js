/**
 * AJAX Add to Cart for Single Product Pages
 * Prevents page refresh and enables cart notification
 * NOW SUPPORTS BOTH SIMPLE AND VARIABLE PRODUCTS
 * 
 * @package AAAPOS_Prime
 * @version 2.0.0 - VARIABLE PRODUCTS SUPPORT
 */

(function($) {
    'use strict';

    // Skip on My Account pages
    if ($('body').hasClass('woocommerce-account')) {
        return;
    }

    /**
     * AJAX Add to Cart for Single Products (Simple + Variable)
     */
    $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function(e) {
        const $button = $(this);
        const $form = $button.closest('form.cart');
        
        // Check if button is disabled or variation not selected
        if ($button.hasClass('disabled') || $button.hasClass('wc-variation-selection-needed')) {
            return; // Let WooCommerce show the error message
        }

        e.preventDefault();

        // Check if it's a variable product
        const isVariableProduct = $form.hasClass('variations_form');
        
        // For variable products, ensure a variation is selected
        if (isVariableProduct) {
            const variationId = $form.find('input[name="variation_id"]').val();
            
            if (!variationId || variationId === '0' || variationId === '') {
                // No variation selected - let WooCommerce handle the validation
                return;
            }
        }

        // Disable button and show loading state
        $button.prop('disabled', true).addClass('loading');
        
        const originalText = $button.text();
        $button.text('Adding...');

        // Prepare AJAX data
        let ajaxData = {
            action: 'woocommerce_add_to_cart'
        };

        if (isVariableProduct) {
            // For variable products, get all form data including variation attributes
            const formData = $form.serializeArray();
            
            // Convert array to object
            $.each(formData, function(i, field) {
                ajaxData[field.name] = field.value;
            });
            
            // Ensure we have the required fields
            if (!ajaxData['product_id']) {
                ajaxData['product_id'] = $form.find('[name="product_id"]').val();
            }
            if (!ajaxData['variation_id']) {
                ajaxData['variation_id'] = $form.find('input[name="variation_id"]').val();
            }
            if (!ajaxData['quantity']) {
                ajaxData['quantity'] = $form.find('[name="quantity"]').val() || 1;
            }
        } else {
            // For simple products
            const productId = $form.find('[name="add-to-cart"]').val() || 
                            $form.find('[name="product_id"]').val();
            const quantity = $form.find('[name="quantity"]').val() || 1;
            
            ajaxData['product_id'] = productId;
            ajaxData['quantity'] = quantity;
        }

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: ajaxData,
            dataType: 'json',
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

                // Success! Update cart fragments
                if (response.fragments) {
                    $.each(response.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }

                // Trigger WooCommerce events
                $(document.body).trigger('wc_fragment_refresh');
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                // Button success state
                $button.text('Added!').removeClass('loading').addClass('added');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.text(originalText).removeClass('added').prop('disabled', false);
                }, 2000);

                // Optional: Reset variable product selections after adding to cart
                // Uncomment if you want to clear selections after adding
                /*
                if (isVariableProduct) {
                    setTimeout(function() {
                        $form.find('.variations select').val('').trigger('change');
                        $('.color-swatches .color-swatch').removeClass('selected');
                        $('.size-buttons .size-button').removeClass('selected');
                    }, 2500);
                }
                */
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Add to Cart Error:', textStatus, errorThrown);
                
                // On error, reset button and submit form normally
                $button.text(originalText).removeClass('loading').prop('disabled', false);
                $form.off('submit').submit();
            },
            complete: function() {
                // Ensure button is never stuck in loading state
                setTimeout(function() {
                    if ($button.hasClass('loading')) {
                        $button.text(originalText).removeClass('loading').prop('disabled', false);
                    }
                }, 3000);
            }
        });

        return false;
    });

})(jQuery);