<?php
/**
 * Hero section customizer settings
 * Matches the hero-section.php layout
 * (eyebrow, two-line title, subtitle, feature bullets, buttons,
 * product carousel images, "other goods" cards).
 *
 * UPDATED: Notification banner removed completely.
 * UPDATED: Feature icons are now media uploads instead of text slugs.
 * UPDATED: "Code Label" field removed from Other Goods cards (unused).
 */

function mr_hero_customizer($wp_customize) {
    // Hero Section
    $wp_customize->add_section('mr_hero', array(
        'title' => __('Hero Section', 'macedon-ranges'),
        'priority' => 40,
    ));

    // === BACKGROUND IMAGE ===
    $wp_customize->add_setting('hero_bg_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_bg_image', array(
        'label' => __('Hero Background Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 10,
    )));

    // === EYEBROW / TITLE / SUBTITLE ===
    $wp_customize->add_setting('hero_eyebrow', array(
        'default' => 'TRUSTED FOR 25+ YEARS',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_eyebrow', array(
        'label' => __('Eyebrow Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 20,
    ));

    $wp_customize->add_setting('hero_title_line1', array(
        'default' => 'AAAPOS RetailManager',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title_line1', array(
        'label' => __('Title - Line 1', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 30,
    ));

    $wp_customize->add_setting('hero_title_line2', array(
        'default' => 'POS Software',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title_line2', array(
        'label' => __('Title - Line 2', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 40,
    ));

    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Formerly MYOB RetailManager, trusted by retailers across Australia, New Zealand, Asia and the Pacific Islands for over 25 years.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Subtitle', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'textarea',
        'priority' => 50,
    ));

    // === FEATURE BULLETS (2) - icon is now a media upload ===
    // Feature 1
    $wp_customize->add_setting('hero_feature_1_icon', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_feature_1_icon', array(
        'label' => __('Feature 1 - Icon', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 60,
    )));

    $wp_customize->add_setting('hero_feature_1_label', array(
        'default' => 'Multi-Store Sync',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_feature_1_label', array(
        'label' => __('Feature 1 - Label', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 61,
    ));

    // Feature 2
    $wp_customize->add_setting('hero_feature_2_icon', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_feature_2_icon', array(
        'label' => __('Feature 2 - Icon', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'priority' => 62,
    )));

    $wp_customize->add_setting('hero_feature_2_label', array(
        'default' => '7 Days Support',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_feature_2_label', array(
        'label' => __('Feature 2 - Label', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 63,
    ));

    // === BUTTONS ===
    $wp_customize->add_setting('hero_primary_btn_text', array(
        'default' => 'RetailManager Update',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_btn_text', array(
        'label' => __('Primary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 70,
    ));

    $wp_customize->add_setting('hero_primary_btn_link', array(
        'default' => '/shop',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_btn_link', array(
        'label' => __('Primary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 71,
    ));

    $wp_customize->add_setting('hero_secondary_btn_text', array(
        'default' => 'Support',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_btn_text', array(
        'label' => __('Secondary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'priority' => 72,
    ));

    $wp_customize->add_setting('hero_secondary_btn_link', array(
        'default' => '/support',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_btn_link', array(
        'label' => __('Secondary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'priority' => 73,
    ));

    // === PRODUCT CAROUSEL (right side sliding images) ===
    $wp_customize->add_setting('hero_show_product_carousel', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_show_product_carousel', array(
        'label' => __('Show Product Carousel', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'priority' => 90,
    ));

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("hero_carousel_image_{$i}", array(
            'default' => '',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "hero_carousel_image_{$i}", array(
            'label' => sprintf(__('Carousel Image %d', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'mime_type' => 'image',
            'active_callback' => function() {
                return get_theme_mod('hero_show_product_carousel', true);
            },
            'priority' => 90 + ($i * 2) - 1,
        )));

        $wp_customize->add_setting("hero_carousel_link_{$i}", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_carousel_link_{$i}", array(
            'label' => sprintf(__('Carousel Image %d - Link URL (optional)', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'url',
            'active_callback' => function() {
                return get_theme_mod('hero_show_product_carousel', true);
            },
            'priority' => 90 + ($i * 2),
        ));
    }

    // === "OTHER GOODS" CARDS (3 cards below the hero) ===
    $wp_customize->add_setting('hero_show_other_goods', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_show_other_goods', array(
        'label' => __('Show "Other Goods" Cards', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'priority' => 100,
    ));

    $other_goods_defaults = array(
        1 => array(
            'title' => 'RM-MultiStore',
            'desc' => 'Head office data aggregation tool to manage multiple stores from one dashboard, with real-time sync.',
            'ribbon' => '',
        ),
        2 => array(
            'title' => 'Webstore Manager',
            'desc' => 'Sync stock and orders automatically with Shopify, WooCommerce, eBay and BigCommerce.',
            'ribbon' => '',
        ),
        3 => array(
            'title' => 'RM Mobile App',
            'desc' => 'Access AAAPOS RetailManager on the go with the free companion app for Android and iOS.',
            'ribbon' => 'Coming soon',
        ),
    );

    foreach ($other_goods_defaults as $i => $defaults) {
        $base_priority = 100 + ($i * 10);

        $wp_customize->add_setting("hero_goods_{$i}_title", array(
            'default' => $defaults['title'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_goods_{$i}_title", array(
            'label' => sprintf(__('Card %d - Title', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'text',
            'active_callback' => function() {
                return get_theme_mod('hero_show_other_goods', true);
            },
            'priority' => $base_priority + 2,
        ));

        $wp_customize->add_setting("hero_goods_{$i}_desc", array(
            'default' => $defaults['desc'],
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_goods_{$i}_desc", array(
            'label' => sprintf(__('Card %d - Description', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'textarea',
            'active_callback' => function() {
                return get_theme_mod('hero_show_other_goods', true);
            },
            'priority' => $base_priority + 3,
        ));

        $wp_customize->add_setting("hero_goods_{$i}_image", array(
            'default' => '',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "hero_goods_{$i}_image", array(
            'label' => sprintf(__('Card %d - Image', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'mime_type' => 'image',
            'active_callback' => function() {
                return get_theme_mod('hero_show_other_goods', true);
            },
            'priority' => $base_priority + 4,
        )));

        $wp_customize->add_setting("hero_goods_{$i}_ribbon", array(
            'default' => $defaults['ribbon'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_goods_{$i}_ribbon", array(
            'label' => sprintf(__('Card %d - Ribbon Text (optional)', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'text',
            'active_callback' => function() {
                return get_theme_mod('hero_show_other_goods', true);
            },
            'priority' => $base_priority + 5,
        ));

        $wp_customize->add_setting("hero_goods_{$i}_link", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_goods_{$i}_link", array(
            'label' => sprintf(__('Card %d - Link URL (optional)', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'url',
            'active_callback' => function() {
                return get_theme_mod('hero_show_other_goods', true);
            },
            'priority' => $base_priority + 6,
        ));
    }
}
add_action('customize_register', 'mr_hero_customizer');