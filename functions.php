<?php
/**
 * AAAPOS Theme Functions
 */

if (!defined("ABSPATH")) {
    exit();
}

// Theme Constants
define('THEME_VERSION', '1.0.0');
define("AAAPOS_VERSION", wp_get_theme()->get("Version"));
define("AAAPOS_THEME_DIR", get_template_directory());
define("AAAPOS_THEME_URI", get_template_directory_uri());
define("AAAPOS_ASSETS_URI", AAAPOS_THEME_URI . "/assets");
define("AAAPOS_INC_DIR", AAAPOS_THEME_DIR . "/inc");

// MR Constants (for compatibility with included files)
define("MR_THEME_VERSION", AAAPOS_VERSION);
define("MR_THEME_DIR", AAAPOS_THEME_DIR);
define("MR_THEME_URI", AAAPOS_THEME_URI);

// Content Width
if (!isset($content_width)) {
    $content_width = 1200;
}

// Load theme files
function aaapos_load_theme_files()
{
    $includes = [
        "inc/setup.php",
        "inc/auth-ajax.php",
        "inc/testimonials.php",
        "inc/enqueue.php",
        "inc/security.php",
        "inc/template-tags.php",
        "inc/widgets.php",
        "inc/woocommerce.php",
        "inc/ajax.php",
        "inc/customizer/customizer.php",
        "inc/customizer/auth-customizer.php",
        "inc/customizer/about-customizer.php",
        "inc/customizer/contact-customizer.php",
        "inc/customizer/colors.php",
        "inc/customizer/typography.php",
        "inc/customizer/header.php",
        "inc/customizer/hero.php",
        "inc/customizer/footer.php",
        "inc/customizer/homepage-sections.php",
        "inc/customizer/woocommerce.php",
        "inc/customizer/header-dropdown-customizer.php",
        "inc/customizer/trust-badges-customizer.php",
        "inc/customizer/multiple-control.php",
        "inc/customizer/category-order-control.php",
    ];

    foreach ($includes as $file) {
        $filepath = AAAPOS_THEME_DIR . "/" . $file;

        if (file_exists($filepath)) {
            require_once $filepath;
        } else {
            if (defined("WP_DEBUG") && WP_DEBUG) {
                error_log("AAAPOS: Missing file " . $file);
            }
        }
    }
}
add_action("after_setup_theme", "aaapos_load_theme_files", 1);

// code for off the site display while maintenance
// add_action('template_redirect', function () {
//     if (
//         ! is_user_logged_in()
//         && ! is_admin()
//         && ! wp_doing_ajax()
//     ) {
//         wp_die(
//             '<h1>Site Under Maintenance</h1><p>We are preparing the website.</p>',
//             'Maintenance',
//             array('response' => 503)
//         );
//     }
// });

// Remove "Clear" button from single product variations
add_filter('woocommerce_reset_variations_link', '__return_empty_string');

// Set to false for development, true for production
define('MR_PRODUCTION_MODE', false);

/**
 * Theme Setup
 *
 * Configure theme features, image sizes, and WordPress support.
 * This runs early in the WordPress initialization process.
 */
function aaapos_theme_setup()
{
    // Internationalization
    load_theme_textdomain("AAAPOS", AAAPOS_THEME_DIR . "/languages");

    // Essential WordPress features
    add_theme_support("automatic-feed-links");
    add_theme_support("title-tag");
    add_theme_support("post-thumbnails");

    // HTML5 markup support
    add_theme_support("html5", [
        "search-form",
        "comment-form",
        "comment-list",
        "gallery",
        "caption",
        "style",
        "script",
        "navigation-widgets",
    ]);

    // Responsive embedded content
    add_theme_support("responsive-embeds");

    // Selective refresh for widgets
    add_theme_support("customize-selective-refresh-widgets");

    // Block editor features
    add_theme_support("wp-block-styles");
    add_theme_support("align-wide");

    // Custom logo with flexible dimensions
    add_theme_support("custom-logo", [
        "height" => 120,
        "width" => 400,
        "flex-width" => true,
        "flex-height" => true,
        "unlink-homepage-logo" => true,
    ]);

    // Custom background
    add_theme_support("custom-background", [
        "default-color" => "ffffff",
    ]);

    // Navigation menus
    register_nav_menus([
        "primary" => esc_html__("Primary Navigation", "AAAPOS"),
        "mobile" => esc_html__("Mobile Navigation", "AAAPOS"),
        "footer" => esc_html__("Footer Navigation", "AAAPOS"),
        "utility" => esc_html__("Utility Navigation", "AAAPOS"),
    ]);

    // WooCommerce support
    if (class_exists("WooCommerce")) {
        add_theme_support("woocommerce");
        add_theme_support("wc-product-gallery-zoom");
        add_theme_support("wc-product-gallery-lightbox");
        add_theme_support("wc-product-gallery-slider");
    }

    // Custom image sizes
    aaapos_register_image_sizes();
}
add_action("after_setup_theme", "aaapos_theme_setup");

/**
 * Replace Cart & Checkout Blocks with Classic Templates on Theme Activation
 */
function aaapos_setup_classic_woocommerce_pages() {
    // Only run once
    if (get_option('aaapos_wc_blocks_replaced')) {
        return;
    }
    
    // Setup Cart Page
    $cart_page = get_page_by_path('cart');
    if ($cart_page && has_blocks($cart_page->post_content)) {
        wp_update_post([
            'ID' => $cart_page->ID,
            'post_content' => '[woocommerce_cart]'
        ]);
    }
    
    // Setup Checkout Page
    $checkout_page = get_page_by_path('checkout');
    if ($checkout_page && has_blocks($checkout_page->post_content)) {
        wp_update_post([
            'ID' => $checkout_page->ID,
            'post_content' => '[woocommerce_checkout]'
        ]);
    }
    
    // Mark as complete
    update_option('aaapos_wc_blocks_replaced', true);
}
add_action('after_switch_theme', 'aaapos_setup_classic_woocommerce_pages');

/**
 * Register Custom Image Sizes
 *
 * Define optimized image sizes for various components.
 * Organized by use case for easy maintenance.
 */
function aaapos_register_image_sizes()
{
    // Product images
    add_image_size("aaapos-product-thumbnail", 400, 400, true);
    add_image_size("aaapos-product-card", 600, 600, true);
    add_image_size("aaapos-product-featured", 800, 800, true);

    // Blog images
    add_image_size("aaapos-blog-card", 600, 400, true);
    add_image_size("aaapos-blog-featured", 1200, 600, true);

    // Category images
    add_image_size("aaapos-category-card", 600, 400, true);
    add_image_size("aaapos-category-banner", 1400, 400, true);

    // Hero images
    add_image_size("aaapos-hero-small", 1200, 600, true);
    add_image_size("aaapos-hero-large", 1920, 800, true);
    add_image_size("aaapos-hero-xlarge", 2560, 1080, true);

    // Misc
    add_image_size("aaapos-testimonial", 200, 200, true);
    add_image_size("aaapos-team-member", 400, 500, true);
}

/**
 * Add Human-Readable Image Size Names
 *
 * Makes custom image sizes selectable in the media library.
 */
function aaapos_custom_image_sizes($sizes)
{
    return array_merge($sizes, [
        "aaapos-product-card" => esc_html__("Product Card", "AAAPOS"),
        "aaapos-blog-card" => esc_html__("Blog Card", "AAAPOS"),
        "aaapos-category-card" => esc_html__("Category Card", "AAAPOS"),
        "aaapos-hero-large" => esc_html__("Hero Large", "AAAPOS"),
    ]);
}
add_filter("image_size_names_choose", "aaapos_custom_image_sizes");

/**
 * Body Classes
 *
 * Add contextual classes to body element for styling hooks.
 */
function aaapos_body_classes($classes)
{
    // WooCommerce active
    if (class_exists("WooCommerce")) {
        $classes[] = "woocommerce-active";

        // Specific WooCommerce pages
        if (is_shop() || is_product_category() || is_product_tag()) {
            $classes[] = "woocommerce-shop-page";
        }

        if (is_product()) {
            $classes[] = "woocommerce-single-product";
        }
    }

    // Sticky header
    if (get_theme_mod("header_sticky", true)) {
        $classes[] = "has-sticky-header";
    }

    // Sidebar layouts
    if (
        is_active_sidebar("shop-sidebar") &&
        (is_shop() || is_product_category())
    ) {
        $classes[] = "has-sidebar";
    } else {
        $classes[] = "no-sidebar";
    }

    // Page template classes
    if (is_page_template()) {
        $template = get_page_template_slug();
        $classes[] =
            "page-template-" .
            sanitize_html_class(str_replace(".php", "", basename($template)));
    }

    // Animation enabled
    if (get_theme_mod("enable_animations", true)) {
        $classes[] = "animations-enabled";
    }

    // Mobile detection (for specific mobile-only features)
    if (wp_is_mobile()) {
        $classes[] = "is-mobile";
    }

    return $classes;
}
add_filter("body_class", "aaapos_body_classes");

/**
 * Post Classes
 *
 * Add custom classes to post elements.
 */
function aaapos_post_classes($classes, $class, $post_id)
{
    // Add animation trigger class
    if (get_theme_mod("enable_animations", true)) {
        $classes[] = "animate-on-scroll";
    }

    return $classes;
}
add_filter("post_class", "aaapos_post_classes", 10, 3);

/**
 * Excerpt Length
 *
 * Customize excerpt length by word count.
 */
function aaapos_excerpt_length($length)
{
    if (is_admin()) {
        return $length;
    }

    // Customizer control
    $custom_length = get_theme_mod("excerpt_length", 25);

    return absint($custom_length);
}
add_filter("excerpt_length", "aaapos_excerpt_length");

/**
 * Excerpt More String
 *
 * Customize the "read more" text.
 */
function aaapos_excerpt_more($more)
{
    if (is_admin()) {
        return $more;
    }

    return "&hellip;";
}
add_filter("excerpt_more", "aaapos_excerpt_more");

/**
 * Custom Logo Helper
 *
 * Returns custom logo or site title fallback with proper markup.
 */
function aaapos_get_logo($echo = true)
{
    $logo_html = "";

    if (has_custom_logo()) {
        $custom_logo_id = get_theme_mod("custom_logo");
        $logo_attr = [
            "class" => "site-logo__image",
            "loading" => "eager",
            "itemprop" => "logo",
        ];

        $logo_html = sprintf(
            '<a href="%1$s" class="site-logo" rel="home" aria-label="%2$s">%3$s</a>',
            esc_url(home_url("/")),
            esc_attr(get_bloginfo("name")),
            wp_get_attachment_image($custom_logo_id, "full", false, $logo_attr),
        );
    } else {
        // Fallback to site title
        $logo_html = sprintf(
            '<a href="%1$s" class="site-logo site-logo--text" rel="home">
                <span class="site-logo__text">%2$s</span>
            </a>',
            esc_url(home_url("/")),
            esc_html(get_bloginfo("name")),
        );
    }

    if ($echo) {
        echo $logo_html;
    } else {
        return $logo_html;
    }
}

/**
 * Navigation Menu Fallback
 *
 * Display helpful message when no menu is assigned.
 */
function aaapos_menu_fallback($args)
{
    if (!current_user_can("edit_theme_options")) {
        return;
    }

    echo '<ul class="' . esc_attr($args["menu_class"]) . '">';
    echo '<li><a href="' . esc_url(admin_url("nav-menus.php")) . '">';
    esc_html_e("Add a Menu", "AAAPOS");
    echo "</a></li>";
    echo "</ul>";
}

/**
 * Preload Critical Assets
 *
 * Preload fonts, styles, and scripts for performance.
 */
function aaapos_preload_critical_assets()
{
    // Preconnect to external domains
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' .
        "\n";

    // Preload critical fonts (if self-hosted)
    $font_dir = AAAPOS_ASSETS_URI . "/fonts/";

    // Only preload if fonts exist
    if (file_exists(AAAPOS_THEME_DIR . "/assets/fonts/")) {
        // Example: Preload primary font
        // Uncomment and adjust when using self-hosted fonts
        /*
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>%s',
            esc_url($font_dir . 'inter-var.woff2'),
            "\n"
        );
        */
    }
}
add_action("wp_head", "aaapos_preload_critical_assets", 1);

/**
 * Resource Hints
 *
 * Add DNS prefetch and preconnect for performance.
 */
function aaapos_resource_hints($urls, $relation_type)
{
    if ("dns-prefetch" === $relation_type) {
        $urls[] = "https://fonts.googleapis.com";
        $urls[] = "https://fonts.gstatic.com";
    }

    return $urls;
}
add_filter("wp_resource_hints", "aaapos_resource_hints", 10, 2);

/**
 * Theme Activation
 *
 * Set default theme options on activation.
 * NOTE: Page creation is handled by setup.php to avoid duplicates
 */
function aaapos_theme_activation()
{
    // Only run once
    if (get_option('aaapos_theme_activated')) {
        return;
    }
    
    // Set default Customizer values if not already set
    $defaults = [
        "primary_color" => "#0ea5e9",
        "secondary_color" => "#f59e0b",
        "accent_color" => "#10b981",
        "header_sticky" => true,
        "enable_animations" => true,
        "show_hero" => true,
        "show_featured_products" => true,
        "show_categories" => true,
        "show_deals" => true,
        "show_testimonials" => true,
        "show_newsletter" => true,
        "excerpt_length" => 25,
    ];

    foreach ($defaults as $setting => $value) {
        if (false === get_theme_mod($setting)) {
            set_theme_mod($setting, $value);
        }
    }

    // Flush rewrite rules for custom post types/taxonomies
    flush_rewrite_rules();
    
    // Mark as activated
    update_option('aaapos_theme_activated', true);
}
add_action("after_switch_theme", "aaapos_theme_activation");

/**
 * Theme Deactivation
 *
 * Cleanup on theme switch.
 */
function aaapos_theme_deactivation()
{
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Remove activation flags so theme can re-run setup if reactivated
    delete_option('aaapos_theme_activated');
    delete_option('aaapos_wc_blocks_replaced');
}
add_action("switch_theme", "aaapos_theme_deactivation");

/**
 * Admin Notice for Missing Dependencies
 *
 * Alert admin if WooCommerce is not installed/activated.
 */
function aaapos_woocommerce_notice()
{
    if (!class_exists("WooCommerce")) { ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php printf(
                    /* translators: %s: WooCommerce plugin link */
                    esc_html__(
                        "The AAAPOS theme recommends installing %s for full functionality.",
                        "AAAPOS",
                    ),
                    '<a href="' .
                        esc_url(
                            admin_url(
                                "plugin-install.php?s=woocommerce&tab=search&type=term",
                            ),
                        ) .
                        '">WooCommerce</a>',
                ); ?>
            </p>
        </div>
        <?php }
}
add_action("admin_notices", "aaapos_woocommerce_notice");

/**
 * Remove Unnecessary WordPress Features
 *
 * Clean up and optimize WordPress output.
 */
function aaapos_cleanup_head()
{
    // Remove unnecessary WordPress meta tags
    remove_action("wp_head", "rsd_link");
    remove_action("wp_head", "wlwmanifest_link");
    remove_action("wp_head", "wp_generator");
    remove_action("wp_head", "wp_shortlink_wp_head");

    // Remove emoji scripts (use SVG or CSS instead)
    remove_action("wp_head", "print_emoji_detection_script", 7);
    remove_action("wp_print_styles", "print_emoji_styles");
    remove_action("admin_print_scripts", "print_emoji_detection_script");
    remove_action("admin_print_styles", "print_emoji_styles");
}
add_action("init", "aaapos_cleanup_head");

/**
 * Remove jQuery Migrate
 */
function aaapos_remove_jquery_migrate($scripts)
{
    if (!is_admin() && isset($scripts->registered["jquery"])) {
        $script = $scripts->registered["jquery"];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, ["jquery-migrate"]);
        }
    }
}
add_action("wp_default_scripts", "aaapos_remove_jquery_migrate");

/**
 * Defer Non-Critical JavaScript
 *
 * Add defer attribute to non-critical scripts for performance.
 */
function aaapos_defer_scripts($tag, $handle, $src)
{
    // List of scripts to defer
    $defer_scripts = ["aaapos-animations", "aaapos-slider", "aaapos-modal"];

    if (in_array($handle, $defer_scripts, true)) {
        return str_replace(" src", " defer src", $tag);
    }

    return $tag;
}
add_filter("script_loader_tag", "aaapos_defer_scripts", 10, 3);

/**
 * Allow SVG Upload in Media Library
 *
 * Enable SVG file upload for logos and icons.
 */
function aaapos_mime_types($mimes)
{
    $mimes["svg"] = "image/svg+xml";
    $mimes["svgz"] = "image/svg+xml";
    return $mimes;
}
add_filter("upload_mimes", "aaapos_mime_types");

/**
 * Fix SVG Display in Media Library
 */
function aaapos_fix_svg_display($response, $attachment, $meta)
{
    if ($response["type"] === "image" && $response["subtype"] === "svg+xml") {
        $response["image"] = [
            "src" => $response["url"],
        ];
    }
    return $response;
}
add_filter("wp_prepare_attachment_for_js", "aaapos_fix_svg_display", 10, 3);

/**
 * Debug Helper Function
 *
 * Pretty print debug information (only in WP_DEBUG mode).
 */
if (!function_exists("aaapos_debug")) {
    function aaapos_debug($data, $label = "")
    {
        if (defined("WP_DEBUG") && WP_DEBUG) {
            echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0;">';
            if ($label) {
                echo "<strong>" . esc_html($label) . ":</strong><br>";
            }
            print_r($data);
            echo "</pre>";
        }
    }
}

/**
 * Remove WordPress default Colors section
 */
function aaapos_remove_default_colors_section($wp_customize)
{
    $wp_customize->remove_section("colors");
}
add_action("customize_register", "aaapos_remove_default_colors_section", 99);

/* ==========================================================================
   WOOCOMMERCE CATEGORY CUSTOMIZATION - FIXED
   ========================================================================== */

/**
 * Remove default category count from title
 * This prevents the (3) from appearing in the category name
 */
add_filter("woocommerce_subcategory_count_html", "__return_empty_string");

/**
 * Customize WooCommerce Category Display
 * Adds description and product count BELOW the title
 */
function aaapos_custom_category_display()
{
    // Add custom content after the title
    add_action(
        "woocommerce_after_subcategory_title",
        "aaapos_category_custom_content",
        10,
    );
}
add_action("init", "aaapos_custom_category_display");

/**
 * Add custom content to category cards
 * Shows: Description (if exists) + Product count
 */
function aaapos_category_custom_content($category)
{
    $count = $category->count;
    $description = $category->description;

    // Description first (if exists)
    if ($description) {
        echo '<p class="category-description">' .
            wp_kses_post(wp_trim_words($description, 20)) .
            "</p>";
    }

    // Product count - Format: "1 Product" or "12 Products" (NO BRACKETS)
    if ($count === 1) {
        echo '<span class="count">1 Product</span>';
    } else {
        echo '<span class="count">' . esc_html($count) . " Products</span>";
    }
}

/* ==========================================================================
   CONTACT FORM HANDLER
   ========================================================================== */

/**
 * Handle Contact Form Submissions
 *
 * Processes form data, validates, sends email, and redirects with status message.
 */
function aaapos_handle_contact_form_submission()
{
    // Verify nonce for security
    if (
        !isset($_POST["contact_nonce"]) ||
        !wp_verify_nonce($_POST["contact_nonce"], "contact_form_submit")
    ) {
        wp_die(
            esc_html__(
                "Security check failed. Please go back and try again.",
                "AAAPOS",
            ),
            esc_html__("Security Error", "AAAPOS"),
            ["response" => 403, "back_link" => true],
        );
    }

    // Honeypot spam check (optional - add hidden field to form if using)
    if (isset($_POST["website"]) && !empty($_POST["website"])) {
        wp_safe_redirect(add_query_arg("form_error", "spam", wp_get_referer()));
        exit();
    }

    // Sanitize and validate input fields
    $name = isset($_POST["contact_name"])
        ? sanitize_text_field($_POST["contact_name"])
        : "";
    $email = isset($_POST["contact_email"])
        ? sanitize_email($_POST["contact_email"])
        : "";
    $phone = isset($_POST["contact_phone"])
        ? sanitize_text_field($_POST["contact_phone"])
        : "";
    $subject = isset($_POST["contact_subject"])
        ? sanitize_text_field($_POST["contact_subject"])
        : "";
    $message = isset($_POST["contact_message"])
        ? sanitize_textarea_field($_POST["contact_message"])
        : "";

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = esc_html__("Name is required", "AAAPOS");
    }

    if (empty($email)) {
        $errors[] = esc_html__("Email is required", "AAAPOS");
    } elseif (!is_email($email)) {
        $errors[] = esc_html__(
            "Please enter a valid email address",
            "AAAPOS",
        );
    }

    if (empty($subject)) {
        $errors[] = esc_html__("Subject is required", "AAAPOS");
    }

    if (empty($message)) {
        $errors[] = esc_html__("Message is required", "AAAPOS");
    }

    // If there are validation errors, redirect back with error
    if (!empty($errors)) {
        $error_message = implode(", ", $errors);
        wp_safe_redirect(
            add_query_arg(
                "form_error",
                urlencode($error_message),
                wp_get_referer(),
            ),
        );
        exit();
    }

    // Prepare email content
    $to = get_theme_mod("contact_form_email", get_option("admin_email"));
    $email_subject = sprintf("[%s] %s", get_bloginfo("name"), $subject);

    // Build email message
    $email_message = sprintf(
        "New contact form submission from %s\n\n" .
            "Name: %s\n" .
            "Email: %s\n" .
            "Phone: %s\n" .
            "Subject: %s\n\n" .
            "Message:\n%s\n\n" .
            "---\n" .
            "This email was sent from the contact form at %s",
        get_bloginfo("name"),
        $name,
        $email,
        !empty($phone) ? $phone : esc_html__("Not provided", "AAAPOS"),
        $subject,
        $message,
        home_url(),
    );

    // Email headers
    $headers = [
        "Content-Type: text/plain; charset=UTF-8",
        sprintf(
            "From: %s <%s>",
            get_bloginfo("name"),
            get_option("admin_email"),
        ),
        sprintf("Reply-To: %s <%s>", $name, $email),
    ];

    // Send the email
    $email_sent = wp_mail($to, $email_subject, $email_message, $headers);

    // Redirect based on success/failure
    if ($email_sent) {
        wp_safe_redirect(add_query_arg("form_success", "1", wp_get_referer()));
    } else {
        wp_safe_redirect(
            add_query_arg("form_error", "email_failed", wp_get_referer()),
        );
    }

    exit();
}

// Hook for logged-in users
add_action(
    "admin_post_submit_contact_form",
    "aaapos_handle_contact_form_submission",
);

// Hook for non-logged-in users
add_action(
    "admin_post_nopriv_submit_contact_form",
    "aaapos_handle_contact_form_submission",
);

/**
 * Display Success/Error Messages on Contact Page
 *
 * Call this function at the top of your contact form to display feedback
 */
function aaapos_display_contact_form_messages()
{
    // Check for success message
    if (isset($_GET["form_success"]) && $_GET["form_success"] == "1") {
        echo '<div class="form-message success" role="alert">';
        echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>';
        echo '<polyline points="22 4 12 14.01 9 11.01"></polyline>';
        echo "</svg>";
        echo "<span>" .
            esc_html__(
                'Thank you! Your message has been sent successfully. We\'ll get back to you soon.',
                "AAAPOS",
            ) .
            "</span>";
        echo "</div>";
    }

    // Check for error messages
    if (isset($_GET["form_error"])) {
        $error = sanitize_text_field($_GET["form_error"]);

        $error_messages = [
            "email_failed" => esc_html__(
                "Sorry, there was a problem sending your message. Please try again or contact us directly.",
                "AAAPOS",
            ),
            "spam" => esc_html__(
                "Your submission was flagged as spam. Please try again.",
                "AAAPOS",
            ),
        ];

        $error_text = isset($error_messages[$error])
            ? $error_messages[$error]
            : urldecode($error);

        echo '<div class="form-message error" role="alert">';
        echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<circle cx="12" cy="12" r="10"></circle>';
        echo '<line x1="12" y1="8" x2="12" y2="12"></line>';
        echo '<line x1="12" y1="16" x2="12.01" y2="16"></line>';
        echo "</svg>";
        echo "<span>" . esc_html($error_text) . "</span>";
        echo "</div>";
    }
}

/**
 * Add this code to your functions.php file
 * 
 * This will:
 * 1. Include the setup wizard class
 * 2. Trigger the setup on theme activation
 * 3. Add a menu item to re-run setup if needed
 */

// Include setup wizard
require_once get_template_directory() . '/inc/setup-wizard.php';

/**
 * Set activation redirect transient on theme switch
 */
function aaapos_setup_theme_activation() {
    // Only run on theme activation, not on every page load
    if (!get_option('aaapos_setup_complete')) {
        set_transient('_aaapos_activation_redirect', 1, 30);
    }
}
add_action('after_switch_theme', 'aaapos_setup_theme_activation');

/**
 * Add setup wizard link to admin menu (for re-running setup)
 */
function aaapos_add_setup_menu_link() {
    // Only show if setup is complete (to re-run) or not (to complete)
    add_theme_page(
        __('Theme Setup', 'aaapos'),
        __('Theme Setup', 'aaapos'),
        'manage_options',
        'aaapos-setup',
        '__return_false' // The wizard handles its own output
    );
}
add_action('admin_menu', 'aaapos_add_setup_menu_link');

/**
 * Add setup wizard link to Appearance menu
 */
function aaapos_add_appearance_setup_link($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'parent' => 'appearance',
        'id' => 'aaapos-setup-wizard',
        'title' => __('Theme Setup Wizard', 'aaapos'),
        'href' => admin_url('admin.php?page=aaapos-setup'),
    ));
}
add_action('admin_bar_menu', 'aaapos_add_appearance_setup_link', 999);

/**
 * Add admin notice if setup is not complete
 */
function aaapos_setup_admin_notice() {
    // Don't show on setup wizard page
    if (isset($_GET['page']) && $_GET['page'] === 'aaapos-setup') {
        return;
    }
    
    // Don't show if setup is complete
    if (get_option('aaapos_setup_complete')) {
        return;
    }
    
    // Show notice
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <strong><?php esc_html_e('Welcome to AAAPOS Theme!', 'aaapos'); ?></strong>
            <?php esc_html_e('Complete the setup wizard to configure your store.', 'aaapos'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=aaapos-setup')); ?>" class="button button-primary" style="margin-left: 10px;">
                <?php esc_html_e('Start Setup', 'aaapos'); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'aaapos_setup_admin_notice');

/**
 * Reset setup wizard (for development/testing)
 * Add ?reset_setup=1 to any admin URL to reset
 */
function aaapos_reset_setup_wizard() {
    if (isset($_GET['reset_setup']) && current_user_can('manage_options')) {
        delete_option('aaapos_setup_complete');
        delete_option('aaapos_pages_created');
        wp_redirect(admin_url('admin.php?page=aaapos-setup'));
        exit;
    }
}
add_action('admin_init', 'aaapos_reset_setup_wizard');

/**
 * Check if current page should display homepage sections
 * 
 * @return bool True if homepage sections should be displayed
 */
function aaapos_should_display_homepage_sections() {
    // Only on front page
    if (!is_front_page()) {
        return false;
    }
    
    // Get the page set as homepage
    $page_id = get_option('page_on_front');
    
    // If no static page is set (showing latest posts), don't show sections
    if (!$page_id) {
        return false;
    }
    
    // Check if the homepage uses the Homepage Template
    $template = get_page_template_slug($page_id);
    
    return ($template === 'page-templates/homepage.php');
}

/**
 * Check if WooCommerce sections should be displayed
 * Useful for conditionally showing product-related sections
 * 
 * @return bool True if WooCommerce is active and sections should show
 */
function aaapos_show_woocommerce_sections() {
    return class_exists('WooCommerce') && aaapos_should_display_homepage_sections();
}

/**
 * Get homepage template status for admin notices
 */
function aaapos_homepage_setup_notice() {
    // Only show on Pages screen
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'edit-page') {
        return;
    }
    
    // Check if homepage is configured
    $page_on_front = get_option('page_on_front');
    $show_on_front = get_option('show_on_front');
    
    if ($show_on_front !== 'page' || !$page_on_front) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php esc_html_e('AAAPOS Theme Tip:', 'aaapos'); ?></strong>
                <?php esc_html_e('To use the homepage sections (hero, products, categories), create a page with the "Homepage Template" and set it as your homepage in Settings > Reading.', 'aaapos'); ?>
            </p>
        </div>
        <?php
        return;
    }
    
    // Check if homepage has the right template
    $template = get_page_template_slug($page_on_front);
    if ($template !== 'page-templates/homepage.php') {
        $edit_link = get_edit_post_link($page_on_front);
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e('AAAPOS Theme Notice:', 'aaapos'); ?></strong>
                <?php 
                printf(
                    /* translators: %s: Edit page link */
                    esc_html__('Your homepage is not using the "Homepage Template". %sEdit the page%s and select "Homepage Template" from the Template dropdown to enable homepage sections.', 'aaapos'),
                    '<a href="' . esc_url($edit_link) . '">',
                    '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'aaapos_homepage_setup_notice');

/**
 * Add helpful text to the Homepage Template in the template selector
 */
function aaapos_add_template_descriptions($templates) {
    if (isset($templates['page-templates/homepage.php'])) {
        $templates['page-templates/homepage.php'] = __('Homepage Template (Enables hero, products, categories sections)', 'aaapos');
    }
    return $templates;
}
add_filter('theme_page_templates', 'aaapos_add_template_descriptions');