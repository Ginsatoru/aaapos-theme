/**
 * Checkout Shipping Visibility & Real-time Update Handler
 * Handles showing/hiding shipping sections and triggers real-time updates
 * 
 * FILE: assets/js/checkout-shipping.js
 * 
 * @package aaapos-prime
 */

(function($) {
    'use strict';

    /**
     * Check if shipping methods are available
     * Returns true if at least one shipping method exists
     */
    function hasShippingMethods() {
        const $shippingMethods = $('.woocommerce-shipping-methods');
        
        if (!$shippingMethods.length) {
            return false;
        }
        
        const $inputs = $shippingMethods.find('input.shipping_method');
        return $inputs.length > 0;
    }

    /**
     * Update shipping section visibility based on method availability
     * Shows native WooCommerce "no shipping" message when no methods available
     */
    function updateShippingVisibility() {
        const $shippingMethodSection = $('.shipping-method-section');
        
        // Always ensure the shipping method section is visible
        $shippingMethodSection.show();
        
        if (hasShippingMethods()) {
            console.log('✅ Shipping methods available');
        } else {
            console.log('🚫 No shipping methods - showing WooCommerce message');
        }
    }

    /**
     * Trigger checkout update
     */
    function triggerCheckoutUpdate(source) {
        console.log('🔄 Triggering checkout update from:', source);
        
        // Block both order review and shipping section with loading overlay
        $('#order_review, .shipping-method-options').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        
        // Trigger WooCommerce checkout update with force refresh
        $(document.body).trigger('update_checkout', { update_shipping_method: true });
    }

    /**
     * Initialize real-time checkout updates
     */
    function initRealtimeUpdates() {
        let countryUpdateTimer;
        let postcodeUpdateTimer;
        
        // Country/State changes - immediate update (with small delay for UX)
        $(document).on('change', 'select#billing_country, select#billing_state, select#shipping_country, select#shipping_state', function() {
            const fieldId = $(this).attr('id');
            const fieldValue = $(this).val();
            
            console.log(`📍 ${fieldId} changed to: ${fieldValue}`);
            
            clearTimeout(countryUpdateTimer);
            
            countryUpdateTimer = setTimeout(function() {
                triggerCheckoutUpdate(fieldId);
            }, 300);
        });

        // Postcode/ZIP changes - debounced update
        $(document).on('input change', 'input#billing_postcode, input#shipping_postcode', function() {
            const fieldId = $(this).attr('id');
            const fieldValue = $(this).val();
            
            clearTimeout(postcodeUpdateTimer);
            
            postcodeUpdateTimer = setTimeout(function() {
                if (fieldValue.length >= 3) {
                    console.log(`📮 ${fieldId} changed to: ${fieldValue}`);
                    triggerCheckoutUpdate(fieldId);
                }
            }, 1000);
        });

        // City changes - debounced update
        let cityUpdateTimer;
        $(document).on('input change', 'input#billing_city, input#shipping_city', function() {
            const fieldId = $(this).attr('id');
            const fieldValue = $(this).val();
            
            clearTimeout(cityUpdateTimer);
            
            cityUpdateTimer = setTimeout(function() {
                if (fieldValue.length >= 3) {
                    console.log(`🏙️ ${fieldId} changed to: ${fieldValue}`);
                    triggerCheckoutUpdate(fieldId);
                }
            }, 1000);
        });
    }

    /**
     * Initialize shipping visibility on page load
     */
    function initShippingVisibility() {
        if (!$('body').hasClass('woocommerce-checkout')) {
            return;
        }
        
        // Initial check
        updateShippingVisibility();
        
        // Watch for checkout updates
        $(document.body).on('updated_checkout', function() {
            console.log('✅ Checkout updated - refreshing shipping visibility');
            
            // Unblock both sections
            $('#order_review, .shipping-method-options').unblock();
            
            // Force refresh of shipping section to ensure it shows correct content
            const $shippingOptions = $('.shipping-method-options');
            if ($shippingOptions.length) {
                // Add a small fade effect for better UX
                $shippingOptions.css('opacity', '0.5');
                setTimeout(function() {
                    $shippingOptions.css('opacity', '1');
                }, 100);
            }
            
            // Update shipping visibility
            setTimeout(updateShippingVisibility, 50);
            setTimeout(updateShippingVisibility, 200);
            setTimeout(updateShippingVisibility, 500);
            
            // Log shipping methods count for debugging
            const $shippingMethods = $('.woocommerce-shipping-methods');
            if ($shippingMethods.length) {
                const methodCount = $shippingMethods.find('input.shipping_method').length;
                console.log(`🚚 Found ${methodCount} shipping method(s)`);
                
                // Log each method for debugging
                $shippingMethods.find('input.shipping_method').each(function() {
                    const label = $(this).next('label').text().trim();
                    console.log(`  - ${label}`);
                });
            } else {
                console.log('🚫 No shipping methods container');
            }
        });
        
        // Watch for shipping calculator updates
        $(document.body).on('updated_shipping_method', function() {
            console.log('🚚 Shipping method updated');
            setTimeout(updateShippingVisibility, 50);
        });
        
        // Handle checkout errors
        $(document.body).on('checkout_error', function() {
            console.error('❌ Checkout update error');
            $('#order_review, .shipping-method-options').unblock();
        });
        
        // MutationObserver to catch dynamic DOM changes
        const observer = new MutationObserver(function(mutations) {
            let shouldCheck = false;
            
            mutations.forEach(function(mutation) {
                // Check if shipping methods were added/removed
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    $(mutation.addedNodes).each(function() {
                        if ($(this).hasClass('woocommerce-shipping-methods') || 
                            $(this).find('.woocommerce-shipping-methods').length) {
                            shouldCheck = true;
                        }
                    });
                    $(mutation.removedNodes).each(function() {
                        if ($(this).hasClass('woocommerce-shipping-methods') || 
                            $(this).find('.woocommerce-shipping-methods').length) {
                            shouldCheck = true;
                        }
                    });
                }
            });
            
            if (shouldCheck) {
                console.log('🔍 DOM mutation detected - checking shipping');
                setTimeout(updateShippingVisibility, 50);
            }
        });
        
        // Start observing the order review area
        const orderReview = document.getElementById('order_review');
        if (orderReview) {
            observer.observe(orderReview, {
                childList: true,
                subtree: true
            });
        }
        
        console.log('✨ Shipping visibility & real-time updates initialized');
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        if ($('body').hasClass('woocommerce-checkout')) {
            // Initialize shipping visibility handler
            initShippingVisibility();
            
            // Initialize real-time update handlers
            initRealtimeUpdates();
            
            // Run initial checks with delays to catch late-loading elements
            setTimeout(updateShippingVisibility, 500);
            setTimeout(updateShippingVisibility, 1000);
            setTimeout(updateShippingVisibility, 2000);
        }
    });

    /**
     * Reinitialize on AJAX complete (for compatibility with other plugins)
     */
    $(document).ajaxComplete(function(event, xhr, settings) {
        // Only on checkout page and for WooCommerce AJAX calls
        if ($('body').hasClass('woocommerce-checkout') && 
            settings.url && 
            (settings.url.indexOf('wc-ajax') !== -1 || settings.url.indexOf('update_order_review') !== -1)) {
            
            setTimeout(updateShippingVisibility, 200);
        }
    });

    // Expose functions globally for manual triggering if needed
    window.aaaposUpdateShippingVisibility = updateShippingVisibility;
    window.aaaposTriggerCheckoutUpdate = triggerCheckoutUpdate;

})(jQuery);