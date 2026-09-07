<?php
/**
 * Hero Section - CLASSIC design controls
 *
 * Title + highlight / image slideshow or video background /
 * notification banner / product carousel with price cards. Registered
 * into the shared "mr_hero" Customizer section by mr_hero_customizer()
 * in inc/customizer/hero.php. Controls only show when Hero Design =
 * Classic (see mr_hero_is_classic()).
 */

if (!function_exists('mr_hero_classic_controls')) {
    function mr_hero_classic_controls($wp_customize) {

    // =========================================================================
    // CLASSIC DESIGN CONTROLS
    // =========================================================================

    // === MEDIA TYPE (image slideshow vs. video background) ===
    $wp_customize->add_setting('hero_media_type', array(
        'default' => 'image',
        'sanitize_callback' => 'mr_hero_sanitize_media_type',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_media_type', array(
        'label' => __('Hero Background Type', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'select',
        'choices' => array(
            'image' => __('Image Slideshow', 'macedon-ranges'),
            'video' => __('Single Video Background', 'macedon-ranges'),
        ),
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 300,
    ));

    // === VIDEO SETTINGS (only when media type = video) ===
    $wp_customize->add_setting('hero_video_webm', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_video_webm', array(
        'label' => __('Hero Background Video (WebM)', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'video/webm',
        'active_callback' => 'mr_hero_is_classic_video',
        'priority' => 301,
    )));

    $wp_customize->add_setting('hero_video_fallback', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_video_fallback', array(
        'label' => __('Video Fallback Image', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'active_callback' => 'mr_hero_is_classic_video',
        'priority' => 302,
    )));

    $wp_customize->add_setting('hero_video_loop', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_video_loop', array(
        'label' => __('Loop Video', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic_video',
        'priority' => 303,
    ));

    $wp_customize->add_setting('hero_video_mute', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_video_mute', array(
        'label' => __('Mute Video', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic_video',
        'priority' => 304,
    ));

    $wp_customize->add_setting('hero_video_mobile_fallback', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_video_mobile_fallback', array(
        'label' => __('Use Image Fallback on Mobile', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic_video',
        'priority' => 305,
    ));

    // === IMAGE SLIDESHOW SETTINGS (only when media type = image) ===
    $wp_customize->add_setting('hero_enable_slideshow', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_enable_slideshow', array(
        'label' => __('Enable Image Slideshow', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic_image',
        'priority' => 310,
    ));

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("hero_slide_{$i}", array(
            'default' => '',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "hero_slide_{$i}", array(
            'label' => $i === 1
                ? __('Slide 1 Image (Primary/Static)', 'macedon-ranges')
                : sprintf(__('Slide %d Image', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'mime_type' => 'image',
            'active_callback' => 'mr_hero_is_classic_image',
            'priority' => 310 + $i,
        )));
    }

    $wp_customize->add_setting('hero_slideshow_speed', array(
        'default' => 5000,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_slideshow_speed', array(
        'label' => __('Slideshow Speed (ms)', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 2000,
            'max' => 10000,
            'step' => 500,
        ),
        'active_callback' => 'mr_hero_is_classic_image',
        'priority' => 315,
    ));

    // === OVERLAY DARKNESS (applies to image and video backgrounds) ===
    $wp_customize->add_setting('hero_overlay_opacity', array(
        'default' => 0.6,
        'sanitize_callback' => 'mr_sanitize_float',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_overlay_opacity', array(
        'label' => __('Background Overlay Darkness', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 0,
            'max' => 1,
            'step' => 0.05,
        ),
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 316,
    ));

    // === PRODUCT CAROUSEL (right side, with name/price/link per slide) ===
    // Named hero_classic_show_product_carousel (not hero_show_product_carousel)
    // because the Modern design above already uses that mod key.
    $wp_customize->add_setting('hero_classic_show_product_carousel', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_classic_show_product_carousel', array(
        'label' => __('Show Product Carousel', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 320,
    ));

    $classic_product_defaults = array(
        1 => array('name' => 'RetailManager POS', 'price' => '$1,299'),
        2 => array('name' => 'Premium Package', 'price' => '$1,999'),
        3 => array('name' => 'Enterprise Solution', 'price' => '$2,999'),
        4 => array('name' => 'Cloud Edition', 'price' => '$99/mo'),
    );

    foreach ($classic_product_defaults as $i => $defaults) {
        $base_priority = 320 + ($i * 5);

        $wp_customize->add_setting("hero_product_{$i}_image", array(
            'default' => '',
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, "hero_product_{$i}_image", array(
            'label' => sprintf(__('Product %d - Image', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'mime_type' => 'image',
            'active_callback' => 'mr_hero_is_classic',
            'priority' => $base_priority + 1,
        )));

        $wp_customize->add_setting("hero_product_{$i}_name", array(
            'default' => $defaults['name'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_product_{$i}_name", array(
            'label' => sprintf(__('Product %d - Name', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'text',
            'active_callback' => 'mr_hero_is_classic',
            'priority' => $base_priority + 2,
        ));

        $wp_customize->add_setting("hero_product_{$i}_price", array(
            'default' => $defaults['price'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_product_{$i}_price", array(
            'label' => sprintf(__('Product %d - Price', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'text',
            'active_callback' => 'mr_hero_is_classic',
            'priority' => $base_priority + 3,
        ));

        $wp_customize->add_setting("hero_product_{$i}_link", array(
            'default' => '#',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("hero_product_{$i}_link", array(
            'label' => sprintf(__('Product %d - Link', 'macedon-ranges'), $i),
            'section' => 'mr_hero',
            'type' => 'url',
            'active_callback' => 'mr_hero_is_classic',
            'priority' => $base_priority + 4,
        ));
    }

    // === NOTIFICATION BANNER ===
    $wp_customize->add_setting('hero_show_notification', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_show_notification', array(
        'label' => __('Show Notification Banner', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 340,
    ));

    $wp_customize->add_setting('hero_notification_icon', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_notification_icon', array(
        'label' => __('Notification Icon', 'macedon-ranges'),
        'section' => 'mr_hero',
        'mime_type' => 'image',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 341,
    )));

    $wp_customize->add_setting('hero_notification_title', array(
        'default' => 'RetailManager Update Available!',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_notification_title', array(
        'label' => __('Notification Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 342,
    ));

    $wp_customize->add_setting('hero_notification_text', array(
        'default' => 'Discover the latest features, improvements, and enhancements that will boost your retail operations.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_notification_text', array(
        'label' => __('Notification Description', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'textarea',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 343,
    ));

    $wp_customize->add_setting('hero_notification_btn_text', array(
        'default' => 'LEARN ABOUT NEW UPDATES',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_notification_btn_text', array(
        'label' => __('Notification Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 344,
    ));

    $wp_customize->add_setting('hero_notification_btn_link', array(
        'default' => '#',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_notification_btn_link', array(
        'label' => __('Notification Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 345,
    ));

    // === CONTENT (title / subtitle / buttons) ===
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Premium Feed & Supplies',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 350,
    ));

    $wp_customize->add_setting('hero_title_highlight', array(
        'default' => 'For Your Beloved Pets & Livestock',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_title_highlight', array(
        'label' => __('Hero Title Highlight', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 351,
    ));

    // Named hero_classic_subtitle (not hero_subtitle) because the Modern
    // design above already uses the 'hero_subtitle' mod key.
    $wp_customize->add_setting('hero_classic_subtitle', array(
        'default' => 'Your trusted local supplier for premium pet food, animal feed, farm supplies, and everything your animals need. From dogs and cats to horses, poultry, and livestock.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_classic_subtitle', array(
        'label' => __('Hero Subtitle', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'textarea',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 352,
    ));

    $wp_customize->add_setting('hero_primary_button_text', array(
        'default' => 'Shop All Products',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_button_text', array(
        'label' => __('Primary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 353,
    ));

    $wp_customize->add_setting('hero_primary_button_link', array(
        'default' => '/shop',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_primary_button_link', array(
        'label' => __('Primary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 354,
    ));

    $wp_customize->add_setting('hero_secondary_button_text', array(
        'default' => 'About Our Store',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_button_text', array(
        'label' => __('Secondary Button Text', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'text',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 355,
    ));

    $wp_customize->add_setting('hero_secondary_button_link', array(
        'default' => '/about',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('hero_secondary_button_link', array(
        'label' => __('Secondary Button Link', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'url',
        'active_callback' => 'mr_hero_is_classic',
        'priority' => 356,
    ));
    }
}