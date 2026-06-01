/**
 * Serial Number Field — Client-side Validation
 *
 * Runs on single product pages for products that require a serial number.
 * Intercepts the Add to Cart form submit and shows inline errors before
 * the request reaches the server, giving instant feedback.
 *
 * The server ALSO validates (inc/serial-number.php) so this is purely UX.
 *
 * @package AAAPOS_Prime
 * @since   1.1.0
 */

( function ( $ ) {
    'use strict';

    // Config is injected via wp_localize_script as window.aaaposSN
    var cfg = window.aaaposSN || {
        errorMessage : 'Please enter a serial number before adding this product to your cart.',
        minLength    : 0,
        maxLength    : 0,
        minError     : 'Serial number is too short.',
        maxError     : 'Serial number is too long.',
    };

    var $wrapper, $input, $error;

    function init() {
        $wrapper = $( '#aaapos-sn-wrapper' );
        $input   = $( '#aaapos_serial_number' );
        $error   = $( '#aaapos-sn-error' );

        if ( ! $wrapper.length || ! $input.length ) {
            return;
        }

        // Hide error when customer starts typing
        $input.on( 'input', function () {
            clearError();
        } );

        // Intercept form submit
        $( 'form.cart' ).on( 'submit', function ( e ) {
            var valid = validate();
            if ( ! valid ) {
                e.preventDefault();
                e.stopImmediatePropagation();
                scrollToField();
            }
        } );

        // Also intercept WooCommerce AJAX add-to-cart on single product
        // (fired before the XHR is sent when using wc-add-to-cart)
        $( document ).on( 'click', '.single_add_to_cart_button', function ( e ) {
            var valid = validate();
            if ( ! valid ) {
                e.preventDefault();
                e.stopImmediatePropagation();
                scrollToField();
                return false;
            }
        } );
    }

    /**
     * Validate the serial number field.
     * @returns {boolean} true if valid.
     */
    function validate() {
        var val = $input.val().trim();

        if ( val === '' ) {
            showError( cfg.errorMessage );
            return false;
        }

        if ( cfg.minLength > 0 && val.length < cfg.minLength ) {
            showError( cfg.minError );
            return false;
        }

        if ( cfg.maxLength > 0 && val.length > cfg.maxLength ) {
            showError( cfg.maxError );
            return false;
        }

        clearError();
        return true;
    }

    function showError( message ) {
        $error.text( message ).show();
        $input.addClass( 'aaapos-sn__input--error' ).attr( 'aria-invalid', 'true' );
        $wrapper.addClass( 'aaapos-sn--has-error' );
    }

    function clearError() {
        $error.hide();
        $input.removeClass( 'aaapos-sn__input--error' ).removeAttr( 'aria-invalid' );
        $wrapper.removeClass( 'aaapos-sn--has-error' );
    }

    function scrollToField() {
        if ( $wrapper.length ) {
            $( 'html, body' ).animate(
                { scrollTop: $wrapper.offset().top - 120 },
                300,
                function () { $input.focus(); }
            );
        }
    }

    $( document ).ready( init );

} )( jQuery );