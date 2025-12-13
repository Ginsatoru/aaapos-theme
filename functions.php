<?php
/**
 * aaapos-prime Theme Functions
 */

if (!defined("ABSPATH")) {
    exit();
}

// Theme Constants
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
        "inc/testimonials.php",
        "inc/enqueue.php",
        "inc/security.php",
        "inc/template-tags.php",
        "inc/widgets.php",
        "inc/woocommerce.php",
        "inc/ajax.php",
        "inc/customizer/customizer.php",
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
    ];

    foreach ($includes as $file) {
        $filepath = AAAPOS_THEME_DIR . "/" . $file;

        if (file_exists($filepath)) {
            require_once $filepath;
        } else {
            if (defined("WP_DEBUG") && WP_DEBUG) {
                error_log("aaapos-prime: Missing file " . $file);
            }
        }
    }
}
add_action("after_setup_theme", "aaapos_load_theme_files", 1);

/**
 * Theme Setup
 *
 * Configure theme features, image sizes, and WordPress support.
 * This runs early in the WordPress initialization process.
 */
function aaapos_theme_setup()
{
    // Internationalization
    load_theme_textdomain("aaapos-prime", AAAPOS_THEME_DIR . "/languages");

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
        "primary" => esc_html__("Primary Navigation", "aaapos-prime"),
        "mobile" => esc_html__("Mobile Navigation", "aaapos-prime"),
        "footer" => esc_html__("Footer Navigation", "aaapos-prime"),
        "utility" => esc_html__("Utility Navigation", "aaapos-prime"),
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
        "aaapos-product-card" => esc_html__("Product Card", "aaapos-prime"),
        "aaapos-blog-card" => esc_html__("Blog Card", "aaapos-prime"),
        "aaapos-category-card" => esc_html__("Category Card", "aaapos-prime"),
        "aaapos-hero-large" => esc_html__("Hero Large", "aaapos-prime"),
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
    esc_html_e("Add a Menu", "aaapos-prime");
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
 */
function aaapos_theme_activation()
{
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

    // Create default homepage if none exists
    aaapos_create_default_homepage();
}
add_action("after_switch_theme", "aaapos_theme_activation");

/**
 * Create Default Homepage
 *
 * Auto-create a homepage using the homepage template on theme activation.
 */
function aaapos_create_default_homepage()
{
    // Check if homepage already exists
    $homepage_id = get_option("page_on_front");

    if (!$homepage_id || !get_post($homepage_id)) {
        // Create new homepage
        $homepage_id = wp_insert_post([
            "post_title" => esc_html__("Home", "aaapos-prime"),
            "post_content" => "",
            "post_status" => "publish",
            "post_type" => "page",
            "post_author" => 1,
            "page_template" => "page-templates/homepage.php",
        ]);

        if ($homepage_id && !is_wp_error($homepage_id)) {
            // Set as front page
            update_option("show_on_front", "page");
            update_option("page_on_front", $homepage_id);

            // Create blog page
            $blog_page_id = wp_insert_post([
                "post_title" => esc_html__("Blog", "aaapos-prime"),
                "post_content" => "",
                "post_status" => "publish",
                "post_type" => "page",
                "post_author" => 1,
            ]);

            if ($blog_page_id && !is_wp_error($blog_page_id)) {
                update_option("page_for_posts", $blog_page_id);
            }
        }
    }
}

/**
 * Theme Deactivation
 *
 * Cleanup on theme switch.
 */
function aaapos_theme_deactivation()
{
    // Flush rewrite rules
    flush_rewrite_rules();
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
                        "The aaapos-prime theme recommends installing %s for full functionality.",
                        "aaapos-prime",
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
                "aaapos-prime",
            ),
            esc_html__("Security Error", "aaapos-prime"),
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
        $errors[] = esc_html__("Name is required", "aaapos-prime");
    }

    if (empty($email)) {
        $errors[] = esc_html__("Email is required", "aaapos-prime");
    } elseif (!is_email($email)) {
        $errors[] = esc_html__(
            "Please enter a valid email address",
            "aaapos-prime",
        );
    }

    if (empty($subject)) {
        $errors[] = esc_html__("Subject is required", "aaapos-prime");
    }

    if (empty($message)) {
        $errors[] = esc_html__("Message is required", "aaapos-prime");
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
        !empty($phone) ? $phone : esc_html__("Not provided", "aaapos-prime"),
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
                "aaapos-prime",
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
                "aaapos-prime",
            ),
            "spam" => esc_html__(
                "Your submission was flagged as spam. Please try again.",
                "aaapos-prime",
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
