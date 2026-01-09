<?php
/**
 * Theme Setup and Configuration (setup.php)
 *
 * Comprehensive theme initialization with automatic setup,
 * intelligent defaults, and progressive enhancement.
 *
 * @package AAAPOS
 * @since 1.0.0
 */

// Security: Prevent direct file access
if (!defined("ABSPATH")) {
    exit();
}

/**
 * Core Theme Setup
 *
 * Registers all theme features, supports, and configurations.
 * This is the foundation that enables all theme functionality.
 */
function mr_theme_setup()
{
    // ============================================
    // INTERNATIONALIZATION
    // ============================================
    load_theme_textdomain("macedon-ranges", MR_THEME_DIR . "/languages");

    // ============================================
    // CORE WORDPRESS FEATURES
    // ============================================

    // Automatic feed links
    add_theme_support("automatic-feed-links");

    // Let WordPress manage document title
    add_theme_support("title-tag");

    // Featured images with custom sizes
    add_theme_support("post-thumbnails");
    set_post_thumbnail_size(1200, 675, true); // 16:9 ratio

    // ============================================
    // CUSTOM IMAGE SIZES
    // ============================================
    add_image_size("mr-hero", 1920, 800, true); // Hero section
    add_image_size("mr-featured", 800, 600, true); // Featured content
    add_image_size("mr-thumbnail", 400, 300, true); // Thumbnails
    add_image_size("mr-square", 600, 600, true); // Square images
    add_image_size("mr-portrait", 600, 800, true); // Portrait images

    // ============================================
    // HTML5 MARKUP
    // ============================================
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

    // ============================================
    // CUSTOMIZER FEATURES
    // ============================================

    // Selective refresh for widgets
    add_theme_support("customize-selective-refresh-widgets");

    // Custom background
    add_theme_support(
        "custom-background",
        apply_filters("mr_custom_background_args", [
            "default-color" => "ffffff",
            "default-image" => "",
            "default-preset" => "default",
            "default-position-x" => "left",
            "default-position-y" => "top",
            "default-size" => "auto",
            "default-repeat" => "repeat",
            "default-attachment" => "scroll",
        ]),
    );

    // Custom header
    add_theme_support(
        "custom-header",
        apply_filters("mr_custom_header_args", [
            "default-image" => "",
            "width" => 1920,
            "height" => 400,
            "flex-width" => true,
            "flex-height" => true,
            "header-text" => true,
            "default-text-color" => "000000",
        ]),
    );

    // Custom logo with responsive scaling
    add_theme_support(
        "custom-logo",
        apply_filters("mr_custom_logo_args", [
            "height" => 120,
            "width" => 400,
            "flex-width" => true,
            "flex-height" => true,
            "unlink-homepage-logo" => true,
        ]),
    );

    // ============================================
    // BLOCK EDITOR (GUTENBERG) SUPPORT
    // ============================================

    // Block styles
    add_theme_support("wp-block-styles");

    // Wide alignment
    add_theme_support("align-wide");

    // Responsive embedded content
    add_theme_support("responsive-embeds");

    // Editor styles
    add_theme_support("editor-styles");
    add_editor_style("assets/css/editor-styles.css");

    // Custom color palette for editor
    add_theme_support("editor-color-palette", mr_get_editor_color_palette());

    // Custom font sizes for editor
    add_theme_support("editor-font-sizes", mr_get_editor_font_sizes());

    // Disable custom colors (force palette)
    add_theme_support("disable-custom-colors");

    // Disable custom font sizes (force scale)
    add_theme_support("disable-custom-font-sizes");

    // Custom gradient presets
    add_theme_support("editor-gradient-presets", mr_get_editor_gradients());

    // ============================================
    // NAVIGATION MENUS
    // ============================================
    register_nav_menus([
        "primary" => esc_html__("Primary Navigation", "macedon-ranges"),
        "mobile" => esc_html__("Mobile Navigation", "macedon-ranges"),
        "footer" => esc_html__("Footer Navigation", "macedon-ranges"),
        "utility" => esc_html__("Utility Navigation", "macedon-ranges"),
        "social" => esc_html__("Social Links", "macedon-ranges"),
    ]);

    // ============================================
    // WOOCOMMERCE INTEGRATION
    // ============================================
    if (class_exists("WooCommerce")) {
        mr_setup_woocommerce_support();
    }

    // ============================================
    // POST FORMATS (Optional)
    // ============================================
    add_theme_support("post-formats", [
        "aside",
        "gallery",
        "link",
        "image",
        "quote",
        "status",
        "video",
        "audio",
    ]);

    // ============================================
    // AUTOMATIC SETUP ON FIRST RUN
    // ============================================
    mr_auto_setup_on_activation();
}
add_action("after_setup_theme", "mr_theme_setup");

/**
 * WooCommerce Support Setup
 *
 * Configure all WooCommerce-specific theme features.
 */
function mr_setup_woocommerce_support()
{
    // Basic WooCommerce support
    add_theme_support("woocommerce");

    // Product gallery features
    add_theme_support("wc-product-gallery-zoom");
    add_theme_support("wc-product-gallery-lightbox");
    add_theme_support("wc-product-gallery-slider");

    // WooCommerce 3.0+ features
    add_theme_support("woocommerce", [
        "thumbnail_image_width" => 400,
        "gallery_thumbnail_image_width" => 150,
        "single_image_width" => 800,
        "product_grid" => [
            "default_rows" => 3,
            "min_rows" => 2,
            "max_rows" => 8,
            "default_columns" => 4,
            "min_columns" => 2,
            "max_columns" => 5,
        ],
    ]);
}

/**
 * Editor Color Palette
 *
 * Define colors available in the block editor.
 * Syncs with CSS variables for consistency.
 */
function mr_get_editor_color_palette()
{
    return [
        [
            "name" => esc_html__("Primary", "macedon-ranges"),
            "slug" => "primary",
            "color" => get_theme_mod("mr_primary_color", "#0ea5e9"),
        ],
        [
            "name" => esc_html__("Secondary", "macedon-ranges"),
            "slug" => "secondary",
            "color" => get_theme_mod("mr_secondary_color", "#f59e0b"),
        ],
        [
            "name" => esc_html__("Accent", "macedon-ranges"),
            "slug" => "accent",
            "color" => get_theme_mod("mr_accent_color", "#10b981"),
        ],
        [
            "name" => esc_html__("Dark", "macedon-ranges"),
            "slug" => "dark",
            "color" => "#1f2937",
        ],
        [
            "name" => esc_html__("Gray", "macedon-ranges"),
            "slug" => "gray",
            "color" => "#6b7280",
        ],
        [
            "name" => esc_html__("Light", "macedon-ranges"),
            "slug" => "light",
            "color" => "#f3f4f6",
        ],
        [
            "name" => esc_html__("White", "macedon-ranges"),
            "slug" => "white",
            "color" => "#ffffff",
        ],
        [
            "name" => esc_html__("Black", "macedon-ranges"),
            "slug" => "black",
            "color" => "#000000",
        ],
    ];
}

/**
 * Editor Font Sizes
 *
 * Define font size scale for block editor.
 */
function mr_get_editor_font_sizes()
{
    return [
        [
            "name" => esc_html__("Small", "macedon-ranges"),
            "slug" => "small",
            "size" => 14,
        ],
        [
            "name" => esc_html__("Normal", "macedon-ranges"),
            "slug" => "normal",
            "size" => 16,
        ],
        [
            "name" => esc_html__("Medium", "macedon-ranges"),
            "slug" => "medium",
            "size" => 18,
        ],
        [
            "name" => esc_html__("Large", "macedon-ranges"),
            "slug" => "large",
            "size" => 24,
        ],
        [
            "name" => esc_html__("Extra Large", "macedon-ranges"),
            "slug" => "extra-large",
            "size" => 32,
        ],
        [
            "name" => esc_html__("Huge", "macedon-ranges"),
            "slug" => "huge",
            "size" => 48,
        ],
    ];
}

/**
 * Editor Gradient Presets
 *
 * Define gradient options for block editor.
 */
function mr_get_editor_gradients()
{
    return [
        [
            "name" => esc_html__("Primary Gradient", "macedon-ranges"),
            "gradient" => "linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%)",
            "slug" => "primary-gradient",
        ],
        [
            "name" => esc_html__("Sunset", "macedon-ranges"),
            "gradient" => "linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)",
            "slug" => "sunset",
        ],
        [
            "name" => esc_html__("Fresh", "macedon-ranges"),
            "gradient" => "linear-gradient(135deg, #10b981 0%, #06b6d4 100%)",
            "slug" => "fresh",
        ],
        [
            "name" => esc_html__("Dark Overlay", "macedon-ranges"),
            "gradient" =>
                "linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%)",
            "slug" => "dark-overlay",
        ],
    ];
}

/**
 * Widget Areas Registration
 *
 * Register all sidebar and widget areas with intelligent defaults.
 */
function mr_widgets_init()
{
    // Default widget wrapper config
    $widget_defaults = [
        "before_widget" => '<div id="%1$s" class="widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h4 class="widget-title">',
        "after_title" => "</h4>",
    ];

    // ============================================
    // FOOTER WIDGETS
    // ============================================

    $footer_columns = absint(get_theme_mod("mr_footer_columns", 4));

    for ($i = 1; $i <= $footer_columns; $i++) {
        register_sidebar(
            array_merge($widget_defaults, [
                "name" => sprintf(
                    esc_html__("Footer Column %d", "macedon-ranges"),
                    $i,
                ),
                "id" => "footer-" . $i,
                "description" => sprintf(
                    esc_html__(
                        "Add widgets here for footer column %d.",
                        "macedon-ranges",
                    ),
                    $i,
                ),
            ]),
        );
    }

    // ============================================
    // SHOP SIDEBAR
    // ============================================

    if (class_exists("WooCommerce")) {
        register_sidebar(
            array_merge($widget_defaults, [
                "name" => esc_html__("Shop Sidebar", "macedon-ranges"),
                "id" => "shop-sidebar",
                "description" => esc_html__(
                    "Displays on WooCommerce shop and product pages.",
                    "macedon-ranges",
                ),
            ]),
        );
    }

    // ============================================
    // BLOG SIDEBAR
    // ============================================

    register_sidebar(
        array_merge($widget_defaults, [
            "name" => esc_html__("Blog Sidebar", "macedon-ranges"),
            "id" => "blog-sidebar",
            "description" => esc_html__(
                "Displays on blog archive and single post pages.",
                "macedon-ranges",
            ),
        ]),
    );

    // ============================================
    // PAGE SIDEBAR
    // ============================================

    register_sidebar(
        array_merge($widget_defaults, [
            "name" => esc_html__("Page Sidebar", "macedon-ranges"),
            "id" => "page-sidebar",
            "description" => esc_html__(
                "Displays on pages when sidebar layout is selected.",
                "macedon-ranges",
            ),
        ]),
    );

    // ============================================
    // HEADER WIDGET AREA
    // ============================================

    if (get_theme_mod("mr_enable_header_widgets", false)) {
        register_sidebar(
            array_merge($widget_defaults, [
                "name" => esc_html__("Header Widgets", "macedon-ranges"),
                "id" => "header-widgets",
                "description" => esc_html__(
                    "Displays in the header area (enable in Customizer).",
                    "macedon-ranges",
                ),
            ]),
        );
    }

    // ============================================
    // OFF-CANVAS WIDGET AREA
    // ============================================

    register_sidebar(
        array_merge($widget_defaults, [
            "name" => esc_html__("Off-Canvas Menu", "macedon-ranges"),
            "id" => "offcanvas-sidebar",
            "description" => esc_html__(
                "Displays in the mobile off-canvas menu.",
                "macedon-ranges",
            ),
        ]),
    );
}
add_action("widgets_init", "mr_widgets_init");

/**
 * Content Width
 *
 * Set maximum content width for embedded media.
 * This affects oEmbed dimensions.
 */
function mr_content_width()
{
    $GLOBALS["content_width"] = apply_filters("mr_content_width", 1200);
}
add_action("after_setup_theme", "mr_content_width", 0);

/**
 * Automatic Theme Setup on Activation
 *
 * Runs once when theme is first activated.
 * Creates pages, sets up menus, configures defaults.
 */
function mr_auto_setup_on_activation()
{
    // Check if already run
    $setup_complete = get_option("mr_auto_setup_complete");

    if ($setup_complete) {
        return;
    }

    // ============================================
    // CREATE DEFAULT PAGES
    // ============================================
    mr_create_default_pages();

    // ============================================
    // CREATE DEFAULT MENUS
    // ============================================
    mr_create_default_menus();

    // ============================================
    // SET CUSTOMIZER DEFAULTS
    // ============================================
    mr_set_customizer_defaults();

    // ============================================
    // CONFIGURE WOOCOMMERCE
    // ============================================
    if (class_exists("WooCommerce")) {
        mr_configure_woocommerce_defaults();
    }

    // ============================================
    // SET PERMALINKS
    // ============================================
    mr_configure_permalinks();

    // Mark setup as complete
    update_option("mr_auto_setup_complete", true);

    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Create Default Pages
 *
 * Automatically creates essential pages if they don't exist.
 * IMPROVED: Better duplicate checking with multiple methods
 */
function mr_create_default_pages()
{
    $default_pages = [
        "home" => [
            "title" => esc_html__("Home", "macedon-ranges"),
            "template" => "page-templates/homepage.php",
            "is_front" => true,
        ],
        "blog" => [
            "title" => esc_html__("Blog", "macedon-ranges"),
            "template" => "",
            "is_posts" => true,
        ],
        "about" => [
            "title" => esc_html__("About Us", "macedon-ranges"),
            "content" => esc_html__(
                "Tell your story here. Edit this page to add your content.",
                "macedon-ranges",
            ),
        ],
        "contact" => [
            "title" => esc_html__("Contact", "macedon-ranges"),
            "content" => esc_html__(
                "Add your contact information and contact form here.",
                "macedon-ranges",
            ),
        ],
    ];

    // Create pages
    foreach ($default_pages as $slug => $page) {
        // IMPROVED: Multiple checks to prevent duplicates
        
        // Method 1: Check by slug
        $existing_page = get_page_by_path($slug);
        
        // Method 2: Check by title if slug check fails
        if (!$existing_page) {
            $existing_page = get_page_by_title($page["title"], OBJECT, 'page');
        }
        
        // Method 3: For front page, check if one is already set
        if (isset($page["is_front"]) && $page["is_front"]) {
            $front_page_id = get_option("page_on_front");
            if ($front_page_id && get_post($front_page_id)) {
                continue; // Skip creation, front page already exists
            }
        }
        
        // Method 4: For blog page, check if one is already set
        if (isset($page["is_posts"]) && $page["is_posts"]) {
            $blog_page_id = get_option("page_for_posts");
            if ($blog_page_id && get_post($blog_page_id)) {
                continue; // Skip creation, blog page already exists
            }
        }

        // Only create if page doesn't exist
        if (!$existing_page) {
            $page_id = wp_insert_post([
                "post_title" => $page["title"],
                "post_content" => isset($page["content"])
                    ? $page["content"]
                    : "",
                "post_status" => "publish",
                "post_type" => "page",
                "post_name" => $slug,
                "page_template" => isset($page["template"])
                    ? $page["template"]
                    : "",
            ]);

            // Set as front page
            if (isset($page["is_front"]) && $page["is_front"] && $page_id && !is_wp_error($page_id)) {
                update_option("show_on_front", "page");
                update_option("page_on_front", $page_id);
            }

            // Set as posts page
            if (isset($page["is_posts"]) && $page["is_posts"] && $page_id && !is_wp_error($page_id)) {
                update_option("page_for_posts", $page_id);
            }
        }
    }
}

/**
 * Create Default Menus
 *
 * Automatically creates and assigns navigation menus.
 * IMPROVED: Better duplicate checking
 */
function mr_create_default_menus()
{
    // Check if primary menu exists
    $menu_name = "Primary Menu";
    $menu_exists = wp_get_nav_menu_object($menu_name);

    if (!$menu_exists) {
        // Create menu
        $menu_id = wp_create_nav_menu($menu_name);

        if (!is_wp_error($menu_id)) {
            // Get pages
            $home_page = get_page_by_path("home");
            $blog_page = get_page_by_path("blog");
            $about_page = get_page_by_path("about");
            $contact_page = get_page_by_path("contact");

            $position = 1;

            // Add menu items
            if ($home_page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    "menu-item-title" => esc_html__("Home", "macedon-ranges"),
                    "menu-item-object-id" => $home_page->ID,
                    "menu-item-object" => "page",
                    "menu-item-type" => "post_type",
                    "menu-item-status" => "publish",
                    "menu-item-position" => $position++,
                ]);
            }

            // Add Shop link if WooCommerce is active
            if (class_exists("WooCommerce")) {
                $shop_page_id = get_option("woocommerce_shop_page_id");
                if ($shop_page_id) {
                    wp_update_nav_menu_item($menu_id, 0, [
                        "menu-item-title" => esc_html__(
                            "Shop",
                            "macedon-ranges",
                        ),
                        "menu-item-object-id" => $shop_page_id,
                        "menu-item-object" => "page",
                        "menu-item-type" => "post_type",
                        "menu-item-status" => "publish",
                        "menu-item-position" => $position++,
                    ]);
                }
            }

            if ($about_page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    "menu-item-title" => esc_html__("About", "macedon-ranges"),
                    "menu-item-object-id" => $about_page->ID,
                    "menu-item-object" => "page",
                    "menu-item-type" => "post_type",
                    "menu-item-status" => "publish",
                    "menu-item-position" => $position++,
                ]);
            }

            if ($blog_page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    "menu-item-title" => esc_html__("Blog", "macedon-ranges"),
                    "menu-item-object-id" => $blog_page->ID,
                    "menu-item-object" => "page",
                    "menu-item-type" => "post_type",
                    "menu-item-status" => "publish",
                    "menu-item-position" => $position++,
                ]);
            }

            if ($contact_page) {
                wp_update_nav_menu_item($menu_id, 0, [
                    "menu-item-title" => esc_html__(
                        "Contact",
                        "macedon-ranges",
                    ),
                    "menu-item-object-id" => $contact_page->ID,
                    "menu-item-object" => "page",
                    "menu-item-type" => "post_type",
                    "menu-item-status" => "publish",
                    "menu-item-position" => $position++,
                ]);
            }

            // Assign to primary and mobile locations
            $locations = get_theme_mod("nav_menu_locations");
            if (!is_array($locations)) {
                $locations = [];
            }
            $locations["primary"] = $menu_id;
            $locations["mobile"] = $menu_id;
            set_theme_mod("nav_menu_locations", $locations);
        }
    }
}

/**
 * Set Customizer Defaults
 *
 * Initialize all Customizer settings with sensible defaults.
 */
function mr_set_customizer_defaults()
{
    $defaults = [
        // Colors
        "mr_primary_color" => "#0ea5e9",
        "mr_secondary_color" => "#f59e0b",
        "mr_accent_color" => "#10b981",
        "mr_text_color" => "#1f2937",
        "mr_heading_color" => "#111827",

        // Typography
        "mr_body_font" => "Inter",
        "mr_heading_font" => "Montserrat",
        "mr_accent_font" => "Playfair Display",
        "mr_font_size_base" => 16,

        // Header
        "mr_header_layout" => "default",
        "mr_header_sticky" => true,
        "mr_header_transparent" => false,
        "mr_logo_width" => 200,
        "mr_logo_width_mobile" => 150,

        // Hero Section
        "mr_show_hero" => true,
        "mr_hero_height" => 600,
        "mr_hero_overlay_opacity" => 0.4,

        // Homepage Sections
        "mr_show_featured_products" => true,
        "mr_show_categories" => true,
        "mr_show_deals" => true,
        "mr_show_testimonials" => true,
        "mr_show_blog_preview" => true,
        "mr_show_newsletter" => true,

        // Footer
        "mr_footer_columns" => 4,
        "mr_show_footer_widgets" => true,
        "mr_footer_copyright" => sprintf(
            "&copy; %s %s. All rights reserved.",
            gmdate("Y"),
            get_bloginfo("name"),
        ),

        // Performance
        "mr_enable_animations" => true,
        "mr_animation_speed" => "normal",
        "mr_lazy_load_images" => true,

        // WooCommerce
        "mr_products_per_page" => 12,
        "mr_product_columns" => 4,
        "mr_product_columns_mobile" => 2,

        // Blog
        "mr_excerpt_length" => 25,
        "mr_posts_per_page" => 9,
    ];

    foreach ($defaults as $setting => $value) {
        if (false === get_theme_mod($setting)) {
            set_theme_mod($setting, $value);
        }
    }
}

/**
 * Configure WooCommerce Defaults
 *
 * Set up WooCommerce pages and settings automatically.
 * IMPROVED: Better duplicate page prevention
 */
function mr_configure_woocommerce_defaults()
{
    // Create WooCommerce pages if they don't exist
    $wc_pages = [
        "shop" => esc_html__("Shop", "macedon-ranges"),
        "cart" => esc_html__("Cart", "macedon-ranges"),
        "checkout" => esc_html__("Checkout", "macedon-ranges"),
        "myaccount" => esc_html__("My Account", "macedon-ranges"),
    ];

    foreach ($wc_pages as $slug => $title) {
        $page_id = get_option("woocommerce_" . $slug . "_page_id");

        // IMPROVED: Check both if option exists AND if page actually exists
        if (!$page_id || !get_post($page_id)) {
            // Also check if page exists by slug to prevent duplicates
            $existing_page = get_page_by_path($slug);
            
            if (!$existing_page) {
                $page_id = wp_insert_post([
                    "post_title" => $title,
                    "post_content" => "[woocommerce_" . $slug . "]",
                    "post_status" => "publish",
                    "post_type" => "page",
                    "post_name" => $slug,
                ]);

                if ($page_id && !is_wp_error($page_id)) {
                    update_option("woocommerce_" . $slug . "_page_id", $page_id);
                }
            } else {
                // Page exists, just update the option
                update_option("woocommerce_" . $slug . "_page_id", $existing_page->ID);
            }
        }
    }

    // Set WooCommerce image dimensions
    update_option("woocommerce_thumbnail_image_width", 400);
    update_option("woocommerce_single_image_width", 800);
    update_option("woocommerce_thumbnail_cropping", "1:1");
}

/**
 * Configure Permalink Structure
 *
 * Set SEO-friendly permalink structure.
 */
function mr_configure_permalinks()
{
    global $wp_rewrite;

    // Set to post name structure
    $wp_rewrite->set_permalink_structure("/%postname%/");

    // Flush rules
    flush_rewrite_rules();
}

/**
 * Theme Reset Function
 *
 * Allows admin to reset theme to defaults.
 * Add button to Customizer or theme options page.
 */
function mr_reset_theme_to_defaults()
{
    // Security check
    if (!current_user_can("manage_options")) {
        return false;
    }

    // Verify nonce
    if (
        !isset($_POST["mr_reset_nonce"]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["mr_reset_nonce"])),
            "mr_reset_theme",
        )
    ) {
        return false;
    }

    // Remove auto setup flag
    delete_option("mr_auto_setup_complete");

    // Remove all theme mods
    remove_theme_mods();

    // Run setup again
    mr_auto_setup_on_activation();

    return true;
}

/**
 * Add theme reset button to Customizer (optional)
 */
function mr_add_reset_button_to_customizer($wp_customize)
{
    $wp_customize->add_section("mr_reset_section", [
        "title" => esc_html__("Theme Reset", "macedon-ranges"),
        "priority" => 200,
    ]);

    $wp_customize->add_setting("mr_reset_button", [
        "sanitize_callback" => "absint",
    ]);

    $wp_customize->add_control(
        new WP_Customize_Control($wp_customize, "mr_reset_button", [
            "label" => esc_html__("Reset Theme", "macedon-ranges"),
            "description" => esc_html__(
                "Click to reset all theme settings to default values. This cannot be undone.",
                "macedon-ranges",
            ),
            "section" => "mr_reset_section",
            "type" => "button",
        ]),
    );
}
// Uncomment to enable reset button in Customizer
// add_action('customize_register', 'mr_add_reset_button_to_customizer');