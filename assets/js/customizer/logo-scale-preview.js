/**
 * Logo Scale - Customizer Live Preview
 *
 * Updates the --logo-scale CSS variable instantly as the slider moves
 * in the Customizer, instead of only applying on Save/Publish.
 *
 * @package aaapos-prime
 */
(function (wp) {
    if (!wp || !wp.customize) {
        return;
    }

    wp.customize('aaapos_logo_scale', function (value) {
        value.bind(function (newValue) {
            var scale = parseInt(newValue, 10) / 100;
            document.documentElement.style.setProperty('--logo-scale', scale);
        });
    });
})(window.wp);