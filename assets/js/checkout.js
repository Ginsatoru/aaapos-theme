/**
 * Checkout Page Enhancements
 * 
 * @package aaapos-prime
 */

(function($) {
    'use strict';

    /**
     * Initialize checkout functionality
     */
    function initCheckout() {
        
        // Smooth toggle for "Ship to different address"
        $('#ship-to-different-address-checkbox').on('change', function() {
            const $shippingFields = $('.shipping-fields');
            
            if ($(this).is(':checked')) {
                $shippingFields.slideDown(300);
            } else {
                $shippingFields.slideUp(300);
            }
        });

        // Add loading state to place order button
        $('form.checkout').on('submit', function() {
            const $button = $('#place_order');
            
            if (!$button.hasClass('processing')) {
                $button.addClass('processing');
                $button.prop('disabled', true);
                
                // Add spinner
                if (!$button.find('.spinner').length) {
                    $button.append('<span class="spinner" style="margin-left: 8px; display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></span>');
                }
            }
        });

        // Remove loading state if checkout fails
        $(document.body).on('checkout_error', function() {
            const $button = $('#place_order');
            $button.removeClass('processing');
            $button.prop('disabled', false);
            $button.find('.spinner').remove();
        });

        // Field validation feedback
        $('form.checkout input, form.checkout select, form.checkout textarea').on('blur', function() {
            const $field = $(this);
            const $parent = $field.closest('.form-row');
            
            // Remove previous validation states
            $parent.removeClass('woocommerce-invalid woocommerce-validated');
            
            // Check if field is required and empty
            if ($field.prop('required') && !$field.val()) {
                $parent.addClass('woocommerce-invalid');
            } else if ($field.val()) {
                $parent.addClass('woocommerce-validated');
            }
        });

        // Update order review on field changes (for shipping calculations)
        let updateTimer;
        $('form.checkout').on('change', 'select#billing_country, select#billing_state, select#shipping_country, select#shipping_state, input#billing_postcode, input#shipping_postcode', function() {
            clearTimeout(updateTimer);
            updateTimer = setTimeout(function() {
                $(document.body).trigger('update_checkout');
            }, 500);
        });

        // Enhance payment method selection
        $('input[name="payment_method"]').on('change', function() {
            const $selected = $(this);
            const $allMethods = $('.payment_methods li');
            
            // Remove active class from all
            $allMethods.removeClass('active');
            
            // Add active class to selected
            $selected.closest('li').addClass('active');
        });

        // Scroll to errors
        $(document.body).on('checkout_error', function() {
            const $errors = $('.woocommerce-error, .woocommerce-message');
            if ($errors.length) {
                $('html, body').animate({
                    scrollTop: $errors.offset().top - 100
                }, 500);
            }
        });

        // Format phone numbers (optional - basic formatting)
        $('input[type="tel"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            $(this).val(value);
        });

        // Auto-fill same as billing (enhanced UX)
        $('#ship-to-different-address-checkbox').on('change', function() {
            if (!$(this).is(':checked')) {
                // Optionally copy billing to shipping when unchecked
                copyBillingToShipping();
            }
        });

        function copyBillingToShipping() {
            const fields = [
                'first_name',
                'last_name',
                'company',
                'address_1',
                'address_2',
                'city',
                'postcode',
                'country',
                'state'
            ];

            fields.forEach(function(field) {
                const billingValue = $('#billing_' + field).val();
                if (billingValue) {
                    $('#shipping_' + field).val(billingValue).trigger('change');
                }
            });
        }

        // Prevent double submission
        let isSubmitting = false;
        $('form.checkout').on('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            // Reset after 5 seconds (in case of error)
            setTimeout(function() {
                isSubmitting = false;
            }, 5000);
        });

        // Update checkout on coupon apply/remove
        $(document.body).on('applied_coupon removed_coupon', function() {
            $(document.body).trigger('update_checkout');
        });

    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        if ($('body').hasClass('woocommerce-checkout')) {
            initCheckout();
        }
    });

    /**
     * Reinitialize after AJAX updates
     */
    $(document.body).on('updated_checkout', function() {
        initCheckout();
    });

})(jQuery);