<?php
/**
 * reCAPTCHA v2 Checkbox Settings - Contact Form
 *
 * Site/secret keys are stored via the Customizer instead of hardcoded,
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
}
add_action('customize_register', 'aaapos_recaptcha_customizer');

/**
 * Shared reCAPTCHA v2 verification helper.
 * Verifies a g-recaptcha-response token against Google's siteverify endpoint
 * using the secret key stored in the Customizer.
 *
 * @param string $recaptcha_response The g-recaptcha-response token from the client.
 * @return bool True if verification succeeded, false otherwise.
 */
if (!function_exists('aaapos_verify_recaptcha_response')) {
    function aaapos_verify_recaptcha_response($recaptcha_response) {
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

        return !empty($recaptcha_body['success']);
    }
}