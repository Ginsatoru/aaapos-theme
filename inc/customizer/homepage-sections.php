<?php
/**
 * Homepage sections customizer settings
 */
function mr_homepage_sections_customizer($wp_customize)
{
    // Homepage Sections
    $wp_customize->add_section("mr_homepage_sections", [
        "title" => __("Homepage Sections", "macedon-ranges"),
        "priority" => 45,
    ]);

    // ===================================
    // FEATURED PRODUCTS SECTION
    // ===================================

    $wp_customize->add_setting("show_featured_products", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_featured_products", [
        "label" => __("Show Featured Products Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 10,
    ]);

    $wp_customize->add_setting("featured_products_title", [
        "default" => "Featured Products",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("featured_products_title", [
        "label" => __("Featured Products Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 20,
    ]);

    $wp_customize->add_setting("featured_products_description", [
        "default" => "",
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("featured_products_description", [
        "label" => __("Featured Products Description", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "textarea",
        "priority" => 25,
    ]);

    $wp_customize->add_setting("featured_products_count", [
        "default" => 8,
        "sanitize_callback" => "absint",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("featured_products_count", [
        "label" => __("Number of Featured Products", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "number",
        "input_attrs" => [
            "min" => 1,
            "max" => 12,
            "step" => 1,
        ],
        "priority" => 30,
    ]);

    // ===================================
    // PRODUCT CATEGORIES BY SLUG
    // ===================================

    $wp_customize->add_setting("show_categories", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_categories", [
        "label" => __("Show Categories Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 40,
    ]);

    $wp_customize->add_setting("categories_title", [
        "default" => "Shop by Category",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("categories_title", [
        "label" => __("Categories Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 50,
    ]);

    $wp_customize->add_setting("categories_subtitle", [
        "default" =>
            "Quality feed and supplies for all your pets and livestock needs",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("categories_subtitle", [
        "label" => __("Categories Subtitle", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "textarea",
        "priority" => 55,
    ]);

    // Category Slugs Input
    $wp_customize->add_setting("categories_slugs", [
        "default" => "",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("categories_slugs", [
        "label" => __("Category Slugs", "macedon-ranges"),
        "description" => __(
            "Enter category slugs separated by commas. Example: dog-food, cat-food, bird-supplies, horse-feed. Leave empty to show all categories.",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "textarea",
        "priority" => 56,
    ]);

    // Fallback: Number of categories (used when slugs is empty)
    $wp_customize->add_setting("categories_count", [
        "default" => 6,
        "sanitize_callback" => "absint",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("categories_count", [
        "label" => __(
            "Number of Categories (if no slugs specified)",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "number",
        "input_attrs" => [
            "min" => 3,
            "max" => 12,
            "step" => 1,
        ],
        "priority" => 57,
    ]);

    // ===================================
    // SPECIAL DEALS SECTION - ENHANCED
    // ===================================

    $wp_customize->add_setting("show_deals", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_deals", [
        "label" => __("Show Special Deals Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 60,
    ]);

    $wp_customize->add_setting("deals_title", [
        "default" => "Special Deals",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("deals_title", [
        "label" => __("Deals Section Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 65,
    ]);

    $wp_customize->add_setting("deals_description", [
        "default" => "Limited offer!",
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("deals_description", [
        "label" => __("Deals Section Description", "macedon-ranges"),
        "description" => __(
            "Subtitle or tagline for the deals section",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "textarea",
        "priority" => 70,
    ]);

    // Background Image
    $wp_customize->add_setting("deals_background_image", [
        "default" => "",
        "sanitize_callback" => "esc_url_raw",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            "deals_background_image",
            [
                "label" => __("Background Image", "macedon-ranges"),
                "description" => __(
                    "Upload a background image for the deals section. Recommended size: 1920x800px. Leave empty to use default (deal.png).",
                    "macedon-ranges",
                ),
                "section" => "mr_homepage_sections",
                "settings" => "deals_background_image",
                "priority" => 72,
            ],
        ),
    );

    // Background Overlay Opacity
    $wp_customize->add_setting("deals_overlay_opacity", [
        "default" => 0.6,
        "sanitize_callback" => "aaapos_sanitize_float",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("deals_overlay_opacity", [
        "label" => __("Background Overlay Opacity", "macedon-ranges"),
        "description" => __(
            "Adjust the darkness of the overlay (0 = transparent, 1 = fully dark)",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "range",
        "input_attrs" => [
            "min" => 0,
            "max" => 1,
            "step" => 0.1,
        ],
        "priority" => 73,
    ]);

    // Deal Selection Method
    $wp_customize->add_setting("deal_selection_method", [
        "default" => "manual",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("deal_selection_method", [
        "label" => __("Deal Selection Method", "macedon-ranges"),
        "description" => __(
            "Choose how to select the product to display",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "select",
        "choices" => [
            "manual" => __("Manual - Select Product ID", "macedon-ranges"),
            "latest_sale" => __(
                "Automatic - Latest Sale Product",
                "macedon-ranges",
            ),
            "featured_sale" => __(
                "Automatic - Featured Sale Product",
                "macedon-ranges",
            ),
        ],
        "priority" => 75,
    ]);

    $wp_customize->add_setting("deal_product_id", [
        "default" => 0,
        "sanitize_callback" => "absint",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("deal_product_id", [
        "label" => __("Deal Product ID", "macedon-ranges"),
        "description" => __(
            "Enter the product ID to display (only used when Manual is selected)",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "number",
        "priority" => 80,
    ]);

    $wp_customize->add_setting("deal_end_date", [
        "default" => "",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("deal_end_date", [
        "label" => __("Deal End Date & Time", "macedon-ranges"),
        "description" => __(
            "Format: YYYY-MM-DD HH:MM:SS (e.g., 2026-12-31 23:59:59). Leave empty to hide countdown.",
            "macedon-ranges",
        ),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 85,
    ]);

    // ===================================
    // TESTIMONIALS
    // ===================================

    $wp_customize->add_setting("show_testimonials", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_testimonials", [
        "label" => __("Show Testimonials Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 100,
    ]);

    $wp_customize->add_setting("testimonials_title", [
        "default" => "What Our Customers Say",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("testimonials_title", [
        "label" => __("Testimonials Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 110,
    ]);

    $wp_customize->add_setting("testimonials_section_title", [
        "default" => "What Our Customers Say",
        "sanitize_callback" => "sanitize_text_field",
    ]);

    $wp_customize->add_control("testimonials_section_title", [
        "label" => __("Testimonials Section Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 111,
    ]);

    $wp_customize->add_setting("testimonials_section_subtitle", [
        "default" => "Don't just take our word for it",
        "sanitize_callback" => "sanitize_text_field",
    ]);

    $wp_customize->add_control("testimonials_section_subtitle", [
        "label" => __("Testimonials Section Subtitle", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 112,
    ]);

    // ===================================
    // BLOG PREVIEW
    // ===================================

    $wp_customize->add_setting("show_blog", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_blog", [
        "label" => __("Show Blog Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 120,
    ]);

    $wp_customize->add_setting("blog_title", [
        "default" => "Latest from Our Blog",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("blog_title", [
        "label" => __("Blog Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 130,
    ]);

    $wp_customize->add_setting("blog_posts_count", [
        "default" => 3,
        "sanitize_callback" => "absint",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("blog_posts_count", [
        "label" => __("Number of Blog Posts", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "number",
        "input_attrs" => [
            "min" => 1,
            "max" => 6,
            "step" => 1,
        ],
        "priority" => 140,
    ]);

    // ===================================
    // NEWSLETTER
    // ===================================

    $wp_customize->add_setting("show_newsletter", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("show_newsletter", [
        "label" => __("Show Newsletter Section", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 150,
    ]);

    $wp_customize->add_setting("newsletter_title", [
        "default" => "Stay Updated",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("newsletter_title", [
        "label" => __("Newsletter Title", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "text",
        "priority" => 160,
    ]);

    $wp_customize->add_setting("newsletter_description", [
        "default" =>
            "Get exclusive deals and product updates delivered to your inbox.",
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("newsletter_description", [
        "label" => __("Newsletter Description", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "textarea",
        "priority" => 170,
    ]);

    $wp_customize->add_setting("newsletter_gdpr", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("newsletter_gdpr", [
        "label" => __("Show GDPR Checkbox", "macedon-ranges"),
        "section" => "mr_homepage_sections",
        "type" => "checkbox",
        "priority" => 180,
    ]);
}
add_action("customize_register", "mr_homepage_sections_customizer");

/**
 * Sanitize Float values for customizer
 */
if (!function_exists("aaapos_sanitize_float")) {
    function aaapos_sanitize_float($input)
    {
        return floatval($input);
    }
}
