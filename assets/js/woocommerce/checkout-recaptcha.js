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

    var cfg = (typeof aaapos_checkout_recaptcha !== 'undefined') ? aaapos_checkout_recaptcha : {};

    // v3: no widget. Get an invisible token when the customer clicks Place Order,
    // add it to the form, then let WooCommerce submit as normal.
    if (cfg.version === 'v3') {
        if (typeof jQuery !== 'undefined') {
            jQuery(function ($) {
                var passed = false;

                $('form.checkout').on('checkout_place_order', function () {
                    var $form = $(this);

                    if (passed) {
                        passed = false;
                        return true;
                    }

                    window.aaaposRecaptchaV3(cfg.site_key, 'checkout').then(function (token) {
                        var $field = $form.find('input[name="g-recaptcha-response"]');
                        if (!$field.length) {
                            $field = $('<input type="hidden" name="g-recaptcha-response">').appendTo($form);
                        }
                        $field.val(token);
                    }, function () {}).then(function () {
                        passed = true;
                        $form.trigger('submit');
                    });

                    return false;
                });
            });
        }
        return;
    }

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