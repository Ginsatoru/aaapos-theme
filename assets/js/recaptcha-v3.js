/**
 * reCAPTCHA v3 helper
 * window.aaaposRecaptchaV3(siteKey, action) returns a Promise for a token.
 * window.aaaposRecaptchaV3.preload(siteKey) loads Google's script ahead of time.
 * Used by the contact, demo, login/register and checkout forms when
 * Customizer > reCAPTCHA > Version is set to v3.
 *
 * @package AAAPOS_Prime
 */
(function () {
    'use strict';

    var loading = null;

    function load(key) {
        if (window.grecaptcha && window.grecaptcha.execute) {
            return new Promise(function (resolve) { window.grecaptcha.ready(resolve); });
        }

        if (!loading) {
            loading = new Promise(function (resolve, reject) {
                var s = document.createElement('script');
                s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(key);
                s.async = true;
                s.onload = function () { window.grecaptcha.ready(resolve); };
                s.onerror = function () { loading = null; reject(new Error('reCAPTCHA failed to load')); };
                document.head.appendChild(s);
            });
        }

        return loading;
    }

    window.aaaposRecaptchaV3 = function (key, action) {
        return load(key).then(function () {
            return window.grecaptcha.execute(key, { action: action });
        });
    };
    window.aaaposRecaptchaV3.preload = load;
})();