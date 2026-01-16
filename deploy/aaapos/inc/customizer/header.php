<?php
/**
 * Header and Topbar customizer settings
 */
function mr_header_customizer($wp_customize)
{
    // Header Section
    $wp_customize->add_section("mr_header", [
        "title" => __("Header & Topbar Settings", "macedon-ranges"),
        "priority" => 30,
    ]);

    // ===================================
    // TOP BAR SETTINGS
    // ===================================

    $wp_customize->add_setting("show_top_bar", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_top_bar", [
        "label" => __("Show Top Bar", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 10,
    ]);

    $wp_customize->add_setting("topbar_phone", [
        "default" => "+61 3 5420 0000",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("topbar_phone", [
        "label" => __("Top Bar Phone", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "text",
        "priority" => 20,
    ]);

    $wp_customize->add_setting("topbar_email", [
        "default" => "info@macedonrangesproduce.com",
        "sanitize_callback" => "sanitize_email",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("topbar_email", [
        "label" => __("Top Bar Email", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "email",
        "priority" => 30,
    ]);

    $wp_customize->add_setting("topbar_promo_text", [
        "default" => "Fresh local produce delivered daily!",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("topbar_promo_text", [
        "label" => __("Top Bar Promo Text", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "text",
        "priority" => 40,
    ]);

    // ===================================
    // HEADER SETTINGS
    // ===================================

    $wp_customize->add_setting("sticky_header", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("sticky_header", [
        "label" => __("Sticky Header", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 50,
    ]);

    // ===================================
    // HEADER ELEMENTS VISIBILITY
    // ===================================

    $wp_customize->add_setting("show_search_bar", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_search_bar", [
        "label" => __("Show Search Bar", "macedon-ranges"),
        "description" => __("Display search bar in header", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 55,
    ]);

    // NEW: Show/Hide Account Icon
    $wp_customize->add_setting("show_account_icon", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh", // Use refresh to regenerate HTML
    ]);

    $wp_customize->add_control("show_account_icon", [
        "label" => __("Show Account/Profile Icon", "macedon-ranges"),
        "description" => __("Display account login/profile link in header", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 56,
    ]);

    // NEW: Show/Hide Cart Icon
    $wp_customize->add_setting("show_cart_icon", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh", // Use refresh to regenerate HTML
    ]);

    $wp_customize->add_control("show_cart_icon", [
        "label" => __("Show Shopping Cart Icon", "macedon-ranges"),
        "description" => __("Display shopping cart in header (WooCommerce required)", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 57,
    ]);

    // ===================================
    // CART ICON STYLE (only if cart is shown)
    // ===================================

    $wp_customize->add_setting("cart_icon_style", [
        "default" => "icon-count",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("cart_icon_style", [
        "label" => __("Cart Icon Style", "macedon-ranges"),
        "description" => __("Choose how to display the cart icon", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "select",
        "choices" => [
            "icon-only" => __("Icon Only", "macedon-ranges"),
            "icon-count" => __("Icon + Item Count", "macedon-ranges"),
            "icon-total" => __("Icon + Cart Total", "macedon-ranges"),
        ],
        "priority" => 58,
        "active_callback" => function() {
            return get_theme_mod("show_cart_icon", true);
        },
    ]);
}
add_action("customize_register", "mr_header_customizer");