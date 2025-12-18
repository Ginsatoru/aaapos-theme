<?php
/**
 * WooCommerce Customizer Settings woocommerce.php
 * Updated with Multi-Checkbox Category Filter Controls
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Load custom control class
 */
require_once get_template_directory() . "/inc/customizer/multiple-control.php";

/**
 * Add WooCommerce customizer settings
 */
function mr_woocommerce_customizer($wp_customize)
{
    // Add WooCommerce Section
    $wp_customize->add_section("mr_woocommerce_settings", [
        "title" => __("WooCommerce Settings", "macedon-ranges"),
        "priority" => 120,
    ]);

    // ============================================
    // CATEGORY FILTER SETTINGS (NEW - WITH MULTI-CHECKBOX)
    // ============================================

    // Enable/Disable Category Filter
    $wp_customize->add_setting("enable_category_filter", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("enable_category_filter", [
        "label" => __("Enable Category Filter", "macedon-ranges"),
        "description" => __(
            "Show category filter buttons on shop page",
            "macedon-ranges",
        ),
        "section" => "mr_woocommerce_settings",
        "type" => "checkbox",
        "priority" => 5,
    ]);

    // Select Categories to Display (Multi-Checkbox)
    $categories = get_terms([
        "taxonomy" => "product_cat",
        "hide_empty" => false,
    ]);

    $category_choices = [];
    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_choices[$category->term_id] =
                $category->name . " (" . $category->count . ")";
        }
    }

    $wp_customize->add_setting("category_filter_categories", [
        "default" => "",
        "sanitize_callback" => "aaapos_sanitize_category_filter_multi",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control(
        new AAAPOS_Checkbox_Multiple_Control(
            $wp_customize,
            "category_filter_categories",
            [
                "label" => __("Select Categories to Display", "macedon-ranges"),
                "description" => __(
                    'Check categories to show in filter. Use "Select All" to show all categories. Unchecking all will hide the filter.',
                    "macedon-ranges",
                ),
                "section" => "mr_woocommerce_settings",
                "choices" => $category_choices,
                "priority" => 6,
            ],
        ),
    );

    // ============================================
    // EXISTING SETTINGS
    // ============================================

    // Products Per Page
    $wp_customize->add_setting("products_per_page", [
        "default" => 12,
        "sanitize_callback" => "absint",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("products_per_page", [
        "label" => __("Products Per Page", "macedon-ranges"),
        "description" => __(
            "Number of products to display per page in shop",
            "macedon-ranges",
        ),
        "section" => "mr_woocommerce_settings",
        "type" => "number",
        "input_attrs" => [
            "min" => 1,
            "max" => 100,
            "step" => 1,
        ],
        "priority" => 10,
    ]);

    // Shop Layout
    $wp_customize->add_setting("shop_layout", [
        "default" => "grid",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("shop_layout", [
        "label" => __("Shop Layout", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "select",
        "choices" => [
            "grid" => __("Grid", "macedon-ranges"),
            "list" => __("List", "macedon-ranges"),
        ],
        "priority" => 15,
    ]);

    // Products Per Row
    $wp_customize->add_setting("products_per_row", [
        "default" => 3,
        "sanitize_callback" => "absint",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("products_per_row", [
        "label" => __("Products Per Row", "macedon-ranges"),
        "description" => __(
            "Number of product columns in grid layout",
            "macedon-ranges",
        ),
        "section" => "mr_woocommerce_settings",
        "type" => "select",
        "choices" => [
            "2" => __("2 Columns", "macedon-ranges"),
            "3" => __("3 Columns", "macedon-ranges"),
            "4" => __("4 Columns", "macedon-ranges"),
            "5" => __("5 Columns", "macedon-ranges"),
        ],
        "priority" => 20,
    ]);

    // Show/Hide Product Rating
    $wp_customize->add_setting("show_product_rating", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_product_rating", [
        "label" => __("Show Product Rating", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "checkbox",
        "priority" => 25,
    ]);

    // Show/Hide Quick View
    $wp_customize->add_setting("show_quick_view", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_quick_view", [
        "label" => __("Enable Quick View", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "checkbox",
        "priority" => 30,
    ]);

    // Sale Badge Text
    $wp_customize->add_setting("sale_badge_text", [
        "default" => __("Sale", "macedon-ranges"),
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("sale_badge_text", [
        "label" => __("Sale Badge Text", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "text",
        "priority" => 35,
    ]);

    // Related Products Count
    $wp_customize->add_setting("related_products_count", [
        "default" => 4,
        "sanitize_callback" => "absint",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("related_products_count", [
        "label" => __("Related Products Count", "macedon-ranges"),
        "description" => __(
            "Number of related products to show on single product page",
            "macedon-ranges",
        ),
        "section" => "mr_woocommerce_settings",
        "type" => "number",
        "input_attrs" => [
            "min" => 0,
            "max" => 12,
            "step" => 1,
        ],
        "priority" => 40,
    ]);

    // Show/Hide Product Sidebar
    $wp_customize->add_setting("show_shop_sidebar", [
        "default" => false,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_shop_sidebar", [
        "label" => __("Show Shop Sidebar", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "checkbox",
        "priority" => 45,
    ]);

    // Cart Icon Style
    $wp_customize->add_setting("cart_icon_style", [
        "default" => "icon-count",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("cart_icon_style", [
        "label" => __("Cart Icon Style", "macedon-ranges"),
        "section" => "mr_woocommerce_settings",
        "type" => "select",
        "choices" => [
            "icon-only" => __("Icon Only", "macedon-ranges"),
            "icon-count" => __("Icon with Count", "macedon-ranges"),
            "icon-total" => __("Icon with Total", "macedon-ranges"),
        ],
        "priority" => 50,
    ]);
}
add_action("customize_register", "mr_woocommerce_customizer");

/**
 * Sanitize category filter multi-checkbox selection
 * Accepts comma-separated string and returns sanitized comma-separated string
 */
function aaapos_sanitize_category_filter_multi($input)
{
    // If input is empty, return empty string
    if (empty($input)) {
        return "";
    }

    // Convert to array if it's a string
    if (is_string($input)) {
        $input = explode(",", $input);
    }

    // If not an array at this point, return empty
    if (!is_array($input)) {
        return "";
    }

    // Sanitize each value
    $sanitized = array_map("absint", $input);

    // Remove any empty values
    $sanitized = array_filter($sanitized);

    // Return as comma-separated string
    return implode(",", $sanitized);
}
