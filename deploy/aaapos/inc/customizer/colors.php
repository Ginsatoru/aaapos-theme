<?php
/**
 * Simplified Brand Color Customizer
 * 
 * Single branding color that controls all accent elements.
 * Hover and light variants are auto-calculated.
 * 
 * @package aaapos-prime
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Brand Color Customizer Settings
 */
function aaapos_colors_customizer($wp_customize) {
    
    // =========================================================================
    // SECTION: Brand Color (Simple - Just ONE color!)
    // =========================================================================
    $wp_customize->add_section('aaapos_brand_color', array(
        'title'       => __('Brand Color', 'aaapos-prime'),
        'description' => __('Choose your brand color. This single color controls buttons, links, badges, and all accent elements throughout your site.', 'aaapos-prime'),
        'priority'    => 30,
    ));

    // THE ONE AND ONLY BRANDING COLOR
    $wp_customize->add_setting('brand_color', array(
        'default'           => '#0ea5e9',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'brand_color', array(
        'label'       => __('Brand Color', 'aaapos-prime'),
        'description' => __('Your main brand/accent color. Used for buttons, links, badges, icons, and highlights.', 'aaapos-prime'),
        'section'     => 'aaapos_brand_color',
        'priority'    => 10,
    )));

    // =========================================================================
    // OPTIONAL: Navigation Accent Color (separate from brand)
    // =========================================================================
    $wp_customize->add_setting('nav_accent_color', array(
        'default'           => '#D4AF37',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_accent_color', array(
        'label'       => __('Navigation Accent', 'aaapos-prime'),
        'description' => __('Color for navigation hover and active states. Leave as gold for elegant contrast, or match your brand color.', 'aaapos-prime'),
        'section'     => 'aaapos_brand_color',
        'priority'    => 20,
    )));
}
add_action('customize_register', 'aaapos_colors_customizer');