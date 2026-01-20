<?php
/**
 * Asset Management - Scripts & Styles Enqueue
 *
 * Handles theme asset loading with:
 * - Conditional loading
 * - Dependency management
 * - Performance optimization
 * - Resource hints
 * - Auto-detection of production/development mode
 *
 * @package AAAPOS
 * @since 1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Check if we're in production mode (minified files exist)
 * 
 * @return bool True if production files exist
 */
function mr_is_production_mode() {
    static $is_production = null;
    
    if ($is_production === null) {
        $is_production = file_exists(get_template_directory() . '/assets/css/dist/main.min.css') && 
                        file_exists(get_template_directory() . '/assets/js/dist/bundle.min.js');
    }
    
    return $is_production;
}

/**
 * Enqueue theme styles
 */
function mr_enqueue_styles()
{
    $is_production = mr_is_production_mode();

    if ($is_production) {
        // PRODUCTION MODE - Load single minified CSS file
        wp_enqueue_style(
            "mr-main",
            MR_THEME_URI . "/assets/css/dist/main.min.css",
            [],
            MR_THEME_VERSION,
        );
    } else {
        // DEVELOPMENT MODE - Load individual CSS files
        
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

        // Animation styles
        wp_enqueue_style(
            "mr-animations",
            MR_THEME_URI . "/assets/css/animations.css",
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
    }

    // ALWAYS LOAD THESE (Both modes) - Not in main.css
    
    // Featured products (standalone)
    wp_enqueue_style(
        "mr-featured-products",
        MR_THEME_URI . "/assets/css/components/featured-products.css",
        $is_production ? ["mr-main"] : ["mr-components"],
        MR_THEME_VERSION,
    );

    // Search results styles
    wp_enqueue_style(
        "mr-search-results",
        MR_THEME_URI . "/assets/css/components/search-results.css",
        $is_production ? ["mr-main"] : ["mr-components"],
        MR_THEME_VERSION,
    );

    // Search no results styles
    wp_enqueue_style(
        "mr-search-no-results",
        MR_THEME_URI . "/assets/css/components/search-no-results.css",
        $is_production ? ["mr-main"] : ["mr-components"],
        MR_THEME_VERSION,
    );

    // Cart Confirmation Modal CSS
    wp_enqueue_style(
        "cart-confirm-modal",
        get_template_directory_uri() . "/assets/css/cart-confirm-modal.css",
        [],
        MR_THEME_VERSION,
    );

    // Breadcrumb component styles
    wp_enqueue_style(
        "mr-breadcrumb-shop",
        MR_THEME_URI . "/assets/css/components/breadcrumb-shop.css",
        $is_production ? ["mr-main"] : ["mr-components"],
        MR_THEME_VERSION,
    );

    // Auth Modal CSS
    wp_enqueue_style(
        "mr-auth-modal",
        MR_THEME_URI . "/assets/css/auth-modal.css",
        [],
        MR_THEME_VERSION,
    );

    // Cart Notifications CSS
    wp_enqueue_style(
        "aaapos-cart-notifications",
        MR_THEME_URI . "/assets/css/cart-notifications.css",
        [],
        MR_THEME_VERSION,
    );

    // Checkout styles - ONLY on checkout page
    if (is_checkout()) {
        wp_enqueue_style(
            "checkout-main",
            get_template_directory_uri() .
                "/assets/css/components/cart/checkout-main.css",
            [],
            MR_THEME_VERSION,
        );

        wp_enqueue_style(
            "checkout-form",
            get_template_directory_uri() .
                "/assets/css/components/cart/checkout-form.css",
            ["checkout-main"],
            MR_THEME_VERSION,
        );
        
        wp_enqueue_style(
            "checkout-review",
            get_template_directory_uri() .
                "/assets/css/components/cart/checkout-review.css",
            ["checkout-form"],
            MR_THEME_VERSION,
        );

        wp_enqueue_style(
            "aaapos-checkout-progress",
            get_template_directory_uri() .
                "/assets/css/components/cart/checkout-progress.css",
            [],
            MR_THEME_VERSION,
        );
    }

    // Order Received Page
    if (is_order_received_page()) {
        wp_enqueue_style(
            "aaapos-order-received",
            get_template_directory_uri() .
                "/assets/css/components/cart/order-received.css",
            [],
            MR_THEME_VERSION,
        );
    }

    // Category cards styles
    wp_enqueue_style(
        "aaapos-categories",
        get_template_directory_uri() .
            "/assets/css/components/categories-shop.css",
        $is_production ? ["mr-main"] : ["mr-components"],
        MR_THEME_VERSION,
    );

    // 404 Page styles
    if (is_404()) {
        wp_enqueue_style(
            "macedon-ranges-404",
            get_template_directory_uri() . "/assets/css/404.css",
            [],
            MR_THEME_VERSION,
        );
    }

    // WooCommerce specific styles
    if (class_exists("WooCommerce")) {
        // Quick View Modal styles
        wp_enqueue_style(
            "aaapos-quick-view",
            MR_THEME_URI . "/assets/css/quick-view.css",
            [],
            MR_THEME_VERSION,
        );
    }
    // Variation Alert styles
    wp_enqueue_style(
    'aaapos-variation-alert',
    get_template_directory_uri() . '/assets/css/variation-alert.css',
    array(),
    '1.0.0'
);


wp_enqueue_script(
    'aaapos-variation-alert',
    get_template_directory_uri() . '/assets/js/variation-alert.js',
    array('jquery'),
    '1.0.0',
    true
);

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
 * Enqueue hero video script for single WebM video
 */
function mr_enqueue_hero_video_script()
{
    // Check if hero is shown and in video mode
    if (
        get_theme_mod("show_hero", true) &&
        get_theme_mod("hero_media_type", "image") === "video"
    ) {
        $video_id = get_theme_mod("hero_video_webm", "");

        if ($video_id && !mr_is_production_mode()) {
            wp_enqueue_script(
                "mr-hero-video",
                get_template_directory_uri() . "/assets/js/hero-video.js",
                ["jquery"],
                MR_THEME_VERSION,
                true,
            );
        }
    }
}
add_action("wp_enqueue_scripts", "mr_enqueue_hero_video_script", 20);

/**
 * Enqueue theme scripts
 */
function mr_enqueue_scripts()
{
    $is_production = mr_is_production_mode();

    if ($is_production) {
        // PRODUCTION MODE - Load single minified JS bundle
        wp_enqueue_script(
            "mr-bundle",
            MR_THEME_URI . "/assets/js/dist/bundle.min.js",
            ["jquery"],
            MR_THEME_VERSION,
            true,
        );

        // Localize the bundle with all necessary data
        wp_localize_script("mr-bundle", "mrTheme", [
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

        // Navigation localization
        if (has_nav_menu("primary") || has_nav_menu("mobile")) {
            wp_localize_script("mr-bundle", "mrNav", [
                "ajax_url" => admin_url("admin-ajax.php"),
                "nonce" => wp_create_nonce("mr_cart_nonce"),
            ]);
        }

        // WooCommerce localizations
        if (class_exists("WooCommerce")) {
            wp_localize_script("mr-bundle", "mr_ajax", [
                "url" => admin_url("admin-ajax.php"),
                "ajax_url" => admin_url("admin-ajax.php"),
                "wc_ajax_url" => WC_AJAX::get_endpoint("%%endpoint%%"),
                "nonce" => wp_create_nonce("mr_nonce"),
                "cart_nonce" => wp_create_nonce("mr_cart_nonce"),
            ]);

            // Quick View localization
            if (
                is_shop() ||
                is_product_category() ||
                is_product_tag() ||
                is_search() ||
                is_front_page()
            ) {
                wp_localize_script("mr-bundle", "aaaposQuickView", [
                    "ajax_url" => admin_url("admin-ajax.php"),
                    "nonce" => wp_create_nonce("aaapos_quick_view_nonce"),
                ]);
            }
        }

    } else {
        // DEVELOPMENT MODE - Load individual JS files
        
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

            // Quick View
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
                    "nonce" => wp_create_nonce("aaapos_quick_view_nonce"),
                ]);
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
                    MR_THEME_VERSION,
                    true,
                );
            }

            // Category filter drag
            wp_enqueue_script(
                "aaapos-category-filter-drag",
                get_template_directory_uri() . "/assets/js/category-filter-drag.js",
                [],
                MR_THEME_VERSION,
                true,
            );

            // Product Share - ONLY on single product pages
if (is_product()) {
    wp_enqueue_script(
        'aaapos-product-share',
        get_template_directory_uri() . '/assets/js/product-share.js',
        array('jquery'),
        MR_THEME_VERSION,
        true
    );
}

/**
 * Enqueue Checkout Shipping Visibility Handler
 */
function aaapos_enqueue_checkout_shipping_handler() {
    // Only on checkout page
    if (is_checkout() && !is_order_received_page()) {
        wp_enqueue_script(
            'aaapos-checkout-shipping-visibility',
            get_template_directory_uri() . '/assets/js/checkout-shipping.js',
            array('jquery', 'wc-checkout'),
            AAAPOS_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'aaapos_enqueue_checkout_shipping_handler', 1001);
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
    }

    // ALWAYS LOAD THESE (Not in bundle) - Both dev and production
    
    // Cart Confirmation Modal - Standalone
    wp_enqueue_script(
        "cart-confirm-modal",
        get_template_directory_uri() . "/assets/js/cart-confirm-modal.js",
        [],
        MR_THEME_VERSION,
        true,
    );

    // Auth Modal - Standalone (needs localization)
    wp_enqueue_script(
        "mr-auth-modal",
        get_template_directory_uri() . "/assets/js/auth-modal.js",
        ["jquery"],
        MR_THEME_VERSION,
        true,
    );

    // Deals Rotation Script - ALWAYS LOAD (Both production and development)
if (is_front_page()) {
    wp_enqueue_script(
        'aaapos-deals-rotation',
        get_template_directory_uri() . '/assets/js/deals-rotation.js',
        array(), // REMOVE jQuery dependency - script is vanilla JS
        MR_THEME_VERSION,
        true // Load in footer
    );
}

    // Get custom image directly from customizer
    $login_image_id = get_theme_mod('auth_modal_login_image', '');
    $login_image_url = '';
    $has_custom_image = false;
    
    if (!empty($login_image_id)) {
        $image_data = wp_get_attachment_image_src($login_image_id, 'full');
        if ($image_data && isset($image_data[0])) {
            $login_image_url = $image_data[0];
            $has_custom_image = true;
        }
    }

    // Localize auth modal (ALWAYS - both modes)
    wp_localize_script("mr-auth-modal", "mr_auth", [
        "ajax_url" => admin_url("admin-ajax.php"),
        "nonce" => wp_create_nonce("mr_auth_nonce"),
        "login_image" => $login_image_url,
        "has_custom_image" => $has_custom_image,
        "login_subtitle" => get_theme_mod('auth_modal_login_subtitle', __('Welcome back! Please enter your details', 'aaapos')),
        "register_subtitle" => get_theme_mod('auth_modal_register_subtitle', __('Create your account to get started', 'aaapos')),
        "lost_password_url" => wp_lostpassword_url(),
    ]);

    // Cart Notifications - Standalone
    if (
        is_shop() ||
        is_product_category() ||
        is_product_tag() ||
        is_product() ||
        is_search() ||
        is_front_page() ||
        is_page() ||
        is_singular()
    ) {
        wp_enqueue_script(
            "aaapos-cart-notifications",
            MR_THEME_URI . "/assets/js/cart-notifications.js",
            ["jquery"],
            MR_THEME_VERSION,
            true,
        );
    }

    // Checkout scripts - ONLY on checkout page (Not in bundle)
    if (is_checkout() && !is_order_received_page()) {
        wp_enqueue_script(
            "aaapos-checkout",
            get_template_directory_uri() . "/assets/js/checkout.js",
            ["jquery", "wc-checkout"],
            MR_THEME_VERSION,
            true,
        );
    }

    // Comment reply script (conditional)
    if (is_singular() && comments_open() && get_option("thread_comments")) {
        wp_enqueue_script("comment-reply");
    }
}
add_action("wp_enqueue_scripts", "mr_enqueue_scripts", 20);

/**
 * Enqueue customizer live preview script
 * NOTE: This should NEVER be in the bundle
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
    $is_production = mr_is_production_mode();

    if ($is_production) {
        // Production - preload minified bundle
        echo '<link rel="preload" href="' .
            esc_url(MR_THEME_URI . "/assets/css/dist/main.min.css") .
            '" as="style">' .
            "\n";
        echo '<link rel="preload" href="' .
            esc_url(MR_THEME_URI . "/assets/js/dist/bundle.min.js") .
            '" as="script">' .
            "\n";
    } else {
        // Development - preload individual files
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
    }

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

    // Scripts that should be deferred
    $defer_scripts = [
        "mr-bundle",
        "mr-theme",
        "mr-navigation",
        "mr-animations",
        "mr-slider",
        "mr-modal",
        "mr-footer",
        "mr-woocommerce",
        "mr-cart",
        "cart-confirm-modal",
        "mr-auth-modal",
        "mr-header-scroll",
        "mr-variation-swatches",
        "mr-quantity-selector",
        "aaapos-quick-view",
        "aaapos-cart-notifications",
        "aaapos-shop-column-toggle",
        "aaapos-category-filter-drag",
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

/**
 * Enqueue WooCommerce Product Collection Block Styles
 */
function aaapos_enqueue_woocommerce_blocks_styles()
{
    // Only load on pages that have WooCommerce blocks
    if (is_singular() || is_front_page() || is_page()) {
        wp_enqueue_style(
            "aaapos-woocommerce-blocks",
            get_template_directory_uri() .
                "/assets/css/woocommerce/woocommerce-blocks.css",
            [],
            MR_THEME_VERSION,
            "all",
        );
    }
}
add_action(
    "wp_enqueue_scripts",
    "aaapos_enqueue_woocommerce_blocks_styles",
    999,
);