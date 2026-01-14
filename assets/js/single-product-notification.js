/**
 * Single Product - Cart Notification Integration
 * Works with WooCommerce's native add-to-cart (no AJAX override)
 * Just triggers the notification animation
 * 
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Skip on My Account pages
    if ($('body').hasClass('woocommerce-account')) {
        return;
    }

    /**
     * Listen for WooCommerce's native added_to_cart event
     * This fires after WooCommerce successfully adds product to cart
     */
    $(document.body).on('added_to_cart', function(e, fragments, hash, button) {
        // Cart notification will automatically trigger from cart-notifications.js
        // We just need to make sure the product name is available
        console.log('Product added to cart - notification should show');
    });

})(jQuery);