/**
 * Checkout reCAPTCHA
 * Explicitly renders the reCAPTCHA widget in the checkout order-review panel,
 * and re-renders it whenever WooCommerce refreshes that panel via AJAX
 * (WooCommerce's "updated_checkout" event) - since the widget's container
 * gets replaced with fresh markup each time, Google's one-time auto-render
 * scan would otherwise leave it dead after the first refresh.
 *
 * @package AAAPOS_Prime
 */
(function () {
    'use strict';

    function renderWidget() {
        if (typeof grecaptcha === 'undefined' || !grecaptcha.render) {
            return;
        }

        var container = document.getElementById('aaapos-checkout-recaptcha');

        if (!container) {
            return;
        }

        // Already has a rendered widget inside it - avoid grecaptcha's
        // "already been rendered" error on repeat calls.
        if (container.childElementCount > 0) {
            return;
        }

        grecaptcha.render(container, {
            sitekey: (typeof aaapos_checkout_recaptcha !== 'undefined') ? aaapos_checkout_recaptcha.site_key : ''
        });
    }

    // Called by Google's script once it finishes loading (api.js?onload=...&render=explicit).
    window.aaaposInitCheckoutRecaptcha = function () {
        renderWidget();

        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('updated_checkout', renderWidget);
        }
    };
})();