<?php
/**
 * Asset Management - Scripts & Styles Enqueue
 *
 * Handles theme asset loading with:
 * - Conditional loading
 * - Dependency management
 * - Performance optimization
 * - Resource hints
 *
 * @package AAAPOS
 * @since 1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Enqueue theme styles
 */
function mr_enqueue_styles()
{
    // CSS variables & base (highest priority)
    wp_enqueue_style(
        "mr-variables",
        MR_THEME_URI . "/assets/css/variables.css",
        [],
        MR_THEME_VERSION,
    );

    wp_enqueue_style(
        "mr-base",
        MR_THEME_URI . "/assets/css/base.css",
        ["mr-variables"],
        MR_THEME_VERSION,
    );

    // Layout system
    wp_enqueue_style(
        "mr-layout",
        MR_THEME_URI . "/assets/css/layout.css",
        ["mr-base"],
        MR_THEME_VERSION,
    );

    // Component styles
    wp_enqueue_style(
        "mr-components",
        MR_THEME_URI . "/assets/css/main.css",
        ["mr-layout"],
        MR_THEME_VERSION,
    );

    // Feature styles
    wp_enqueue_style(
        "mr-featured-products",
        MR_THEME_URI . "/assets/css/components/featured-products.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Animation styles
    wp_enqueue_style(
        "mr-animations",
        MR_THEME_URI . "/assets/css/animations.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Search results styles
    wp_enqueue_style(
        "mr-search-results",
        MR_THEME_URI . "/assets/css/components/search-results.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Search no results styles
    wp_enqueue_style(
        "mr-search-no-results",
        MR_THEME_URI . "/assets/css/components/search-no-results.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Blog
    wp_enqueue_style(
        "mr-blog",
        MR_THEME_URI . "/assets/css/components/blog.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Breadcrumb component styles
    wp_enqueue_style(
        "mr-breadcrumb-shop",
        MR_THEME_URI . "/assets/css/components/breadcrumb-shop.css",
        ["mr-components"],
        MR_THEME_VERSION,
    );

    // Responsive styles (load last)
    wp_enqueue_style(
        "mr-responsive",
        MR_THEME_URI . "/assets/css/responsive.css",
        ["mr-animations"],
        MR_THEME_VERSION,
    );

    // Category cards styles - NUCLEAR PRIORITY
    wp_enqueue_style(
        "aaapos-categories",
        get_template_directory_uri() .
            "/assets/css/components/categories-shop.css",
        ["aaapos-woocommerce-base"],
        AAAPOS_VERSION . "." . time() . rand(1, 9999),
        "all",
    );

    // JavaScript
    wp_enqueue_script(
        "aaapos-category-filter-drag",
        get_template_directory_uri() . "/assets/js/category-filter-drag.js",
        [],
        "1.0.0",
        true,
    );

    // WooCommerce specific styles
    if (class_exists("WooCommerce")) {
        // Quick View Modal styles
        wp_enqueue_style(
            "aaapos-quick-view",
            MR_THEME_URI . "/assets/css/quick-view.css",
            ["mr-components"],
            MR_THEME_VERSION,
        );

        // Cart Notifications styles
        wp_enqueue_style(
            "aaapos-cart-notifications",
            MR_THEME_URI . "/assets/css/cart-notifications.css",
            ["mr-components"],
            MR_THEME_VERSION,
        );
    }

    // Main theme stylesheet (for theme metadata)
    wp_enqueue_style("mr-style", get_stylesheet_uri(), [], MR_THEME_VERSION);

    // RTL support
    if (is_rtl()) {
        wp_enqueue_style(
            "mr-rtl",
            MR_THEME_URI . "/rtl.css",
            ["mr-style"],
            MR_THEME_VERSION,
        );
    }
}
add_action("wp_enqueue_scripts", "mr_enqueue_styles", 10);

/**
 * Enqueue theme scripts
 */
function mr_enqueue_scripts()
{
    // Core theme JavaScript (defer, footer)
    wp_enqueue_script(
        "mr-theme",
        MR_THEME_URI . "/assets/js/theme.js",
        [],
        MR_THEME_VERSION,
        true,
    );

    // Navigation
    if (has_nav_menu("primary") || has_nav_menu("mobile")) {
        wp_enqueue_script(
            "mr-navigation",
            MR_THEME_URI . "/assets/js/navigation.js",
            ["mr-theme"],
            MR_THEME_VERSION,
            true,
        );

        // Localize navigation script for AJAX
        wp_localize_script("mr-navigation", "mrNav", [
            "ajax_url" => admin_url("admin-ajax.php"),
            "nonce" => wp_create_nonce("mr_cart_nonce"),
        ]);
    }

    // Animations
    wp_enqueue_script(
        "mr-animations",
        MR_THEME_URI . "/assets/js/animations.js",
        ["mr-theme"],
        MR_THEME_VERSION,
        true,
    );

    // Slider (conditional - only on pages with sliders)
    if (is_front_page() || is_page_template("page-templates/homepage.php")) {
        wp_enqueue_script(
            "mr-slider",
            MR_THEME_URI . "/assets/js/slider.js",
            ["mr-theme"],
            MR_THEME_VERSION,
            true,
        );
    }

    // Modal (conditional)
    if (is_search() || is_singular()) {
        wp_enqueue_script(
            "mr-modal",
            MR_THEME_URI . "/assets/js/modal.js",
            ["mr-theme"],
            MR_THEME_VERSION,
            true,
        );
    }

    // Footer scripts (conditional)
    if (
        is_active_sidebar("footer-1") ||
        is_active_sidebar("footer-2") ||
        is_active_sidebar("footer-3")
    ) {
        wp_enqueue_script(
            "mr-footer",
            MR_THEME_URI . "/assets/js/footer.js",
            ["mr-theme"],
            MR_THEME_VERSION,
            true,
        );
    }

    // WooCommerce scripts (conditional)
    if (class_exists("WooCommerce")) {
        // Header scroll effects
        wp_enqueue_script(
            "mr-header-scroll",
            MR_THEME_URI . "/assets/js/header-scroll.js",
            ["mr-theme"],
            MR_THEME_VERSION,
            true,
        );

        // WooCommerce enhancements
        wp_enqueue_script(
            "mr-woocommerce",
            MR_THEME_URI . "/assets/js/woocommerce.js",
            ["mr-animations", "jquery"],
            MR_THEME_VERSION,
            true,
        );

        // Cart scripts
        wp_enqueue_script(
            "mr-cart",
            MR_THEME_URI . "/assets/js/cart.js",
            ["mr-woocommerce", "jquery"],
            MR_THEME_VERSION,
            true,
        );

        // ============================================
        // QUICK VIEW - Load on shop, archive, search pages
        // ============================================
        if (
            is_shop() ||
            is_product_category() ||
            is_product_tag() ||
            is_search() ||
            is_front_page()
        ) {
            wp_enqueue_script(
                "aaapos-quick-view",
                MR_THEME_URI . "/assets/js/quick-view.js",
                ["jquery"],
                MR_THEME_VERSION,
                true,
            );

            // Localize Quick View script
            wp_localize_script("aaapos-quick-view", "aaaposQuickView", [
                "ajax_url" => admin_url("admin-ajax.php"),
                "nonce" => wp_create_nonce("woocommerce-cart"),
            ]);
        }

        // ============================================
        // CART NOTIFICATIONS - Load on all pages with products
        // ============================================
        if (
            is_shop() ||
            is_product_category() ||
            is_product_tag() ||
            is_product() ||
            is_search() ||
            is_front_page()
        ) {
            wp_enqueue_script(
                "aaapos-cart-notifications",
                MR_THEME_URI . "/assets/js/cart-notifications.js",
                ["jquery"],
                MR_THEME_VERSION,
                true,
            );
        }

        // SINGLE PRODUCT PAGE - Modern variation swatches
        if (is_product()) {
            wp_enqueue_script(
                "mr-variation-swatches",
                MR_THEME_URI . "/assets/js/variation-swatches.js",
                ["jquery", "wc-add-to-cart-variation"],
                MR_THEME_VERSION,
                true,
            );

            wp_enqueue_script(
                "mr-quantity-selector",
                MR_THEME_URI . "/assets/js/quantity-selector.js",
                ["jquery"],
                MR_THEME_VERSION,
                true,
            );
        }

        // Shop column toggle
        if (is_shop() || is_product_category() || is_product_tag()) {
            wp_enqueue_script(
                "aaapos-shop-column-toggle",
                get_template_directory_uri() .
                    "/assets/js/shop-column-toggle.js",
                ["jquery"],
                "1.0.0",
                true,
            );
        }

        // Localize for cart.js and woocommerce.js
        wp_localize_script("mr-cart", "mr_ajax", [
            "url" => admin_url("admin-ajax.php"),
            "ajax_url" => admin_url("admin-ajax.php"),
            "wc_ajax_url" => WC_AJAX::get_endpoint("%%endpoint%%"),
            "nonce" => wp_create_nonce("mr_nonce"),
            "cart_nonce" => wp_create_nonce("mr_cart_nonce"),
        ]);
    }

    // Localize script for AJAX & global settings
    wp_localize_script("mr-theme", "mrTheme", [
        "ajaxUrl" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mr_nonce"),
        "homeUrl" => esc_url(home_url("/")),
        "themeUrl" => MR_THEME_URI,
        "isRTL" => is_rtl(),
        "isMobile" => wp_is_mobile(),
        "animationEnabled" => get_theme_mod("mr_enable_animations", true),
        "animationSpeed" => get_theme_mod("mr_animation_speed", "normal"),
        "i18n" => [
            "loading" => esc_html__("Loading...", "aaapos-prime"),
            "error" => esc_html__("An error occurred", "aaapos-prime"),
            "close" => esc_html__("Close", "aaapos-prime"),
            "search" => esc_html__("Search", "aaapos-prime"),
            "noResults" => esc_html__("No results found", "aaapos-prime"),
        ],
    ]);

    // Comment reply script (conditional)
    if (is_singular() && comments_open() && get_option("thread_comments")) {
        wp_enqueue_script("comment-reply");
    }
}
add_action("wp_enqueue_scripts", "mr_enqueue_scripts", 20);

/**
 * Enqueue customizer live preview script
 */
function mr_customizer_live_preview()
{
    wp_enqueue_script(
        "mr-customizer-preview",
        get_template_directory_uri() . "/assets/js/customizer-preview.js",
        ["customize-preview"],
        MR_THEME_VERSION,
        true,
    );
}
add_action("customize_preview_init", "mr_customizer_live_preview");

/**
 * Resource hints for performance
 */
function mr_resource_hints($urls, $relation_type)
{
    if ("preconnect" === $relation_type) {
        // Preconnect to Google Fonts
        $urls[] = [
            "href" => "https://fonts.googleapis.com",
            "crossorigin",
        ];
        $urls[] = [
            "href" => "https://fonts.gstatic.com",
            "crossorigin",
        ];
    }

    if ("dns-prefetch" === $relation_type) {
        // DNS prefetch for external resources
        $urls[] = "https://www.google-analytics.com";
        $urls[] = "https://www.googletagmanager.com";
    }

    return $urls;
}
add_filter("wp_resource_hints", "mr_resource_hints", 10, 2);

function aaapos_customizer_controls_assets()
{
    // Add custom CSS for the customizer panel if needed
    wp_add_inline_style(
        "customize-controls",
        '
        .customize-control-color .wp-color-result {
            border-radius: 4px;
        }
        
        #accordion-panel-aaapos_colors_panel .accordion-section {
            border-left: 3px solid #0ea5e9;
        }
        
        .customize-control-color .customize-control-title {
            font-weight: 600;
        }
    ',
    );
}
add_action(
    "customize_controls_enqueue_scripts",
    "aaapos_customizer_controls_assets",
);

/**
 * Enqueue Google Fonts with optimal loading
 */
function mr_enqueue_google_fonts()
{
    // Get font choices from customizer
    $body_font = get_theme_mod("mr_body_font", "Inter");
    $heading_font = get_theme_mod("mr_heading_font", "Montserrat");
    $accent_font = get_theme_mod("mr_accent_font", "Playfair Display");

    // Build font families string
    $font_families = [];

    if ("Inter" === $body_font) {
        $font_families[] = "Inter:wght@400;500;600";
    }

    if ("Montserrat" === $heading_font) {
        $font_families[] = "Montserrat:wght@600;700;800";
    }

    if ("Playfair Display" === $accent_font) {
        $font_families[] = "Playfair+Display:wght@700";
    }

    // Only load if we have fonts to load
    if (!empty($font_families)) {
        $fonts_url =
            "https://fonts.googleapis.com/css2?family=" .
            implode("&family=", $font_families) .
            "&display=swap";

        wp_enqueue_style("mr-google-fonts", $fonts_url, [], null);
    }
}
add_action("wp_enqueue_scripts", "mr_enqueue_google_fonts", 5);

/**
 * Preload critical assets
 */
function mr_preload_critical_assets()
{
    echo '<link rel="preload" href="' .
        esc_url(MR_THEME_URI . "/assets/css/variables.css") .
        '" as="style">' .
        "\n";
    echo '<link rel="preload" href="' .
        esc_url(MR_THEME_URI . "/assets/css/base.css") .
        '" as="style">' .
        "\n";
    echo '<link rel="preload" href="' .
        esc_url(MR_THEME_URI . "/assets/js/theme.js") .
        '" as="script">' .
        "\n";

    // Preload logo if set
    $custom_logo_id = get_theme_mod("custom_logo");
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, "full");
        if ($logo_url) {
            echo '<link rel="preload" href="' .
                esc_url($logo_url) .
                '" as="image">' .
                "\n";
        }
    }

    // Preload hero image on homepage
    if (is_front_page()) {
        $hero_image = get_theme_mod("mr_hero_image");
        if ($hero_image) {
            echo '<link rel="preload" href="' .
                esc_url($hero_image) .
                '" as="image">' .
                "\n";
        }
    }
}
add_action("wp_head", "mr_preload_critical_assets", 1);

/**
 * Add defer/async attributes to scripts
 */
function mr_script_loader_tag($tag, $handle, $src)
{
    // Scripts that should be async
    $async_scripts = [];

    // Scripts that should be deferred (most theme scripts)
    $defer_scripts = [
        "mr-theme",
        "mr-navigation",
        "mr-animations",
        "mr-slider",
        "mr-modal",
        "mr-footer",
        "mr-woocommerce",
        "mr-cart",
        "mr-header-scroll",
        "mr-variation-swatches",
        "mr-quantity-selector",
        "aaapos-quick-view",
        "aaapos-cart-notifications",
    ];

    if (in_array($handle, $async_scripts, true)) {
        return str_replace(" src", " async src", $tag);
    }

    if (in_array($handle, $defer_scripts, true)) {
        return str_replace(" src", " defer src", $tag);
    }

    return $tag;
}
add_filter("script_loader_tag", "mr_script_loader_tag", 10, 3);
