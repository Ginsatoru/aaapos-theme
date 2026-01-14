<?php
/**
 * Trust Badges Customizer Settings
 * Allows users to customize trust badge text
 *
 * @package AAAPOS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Trust Badges Customizer Settings
 */
function aaapos_trust_badges_customizer($wp_customize) {
    
    // Add Trust Badges Section
    $wp_customize->add_section('aaapos_trust_badges', array(
        'title'    => __('Product Trust Badges', 'aaapos'),
        'priority' => 160,
        'panel'    => 'woocommerce',
    ));
    
    // Enable/Disable Trust Badges
    $wp_customize->add_setting('show_trust_badges', array(
        'default'           => true,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_trust_badges', array(
        'label'    => __('Show Trust Badges', 'aaapos'),
        'section'  => 'aaapos_trust_badges',
        'type'     => 'checkbox',
        'priority' => 10,
    ));
    
    // Trust Badge 1 - Shipping
    $wp_customize->add_setting('trust_badge_1_text', array(
        'default'           => __('Free shipping on all orders over $100', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_1_text', array(
        'label'       => __('Badge 1 - Shipping Text', 'aaapos'),
        'description' => __('Enter text for the first trust badge', 'aaapos'),
        'section'     => 'aaapos_trust_badges',
        'type'        => 'text',
        'priority'    => 20,
    ));
    
    // Enable/Disable Badge 1
    $wp_customize->add_setting('trust_badge_1_enable', array(
        'default'           => true,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_1_enable', array(
        'label'    => __('Enable Badge 1', 'aaapos'),
        'section'  => 'aaapos_trust_badges',
        'type'     => 'checkbox',
        'priority' => 25,
    ));
    
    // Trust Badge 2 - Returns
    $wp_customize->add_setting('trust_badge_2_text', array(
        'default'           => __('14 days easy refund & returns', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_2_text', array(
        'label'       => __('Badge 2 - Returns Text', 'aaapos'),
        'description' => __('Enter text for the second trust badge', 'aaapos'),
        'section'     => 'aaapos_trust_badges',
        'type'        => 'text',
        'priority'    => 30,
    ));
    
    // Enable/Disable Badge 2
    $wp_customize->add_setting('trust_badge_2_enable', array(
        'default'           => true,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_2_enable', array(
        'label'    => __('Enable Badge 2', 'aaapos'),
        'section'  => 'aaapos_trust_badges',
        'type'     => 'checkbox',
        'priority' => 35,
    ));
    
    // Trust Badge 3 - Taxes
    $wp_customize->add_setting('trust_badge_3_text', array(
        'default'           => __('Product taxes and customs duties included', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_3_text', array(
        'label'       => __('Badge 3 - Taxes Text', 'aaapos'),
        'description' => __('Enter text for the third trust badge', 'aaapos'),
        'section'     => 'aaapos_trust_badges',
        'type'        => 'text',
        'priority'    => 40,
    ));
    
    // Enable/Disable Badge 3
    $wp_customize->add_setting('trust_badge_3_enable', array(
        'default'           => true,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('trust_badge_3_enable', array(
        'label'    => __('Enable Badge 3', 'aaapos'),
        'section'  => 'aaapos_trust_badges',
        'type'     => 'checkbox',
        'priority' => 45,
    ));

    // ========================================
    // SECURE PAYMENT BADGES SECTION
    // ========================================
    
    $wp_customize->add_section('aaapos_secure_payments', array(
        'title'       => __('Secure Payment Badges', 'aaapos'),
        'description' => __('Payment icons are synced with Footer payment methods. Upload payment icons in Appearance → Customize → Footer Settings.', 'aaapos'),
        'priority'    => 161,
        'panel'       => 'woocommerce',
    ));
    
    // Enable/Disable Secure Payments
    $wp_customize->add_setting('show_secure_payments', array(
        'default'           => true,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_secure_payments', array(
        'label'       => __('Show Secure Payment Badges', 'aaapos'),
        'description' => __('Display payment method icons on single product pages', 'aaapos'),
        'section'     => 'aaapos_secure_payments',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));
    
    // Secure Payments Title
    $wp_customize->add_setting('secure_payments_title', array(
        'default'           => __('Secure payments:', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('secure_payments_title', array(
        'label'       => __('Title Text', 'aaapos'),
        'description' => __('Text shown before payment icons', 'aaapos'),
        'section'     => 'aaapos_secure_payments',
        'type'        => 'text',
        'priority'    => 20,
    ));

    // ========================================
// PRODUCT SHARE BUTTONS SECTION
// ========================================

$wp_customize->add_section('aaapos_product_share', array(
    'title'       => __('Product Share Buttons', 'aaapos'),
    'description' => __('Configure social sharing buttons on single product pages', 'aaapos'),
    'priority'    => 162,
    'panel'       => 'woocommerce',
));

// Enable/Disable Product Share Buttons
$wp_customize->add_setting('show_product_share', array(
    'default'           => true,
    'sanitize_callback' => 'absint',
    'transport'         => 'refresh',
));

$wp_customize->add_control('show_product_share', array(
    'label'       => __('Show Product Share Buttons', 'aaapos'),
    'description' => __('Display social sharing buttons on single product pages', 'aaapos'),
    'section'     => 'aaapos_product_share',
    'type'        => 'checkbox',
    'priority'    => 10,
));

// Share Section Title
$wp_customize->add_setting('product_share_title', array(
    'default'           => __('Share this post', 'aaapos'),
    'sanitize_callback' => 'sanitize_text_field',
    'transport'         => 'refresh',
));

$wp_customize->add_control('product_share_title', array(
    'label'       => __('Share Title Text', 'aaapos'),
    'description' => __('Text shown before share buttons', 'aaapos'),
    'section'     => 'aaapos_product_share',
    'type'        => 'text',
    'priority'    => 20,
));
}
add_action('customize_register', 'aaapos_trust_badges_customizer');