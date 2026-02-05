<?php
/**
 * Hero section customizer settings
 */
function mr_hero_customizer($wp_customize) {
    // Hero Section
    $wp_customize->add_section('mr_hero', array(
        'title' => __('Hero Section', 'macedon-ranges'),
        'priority' => 40,
    ));

    // Show Hero Section
    $wp_customize->add_setting('show_hero', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('show_hero', array(
        'label' => __('Show Hero Section', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'priority' => 10,
    ));

    // Enable Slideshow
    $wp_customize->add_setting('hero_enable_slideshow', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_enable_slideshow', array(
        'label' => __('Enable Slideshow', 'macedon-ranges'),
        'description' => __('When disabled, only the first slide image will be displayed as a static background.', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'priority' => 15,
    ));

    // Hero Slide 1 Image
    $wp_customize->add_setting('hero_slide_1', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_slide_1', array(
        'label' => __('Slide 1 Image (Primary/Static)', 'macedon-ranges'),
        'description' => __('This image is used as the static background when slideshow is disabled.', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 20,
    )));

    // Hero Slide 2 Image
    $wp_customize->add_setting('hero_slide_2', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_slide_2', array(
        'label' => __('Slide 2 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 30,
    )));

    // Hero Slide 3 Image
    $wp_customize->add_setting('hero_slide_3', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_slide_3', array(
        'label' => __('Slide 3 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 40,
    )));

    // Hero Slide 4 Image
    $wp_customize->add_setting('hero_slide_4', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_slide_4', array(
        'label' => __('Slide 4 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 50,
    )));

    // Badge Text
    $wp_customize->add_setting('hero_badge_text', array(
        'default' => '🐾 Quality Pet & Animal Supplies',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_badge_text', array(
        'label' => __('Badge Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 60,
    ));

    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Premium Feed & Supplies',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 70,
    ));

    // Hero Title Highlight
    $wp_customize->add_setting('hero_title_highlight', array(
        'default' => 'For Your Beloved Pets & Livestock',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title_highlight', array(
        'label' => __('Hero Title Highlight', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 80,
    ));

    // Hero Subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Your trusted local supplier for premium pet food, animal feed, farm supplies, and everything your animals need. From dogs and cats to horses, poultry, and livestock.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'textarea',
        'priority' => 90,
    ));

    // Primary Button Text
    $wp_customize->add_setting('hero_primary_button_text', array(
        'default' => 'Shop All Products',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_button_text', array(
        'label' => __('Primary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 100,
    ));

    // Primary Button Link
    $wp_customize->add_setting('hero_primary_button_link', array(
        'default' => '/shop',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_button_link', array(
        'label' => __('Primary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 110,
    ));

    // Secondary Button Text
    $wp_customize->add_setting('hero_secondary_button_text', array(
        'default' => 'About Our Store',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_button_text', array(
        'label' => __('Secondary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 120,
    ));

    // Secondary Button Link
    $wp_customize->add_setting('hero_secondary_button_link', array(
        'default' => '/about',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_button_link', array(
        'label' => __('Secondary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 130,
    ));

    // =========================================================================
    // HERO IMAGE CARDS SECTION
    // =========================================================================

    // Card 1 Settings
    $wp_customize->add_setting('hero_card_1_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_card_1_image', array(
        'label' => __('Card 1 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 140,
    )));

    $wp_customize->add_setting('hero_card_1_title', array(
        'default' => 'Dog Food',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_1_title', array(
        'label' => __('Card 1 Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 150,
    ));

    $wp_customize->add_setting('hero_card_1_link', array(
        'default' => '/product-category/dog-food',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_1_link', array(
        'label' => __('Card 1 Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 160,
    ));

    // Card 2 Settings
    $wp_customize->add_setting('hero_card_2_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_card_2_image', array(
        'label' => __('Card 2 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 170,
    )));

    $wp_customize->add_setting('hero_card_2_title', array(
        'default' => 'Cat Food',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_2_title', array(
        'label' => __('Card 2 Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 180,
    ));

    $wp_customize->add_setting('hero_card_2_link', array(
        'default' => '/product-category/cat-food',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_2_link', array(
        'label' => __('Card 2 Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 190,
    ));

    // Card 3 Settings
    $wp_customize->add_setting('hero_card_3_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_card_3_image', array(
        'label' => __('Card 3 Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 200,
    )));

    $wp_customize->add_setting('hero_card_3_title', array(
        'default' => 'Pet Toys',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_3_title', array(
        'label' => __('Card 3 Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 210,
    ));

    $wp_customize->add_setting('hero_card_3_link', array(
        'default' => '/product-category/pet-toys',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_card_3_link', array(
        'label' => __('Card 3 Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 220,
    ));

    // Slideshow Speed
    $wp_customize->add_setting('hero_slideshow_speed', array(
        'default' => 5000,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_slideshow_speed', array(
        'label' => __('Slideshow Speed (ms)', 'macedon-ranges'),
        'description' => __('Time between slide transitions in milliseconds. Only applies when slideshow is enabled.', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 2000,
            'max' => 10000,
            'step' => 500,
        ),
        'priority' => 230,
    ));
}
add_action('customize_register', 'mr_hero_customizer');