<?php
/**
 * Page Loader Customizer Settings
 * 
 * @package aaapos-prime
 */

if (!defined('ABSPATH')) {
    exit;
}

function aaapos_page_loader_customizer($wp_customize) {
    
    // ===================================================================
    // PAGE LOADER SECTION
    // ===================================================================
    $wp_customize->add_section('aaapos_page_loader', array(
        'title' => __('Page Loading Animation', 'aaapos-prime'),
        'description' => __('Configure the page loading overlay animation that appears when pages load.', 'aaapos-prime'),
        'priority' => 45,
    ));

    // Enable Page Loader
    $wp_customize->add_setting('enable_page_loader', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_page_loader', array(
        'label' => __('Enable Page Loading Animation', 'aaapos-prime'),
        'description' => __('Show a loading overlay with logo and progress bar when pages load.', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'type' => 'checkbox',
        'priority' => 10,
    ));

    // Loader Logo (use site logo by default, or custom)
    $wp_customize->add_setting('page_loader_use_site_logo', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('page_loader_use_site_logo', array(
        'label' => __('Use Site Logo', 'aaapos-prime'),
        'description' => __('Use the main site logo for the loader. Uncheck to upload a custom loader logo.', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'type' => 'checkbox',
        'priority' => 20,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false);
        },
    ));

    // Custom Loader Logo
    $wp_customize->add_setting('page_loader_custom_logo', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'page_loader_custom_logo', array(
        'label' => __('Custom Loader Logo', 'aaapos-prime'),
        'description' => __('Upload a custom logo to show in the loader (recommended: PNG, 400x400px or larger).', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'mime_type' => 'image',
        'priority' => 30,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false) && 
                   !get_theme_mod('page_loader_use_site_logo', true);
        },
    )));

    // Loading Text
    $wp_customize->add_setting('page_loader_text', array(
        'default' => 'Loading…',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('page_loader_text', array(
        'label' => __('Loading Text', 'aaapos-prime'),
        'description' => __('Text displayed below the logo.', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'type' => 'text',
        'priority' => 40,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false);
        },
    ));

    // Loader Background Color
    $wp_customize->add_setting('page_loader_bg_color', array(
        'default' => '#fafafa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_loader_bg_color', array(
        'label' => __('Background Color', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'priority' => 50,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false);
        },
    )));

    // Loader Text Color
    $wp_customize->add_setting('page_loader_text_color', array(
        'default' => '#4a5568',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_loader_text_color', array(
        'label' => __('Text Color', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'priority' => 60,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false);
        },
    )));

    // Progress Bar Color
    $wp_customize->add_setting('page_loader_accent_color', array(
        'default' => '#0f8abe',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_loader_accent_color', array(
        'label' => __('Progress Bar Color', 'aaapos-prime'),
        'section' => 'aaapos_page_loader',
        'priority' => 70,
        'active_callback' => function() {
            return get_theme_mod('enable_page_loader', false);
        },
    )));

    // Minimum Display Time
$wp_customize->add_setting('page_loader_min_time', array(
    'default' => 500,
    'sanitize_callback' => 'absint',
    'transport' => 'refresh',
));

$wp_customize->add_control('page_loader_min_time', array(
    'label' => __('Minimum Display Time (milliseconds)', 'aaapos-prime'),
    'description' => __('Minimum time to show the loader (prevents flash). Default: 500ms.', 'aaapos-prime'),
    'section' => 'aaapos_page_loader',
    'type' => 'number',
    'input_attrs' => array(
        'min' => 300,
        'max' => 2000,
        'step' => 100,
    ),
    'priority' => 80,
    'active_callback' => function() {
        return get_theme_mod('enable_page_loader', false);
    },
));

// Maximum Wait Time
$wp_customize->add_setting('page_loader_max_time', array(
    'default' => 5000,
    'sanitize_callback' => 'absint',
    'transport' => 'refresh',
));

$wp_customize->add_control('page_loader_max_time', array(
    'label' => __('Maximum Wait Time (milliseconds)', 'aaapos-prime'),
    'description' => __('Maximum time before loader auto-hides (fallback). Default: 5000ms (5 seconds).', 'aaapos-prime'),
    'section' => 'aaapos_page_loader',
    'type' => 'number',
    'input_attrs' => array(
        'min' => 2000,
        'max' => 10000,
        'step' => 500,
    ),
    'priority' => 90,
    'active_callback' => function() {
        return get_theme_mod('enable_page_loader', false);
    },
));
}
add_action('customize_register', 'aaapos_page_loader_customizer');