<?php
/**
 * reCAPTCHA Settings (v2 Checkbox or v3 Invisible)
 *
 * The version is chosen in Customizer > reCAPTCHA (default: v2, so existing
 * sites behave exactly as before). Site/secret keys are stored via the Customizer instead of hardcoded,
 * so they can be updated without touching code. Verification happens
 * server-side in inc/functions.php (aaapos_handle_contact_form_submission).
 *
 * @package AAAPOS_Prime
 */

if (!defined('ABSPATH')) {
    exit;
}

function aaapos_recaptcha_customizer($wp_customize) {

    $wp_customize->add_section('aaapos_recaptcha', array(
        'title'    => __('reCAPTCHA', 'aaapos-prime'),
        'priority' => 160,
    ));

    $wp_customize->add_setting('recaptcha_enable', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_enable', array(
        'label'       => __('Enable reCAPTCHA on Contact Form', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    $wp_customize->add_setting('recaptcha_enable_auth', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_enable_auth', array(
        'label'       => __('Enable reCAPTCHA on Login/Register', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'checkbox',
        'priority'    => 15,
    ));

    $wp_customize->add_setting('recaptcha_enable_checkout', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_enable_checkout', array(
        'label'       => __('Enable reCAPTCHA on Checkout', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'checkbox',
        'priority'    => 16,
    ));

    $wp_customize->add_setting('recaptcha_enable_demo', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_enable_demo', array(
        'label'       => __('Enable reCAPTCHA on Request a Demo Form', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'checkbox',
        'priority'    => 17,
    ));

    $wp_customize->add_setting('recaptcha_site_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_site_key', array(
        'label'    => __('Site Key', 'aaapos-prime'),
        'section'  => 'aaapos_recaptcha',
        'type'     => 'text',
        'priority' => 20,
    ));

    $wp_customize->add_setting('recaptcha_secret_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_secret_key', array(
        'label'       => __('Secret Key', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'text',
        'priority'    => 30,
    ));

    $wp_customize->add_setting('recaptcha_version', array(
        'default'           => 'v2',
        'sanitize_callback' => 'aaapos_sanitize_recaptcha_version',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_version', array(
        'label'       => __('reCAPTCHA Version', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'radio',
        'choices'     => array(
            'v2' => __('v2 Checkbox ("I\'m not a robot")', 'aaapos-prime'),
            'v3' => __('v3 Invisible (no checkbox)', 'aaapos-prime'),
        ),
        'priority'    => 18,
    ));

    $wp_customize->add_setting('recaptcha_v3_threshold', array(
        'default'           => 0.5,
        'sanitize_callback' => function ($value) {
            return min(0.9, max(0.1, (float) $value));
        },
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_v3_threshold', array(
        'label'           => __('v3 Minimum Score', 'aaapos-prime'),
        'section'         => 'aaapos_recaptcha',
        'type'            => 'number',
        'input_attrs'     => array('min' => 0.1, 'max' => 0.9, 'step' => 0.1),
        'active_callback' => function () {
            return aaapos_recaptcha_version() === 'v3';
        },
        'priority'        => 19,
    ));
}
add_action('customize_register', 'aaapos_recaptcha_customizer');

function aaapos_sanitize_recaptcha_version($value) {
    return $value === 'v3' ? 'v3' : 'v2';
}

/**
 * Selected reCAPTCHA version: 'v2' (checkbox, default) or 'v3' (invisible).
 */
function aaapos_recaptcha_version() {
    return aaapos_sanitize_recaptcha_version(get_theme_mod('recaptcha_version', 'v2'));
}

/**
 * v3 minimum score (0.1 - 0.9).
 */
function aaapos_recaptcha_threshold() {
    return min(0.9, max(0.1, (float) get_theme_mod('recaptcha_v3_threshold', 0.5)));
}

/**
 * Helper script used by every form in v3 mode: window.aaaposRecaptchaV3(siteKey, action)
 * returns a Promise for a token. Registered only; each form enqueues it when it needs it.
 */
add_action('wp_enqueue_scripts', function () {
    wp_register_script('aaapos-recaptcha-v3', get_template_directory_uri() . '/assets/js/recaptcha-v3.js', array(), AAAPOS_VERSION, true);
}, 5);

/**
 * Shared reCAPTCHA verification helper (v2 and v3).
 * Verifies a token against Google's siteverify endpoint using the secret key
 * stored in the Customizer. In v3 mode it also checks the score and, when
 * given, that the token was created for the expected action.
 *
 * @param string $recaptcha_response The token from the client.
 * @param string $action             Expected v3 action (ignored in v2).
 * @return bool True if verification succeeded, false otherwise.
 */
if (!function_exists('aaapos_verify_recaptcha_response')) {
    function aaapos_verify_recaptcha_response($recaptcha_response, $action = '') {
        if (empty($recaptcha_response)) {
            return false;
        }

        $secret_key = get_theme_mod('recaptcha_secret_key', '');
        if (empty($secret_key)) {
            return false;
        }

        $recaptcha_verify = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'body' => array(
                    'secret'   => $secret_key,
                    'response' => $recaptcha_response,
                    'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
                ),
            )
        );

        if (is_wp_error($recaptcha_verify)) {
            return false;
        }

        $recaptcha_body = json_decode(wp_remote_retrieve_body($recaptcha_verify), true);

        if (empty($recaptcha_body['success'])) {
            return false;
        }

        if (aaapos_recaptcha_version() === 'v3') {
            if ((float) ($recaptcha_body['score'] ?? 0) < aaapos_recaptcha_threshold()) {
                return false;
            }
            if ($action !== '' && isset($recaptcha_body['action']) && $recaptcha_body['action'] !== $action) {
                return false;
            }
        }

        return true;
    }
}