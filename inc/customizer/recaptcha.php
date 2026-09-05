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
        'title'    => __('reCAPTCHA (Contact Form)', 'aaapos-prime'),
        'priority' => 160,
    ));

    $wp_customize->add_setting('recaptcha_enable', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_enable', array(
        'label'       => __('Enable reCAPTCHA on Contact Form', 'aaapos-prime'),
        'description' => __('Uses Google reCAPTCHA v2 Checkbox ("I\'m not a robot").', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'checkbox',
        'priority'    => 10,
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
        'description' => __('Kept server-side only, never output to the page.', 'aaapos-prime'),
        'section'     => 'aaapos_recaptcha',
        'type'        => 'text',
        'priority'    => 30,
    ));
}
add_action('customize_register', 'aaapos_recaptcha_customizer');