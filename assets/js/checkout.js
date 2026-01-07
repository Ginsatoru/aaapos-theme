/**
 * Checkout Page Enhancements
 * WITH COUPON TOGGLE & NOTIFICATIONS
 * FIXED: Prevents all form submissions for coupons, uses only AJAX
 * UPDATED: Shows discount amount in notification like cart page
 * 
 * @package aaapos-prime
 */

(function($) {
    'use strict';

    /**
     * Remove duplicate coupon fields
     */
    function removeDuplicateCoupons() {
        var $couponForms = $('.checkout-coupon-bottom');
        
        // If more than one coupon field exists, remove all except the first
        if ($couponForms.length > 1) {
            $couponForms.not(':first').remove();
        }
        
        // Also remove any default WooCommerce coupon forms
        $('form.checkout_coupon:not(.coupon-form-bottom)').remove();
        $('.woocommerce-form-coupon-toggle').remove();
    }

    /**
     * Show modern validation error with proper positioning
     */
    function showModernValidationError($input, message) {
        // Remove any existing error
        $('.modern-validation-error').remove();
        
        // Add error state to input
        $input.addClass('validation-error').attr('aria-invalid', 'true');
        
        // Create modern error message with icon - positioned absolutely
        const $error = $(`
            <div class="modern-validation-error" style="
                position: absolute;
                left: 0;
                right: 0;
                top: calc(100% + 0.5rem);
                z-index: 10;
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 0.75rem 1rem;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 0.5rem;
                color: #dc2626;
                font-size: 0.875rem;
                font-weight: 500;
                line-height: 1.4;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                opacity: 0;
                transform: translateY(-10px);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            ">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span style="flex: 1;">${message}</span>
            </div>
        `);
        
        // Make sure parent has position relative
        const $parent = $input.closest('.coupon');
        if ($parent.length) {
            $parent.css('position', 'relative');
            $parent.append($error);
        } else {
            // Fallback: insert after input
            $input.parent().css('position', 'relative');
            $input.after($error);
        }
        
        // Trigger animation
        requestAnimationFrame(() => {
            $error.css({
                opacity: '1',
                transform: 'translateY(0)'
            });
        });
        
        // Remove error on input or after timeout
        $input.one('input', function() {
            removeValidationError($input, $error);
        });
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if ($error.parent().length) {
                removeValidationError($input, $error);
            }
        }, 4000);
    }

    /**
     * Remove validation error with animation
     */
    function removeValidationError($input, $error) {
        $input.removeClass('validation-error').removeAttr('aria-invalid');
        $error.css({
            opacity: '0',
            transform: 'translateY(-10px)'
        });
        setTimeout(() => $error.remove(), 300);
    }

    /**
     * Shake animation for input
     */
    function shakeElement($element) {
        $element.css({
            animation: 'shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)'
        });
        
        setTimeout(() => {
            $element.css({
                animation: ''
            });
        }, 500);
        
        // Add shake keyframes and styles if not exists
        if (!document.getElementById('checkout-validation-styles')) {
            const style = document.createElement('style');
            style.id = 'checkout-validation-styles';
            style.textContent = `
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }
                .validation-error {
                    border-color: #dc2626 !important;
                    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Show loading state on button - Simple text change
     */
    function showButtonLoading($button) {
        // Store original text
        if (!$button.data('original-text')) {
            $button.data('original-text', $button.text());
        }
        
        // Change button text and disable
        $button.text('Applying...').prop('disabled', true).css('opacity', '0.7');
    }

    /**
     * Hide loading state on button
     */
    function hideButtonLoading($button) {
        // Restore original text
        const originalText = $button.data('original-text');
        if (originalText) {
            $button.text(originalText);
        }
        
        // Re-enable button
        $button.prop('disabled', false).css('opacity', '1');
    }

    /**
     * Get discount amount from cart totals
     */
    function getDiscountAmount() {
        // Try to find discount in the order review
        const $discountRow = $('.cart-discount');
        if ($discountRow.length) {
            const discountText = $discountRow.find('.amount').text();
            return discountText;
        }
        return '';
    }

    /**
     * Handle Coupon Apply/Remove with Toggle Button
     * FIXED: Completely prevents form submission, uses only AJAX
     */
    function handleCouponToggle() {
        // Remove any existing handlers first
        $(document).off('submit', '.coupon-form-bottom');
        $(document).off('click', '.coupon-apply-btn');
        $(document).off('click', 'a.woocommerce-remove-coupon');
        
        // Prevent ALL form submissions for coupon forms on checkout
        $(document).on('submit', '.coupon-form-bottom', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        });
        
        // Handle button clicks directly
        $(document).on('click', '.coupon-apply-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const $form = $button.closest('form');
            const action = $button.attr('data-action');
            const $input = $form.find('input[name="coupon_code"]');
            const couponCode = action === 'remove' ? $button.attr('data-coupon-code') : $input.val().trim();
            
            // Validate for apply
            if (action === 'apply' && !couponCode) {
                showModernValidationError($input, 'Please enter a coupon code to continue');
                return false;
            }
            
            // Show loading state
            showButtonLoading($button);
            
            // Handle via AJAX
            if (action === 'apply') {
                applyCouponAjax(couponCode, $button, $input);
            } else if (action === 'remove') {
                removeCouponAjax(couponCode, $button);
            }
            
            return false;
        });
        
        // Also intercept WooCommerce's default remove coupon links
        $(document).on('click', 'a.woocommerce-remove-coupon', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $link = $(this);
            const couponCode = $link.data('coupon') || $link.attr('data-coupon');
            
            if (couponCode) {
                removeCouponAjax(couponCode, $link);
            }
            
            return false;
        });
    }
    
    /**
     * Apply coupon via AJAX
     */
    function applyCouponAjax(couponCode, $button, $input) {
        $.ajax({
            type: 'POST',
            url: wc_checkout_params.ajax_url,
            data: {
                action: 'woocommerce_apply_coupon',
                security: wc_checkout_params.apply_coupon_nonce,
                coupon_code: couponCode
            },
            success: function(response) {
                // Wait a bit for cart to update, then get discount
                setTimeout(() => {
                    const discountAmount = getDiscountAmount();
                    
                    // Show notification with discount amount
                    if (typeof window.createCouponNotification === 'function') {
                        window.createCouponNotification(couponCode, discountAmount);
                    }
                }, 500);
                
                // Clear input
                if ($input) {
                    $input.val('');
                }
                
                // Update checkout
                $(document.body).trigger('update_checkout');
            },
            error: function(xhr, status, error) {
                // Try to get error message from response
                let errorMsg = 'This coupon code is invalid or has expired';
                if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMsg = xhr.responseJSON.data;
                }
                
                // Show modern validation error
                showModernValidationError($input, errorMsg);
                
                // Hide loading immediately on error
                hideButtonLoading($button);
            },
            complete: function() {
                // Hide loading state
                setTimeout(() => {
                    hideButtonLoading($button);
                }, 300);
            }
        });
    }
    
    /**
     * Remove coupon via AJAX
     */
    function removeCouponAjax(couponCode, $button) {
        // Show loading if it's a button
        if ($button.is('button') || $button.is('a.button')) {
            if (!$button.data('original-text')) {
                $button.data('original-text', $button.text());
            }
            $button.text('Removing...').prop('disabled', true).css('opacity', '0.7');
        }
        
        $.ajax({
            type: 'POST',
            url: wc_checkout_params.ajax_url,
            data: {
                action: 'woocommerce_remove_coupon',
                security: wc_checkout_params.remove_coupon_nonce,
                coupon: couponCode
            },
            success: function(response) {
                // Show notification
                showSimpleNotification('Coupon removed', couponCode);
                
                // Update checkout to refresh totals
                $(document.body).trigger('update_checkout');
            },
            error: function(xhr, status, error) {
                // Show error notification
                showSimpleNotification('Error', 'Failed to remove coupon. Please try again.');
            },
            complete: function() {
                // Hide loading state
                if ($button.is('button') || $button.is('a.button')) {
                    setTimeout(() => {
                        hideButtonLoading($button);
                    }, 300);
                }
            }
        });
    }

    /**
     * Simple notification for coupon removal (fallback)
     */
    function showSimpleNotification(title, subtitle) {
        const checkmarkSVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>';
        
        // Remove any existing notification
        $('.aaapos-cart-notification').remove();
        
        const $notification = $(`
            <div class="aaapos-cart-notification">
                <div class="aaapos-cart-tick">${checkmarkSVG}</div>
                <div class="aaapos-cart-content aaapos-cart-row">
                    <div class="aaapos-cart-icon">${checkmarkSVG}</div>
                    <div class="aaapos-cart-text">
                        <div class="aaapos-cart-title"><strong>${title}</strong></div>
                        <div class="aaapos-cart-desc">${subtitle}</div>
                    </div>
                    <div class="aaapos-cart-close">&times;</div>
                </div>
            </div>
        `);
        
        $('body').append($notification);
        
        // Animate
        requestAnimationFrame(() => {
            $notification.addClass('is-active');
            setTimeout(() => $notification.addClass('is-center'), 50);
            setTimeout(() => $notification.addClass('is-expanded'), 1000);
            
            // Auto close
            setTimeout(() => {
                $notification.removeClass('is-expanded');
                setTimeout(() => {
                    $notification.removeClass('is-center');
                    setTimeout(() => {
                        $notification.removeClass('is-active');
                        setTimeout(() => $notification.remove(), 300);
                    }, 450);
                }, 450);
            }, 3000);
        });
        
        $notification.on('click', '.aaapos-cart-close', function() {
            $notification.removeClass('is-expanded');
            setTimeout(() => $notification.remove(), 600);
        });
    }

    /**
     * Initialize checkout functionality
     */
    function initCheckout() {
        
        // Remove duplicate coupons immediately
        removeDuplicateCoupons();
        
        // Initialize coupon toggle handler
        handleCouponToggle();
        
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
        $('form.checkout').on('submit', function(e) {
            // Don't interfere with place order submission
            if (!$(e.target).hasClass('coupon-form-bottom')) {
                const $button = $('#place_order');
                
                if (!$button.hasClass('processing')) {
                    $button.addClass('processing');
                    $button.prop('disabled', true);
                    
                    // Add spinner
                    if (!$button.find('.spinner').length) {
                        $button.append('<span class="spinner" style="margin-left: 8px; display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></span>');
                    }
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
            // Only for actual checkout submission, not coupon forms
            if ($(e.target).hasClass('coupon-form-bottom')) {
                return;
            }
            
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
            
            // Remove duplicates on initial load with delays
            setTimeout(removeDuplicateCoupons, 100);
            setTimeout(removeDuplicateCoupons, 300);
            setTimeout(removeDuplicateCoupons, 500);
        }
    });

    /**
     * Reinitialize after AJAX updates
     */
    $(document.body).on('updated_checkout', function() {
        initCheckout();
        
        // Remove duplicates after checkout update
        setTimeout(removeDuplicateCoupons, 100);
        setTimeout(removeDuplicateCoupons, 300);
    });

    /**
     * Monitor for dynamically added coupon forms
     */
    if ($('body').hasClass('woocommerce-checkout')) {
        // Use MutationObserver to catch any dynamically added coupons
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    removeDuplicateCoupons();
                }
            });
        });

        // Start observing the checkout area
        const checkoutArea = document.querySelector('.woocommerce-checkout-review-order');
        if (checkoutArea) {
            observer.observe(checkoutArea, {
                childList: true,
                subtree: true
            });
        }
    }

})(jQuery);