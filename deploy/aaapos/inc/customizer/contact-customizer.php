<?php
/**
 * Contact Page Customizer Settings
 * Complete customization with show/hide controls
 */

/**
 * Sanitize Google Maps embed code - Extract URL from iframe
 */
function aaapos_sanitize_map_url($input) {
    if (empty($input)) {
        return '';
    }
    
    $input = html_entity_decode($input, ENT_QUOTES, 'UTF-8');
    $input = trim(preg_replace('/\s+/', ' ', $input));
    
    if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/', $input, $matches)) {
        return esc_url_raw(trim($matches[1]));
    }
    
    if (preg_match('/(https?:\/\/www\.google\.com\/maps\/embed[^\s<>"\']+)/', $input, $matches)) {
        return esc_url_raw($matches[1]);
    }
    
    if (strpos($input, 'google.com/maps/embed') !== false) {
        if (preg_match('/(https?:\/\/[^\s]+)/', $input, $matches)) {
            return esc_url_raw($matches[1]);
        }
        return esc_url_raw($input);
    }
    
    return '';
}

function aaapos_contact_page_customizer($wp_customize) {
    
    // ===================================
    // CONTACT PAGE SECTION
    // ===================================
    $wp_customize->add_section('aaapos_contact_page', array(
        'title'    => __('Contact Page', 'aaapos-prime'),
        'priority' => 150,
    ));

    // ===================================
    // CONTACT DETAILS
    // ===================================
    
    // Phone Number
    $wp_customize->add_setting('contact_phone', array(
        'default'           => '03 5427 3552',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_phone', array(
        'label'    => __('Phone Number', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 5,
    ));

    // Email Address
    $wp_customize->add_setting('contact_email', array(
        'default'           => 'support@aaapos.com',
        'sanitize_callback' => 'sanitize_email',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_email', array(
        'label'    => __('Email Address', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'email',
        'priority' => 6,
    ));

    // Physical Address
    $wp_customize->add_setting('contact_address', array(
        'default'           => '123 Farm Road, AAAPOS VIC 3440',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_address', array(
        'label'    => __('Physical Address', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'textarea',
        'priority' => 7,
    ));

    // ===================================
    // EMAIL SETTINGS
    // ===================================
    
    $wp_customize->add_setting("contact_form_email", array(
        "default" => get_option("admin_email"),
        "sanitize_callback" => "sanitize_email",
    ));

    $wp_customize->add_control("contact_form_email", array(
        "label" => __("Contact Form Email", "aaapos-prime"),
        "description" => __("Email address to receive contact form submissions", "aaapos-prime"),
        "section" => "aaapos_contact_page",
        "type" => "email",
        "priority" => 10,
    ));

    // ===================================
    // HERO SECTION
    // ===================================
    
    // Show/Hide Hero Section
    $wp_customize->add_setting('contact_show_hero', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_hero', array(
        'label'    => __('Show Hero Section', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 15,
    ));

    // Hero Badge
    $wp_customize->add_setting('contact_hero_badge', array(
        'default'           => 'Get in Touch',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hero_badge', array(
        'label'    => __('Hero Badge Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 20,
    ));

    // Hero Title
    $wp_customize->add_setting('contact_hero_title', array(
        'default'           => 'Let\'s Start a Conversation',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hero_title', array(
        'label'    => __('Hero Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 30,
    ));

    // Hero Subtitle
    $wp_customize->add_setting('contact_hero_subtitle', array(
        'default'           => 'Whether you have a question about products, pricing, or anything else, our team is ready to answer all your questions.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hero_subtitle', array(
        'label'    => __('Hero Subtitle', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'textarea',
        'priority' => 40,
    ));

    // ===================================
    // QUICK STATS (Hero Stats)
    // ===================================
    
    // Show/Hide Quick Stats
    $wp_customize->add_setting('contact_show_quick_stats', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_quick_stats', array(
        'label'    => __('Show Quick Stats Section', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 42,
    ));

    // Show/Hide Stat 1
    $wp_customize->add_setting('contact_show_stat1', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_stat1', array(
        'label'    => __('Show Stat 1', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 44,
    ));

    // Stat 1 - Call Anytime
    $wp_customize->add_setting('contact_stat1_title', array(
        'default'           => 'Call Anytime',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat1_title', array(
        'label'    => __('Quick Stat 1 - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 45,
    ));

    $wp_customize->add_setting('contact_stat1_text', array(
        'default'           => 'Mon-Sat, 9AM-6PM',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat1_text', array(
        'label'    => __('Quick Stat 1 - Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 46,
    ));

    // Show/Hide Stat 2
    $wp_customize->add_setting('contact_show_stat2', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_stat2', array(
        'label'    => __('Show Stat 2', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 46,
    ));

    // Stat 2 - Quick Response
    $wp_customize->add_setting('contact_stat2_title', array(
        'default'           => 'Quick Response',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat2_title', array(
        'label'    => __('Quick Stat 2 - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 47,
    ));

    $wp_customize->add_setting('contact_stat2_text', array(
        'default'           => 'Within 24 hours',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat2_text', array(
        'label'    => __('Quick Stat 2 - Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 48,
    ));

    // Show/Hide Stat 3
    $wp_customize->add_setting('contact_show_stat3', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_stat3', array(
        'label'    => __('Show Stat 3', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 48,
    ));

    // Stat 3 - Visit Us
    $wp_customize->add_setting('contact_stat3_title', array(
        'default'           => 'Visit Us',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat3_title', array(
        'label'    => __('Quick Stat 3 - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 49,
    ));

    $wp_customize->add_setting('contact_stat3_text', array(
        'default'           => 'AAAPOS',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_stat3_text', array(
        'label'    => __('Quick Stat 3 - Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 50,
    ));

    // ===================================
    // CONTACT FORM SECTION
    // ===================================
    
    // Show/Hide Contact Form
    $wp_customize->add_setting('contact_show_form', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_form', array(
        'label'    => __('Show Contact Form', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 55,
    ));

    // Form Title
    $wp_customize->add_setting('contact_form_title', array(
        'default'           => 'Send Us a Message',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_form_title', array(
        'label'    => __('Form Section Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 60,
    ));

    // Form Intro
    $wp_customize->add_setting('contact_form_intro', array(
        'default'           => 'Fill out the form below and we\'ll get back to you as soon as possible.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_form_intro', array(
        'label'    => __('Form Introduction Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'textarea',
        'priority' => 70,
    ));

    // ===================================
    // SIDEBAR - CONTACT INFO
    // ===================================
    
    // Show/Hide Contact Info Block
    $wp_customize->add_setting('contact_show_info_block', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_info_block', array(
        'label'    => __('Show Contact Info Block', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 75,
    ));

    // Contact Info Title
    $wp_customize->add_setting('contact_info_title', array(
        'default'           => 'Contact Information',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_info_title', array(
        'label'    => __('Contact Info Block - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 80,
    ));

    // Contact Info Text
    $wp_customize->add_setting('contact_info_text', array(
        'default'           => 'Reach out through any of these channels—we\'re here to help!',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_info_text', array(
        'label'    => __('Contact Info Block - Text', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'textarea',
        'priority' => 90,
    ));

    // ===================================
    // BUSINESS HOURS
    // ===================================
    
    // Show/Hide Business Hours
    $wp_customize->add_setting('contact_show_hours', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_hours', array(
        'label'    => __('Show Business Hours', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 95,
    ));

    // Business Hours Title
    $wp_customize->add_setting('contact_hours_title', array(
        'default'           => 'Business Hours',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hours_title', array(
        'label'    => __('Business Hours - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 100,
    ));

    // Weekdays Hours
    $wp_customize->add_setting('contact_hours_weekday', array(
        'default'           => '9:00 AM - 6:00 PM',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hours_weekday', array(
        'label'    => __('Weekday Hours (Mon-Fri)', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 110,
    ));

    // Saturday Hours
    $wp_customize->add_setting('contact_hours_saturday', array(
        'default'           => '10:00 AM - 4:00 PM',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hours_saturday', array(
        'label'    => __('Saturday Hours', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 120,
    ));

    // Sunday Hours
    $wp_customize->add_setting('contact_hours_sunday', array(
        'default'           => 'Closed',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_hours_sunday', array(
        'label'    => __('Sunday Hours', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 130,
    ));

    // ===================================
    // SOCIAL MEDIA
    // ===================================
    
    // Show/Hide Social Block
    $wp_customize->add_setting('contact_show_social', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_social', array(
        'label'    => __('Show Social Media Block', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 135,
    ));

    // Social Block Title
    $wp_customize->add_setting('contact_social_title', array(
        'default'           => 'Connect With Us',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_social_title', array(
        'label'    => __('Social Block - Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 140,
    ));

    // Social Block Text
    $wp_customize->add_setting('contact_social_text', array(
        'default'           => 'Follow us for updates, tips, and special offers',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_social_text', array(
        'label'       => __('Social Block - Text', 'aaapos-prime'),
        'description' => __('Social media links are managed in Footer settings', 'aaapos-prime'),
        'section'     => 'aaapos_contact_page',
        'type'        => 'text',
        'priority'    => 150,
    ));

    // ===================================
    // MAP SECTION
    // ===================================

    // Show/Hide Map Section
    $wp_customize->add_setting('contact_show_map', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_show_map', array(
        'label'    => __('Show Map Section', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'checkbox',
        'priority' => 155,
    ));

    // Map Embed
    $wp_customize->add_setting('contact_map_embed', array(
        'default'           => '',
        'sanitize_callback' => 'aaapos_sanitize_map_url',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('contact_map_embed', array(
        'label'       => __('Google Maps Embed', 'aaapos-prime'),
        'description' => __('Paste your Google Maps iframe code or embed URL here', 'aaapos-prime'),
        'section'     => 'aaapos_contact_page',
        'type'        => 'textarea',
        'priority'    => 160,
        'input_attrs' => array(
            'placeholder' => 'Paste iframe code or URL here...',
            'rows' => 4,
        ),
    ));

    // Map Title
    $wp_customize->add_setting('contact_map_title', array(
        'default'           => 'Find Us Here',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_map_title', array(
        'label'    => __('Map Section Title', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 170,
    ));

    // Map Subtitle
    $wp_customize->add_setting('contact_map_subtitle', array(
        'default'           => 'Visit our store and experience our products firsthand',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('contact_map_subtitle', array(
        'label'    => __('Map Section Subtitle', 'aaapos-prime'),
        'section'  => 'aaapos_contact_page',
        'type'     => 'text',
        'priority' => 180,
    ));
}
add_action('customize_register', 'aaapos_contact_page_customizer');