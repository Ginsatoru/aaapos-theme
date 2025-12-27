<?php
/**
 * WooCommerce Integration - FULLY INTEGRATED WITH CUSTOMIZER
 * NOW WITH WORKING CATEGORY FILTER FUNCTIONALITY
 * UPDATED: Added fallback background image support
 * FIXED: Removed shipping calculator from cart page
 *
 * ALL settings now respect WooCommerce Customizer options
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Add WooCommerce Support
 */
function aaapos_woocommerce_setup()
{
    add_theme_support("woocommerce");
    add_theme_support("wc-product-gallery-zoom");
    add_theme_support("wc-product-gallery-lightbox");
    add_theme_support("wc-product-gallery-slider");
}
add_action("after_setup_theme", "aaapos_woocommerce_setup");

/**
 * Get Shop Header Background Image with Fallback
 * Returns customizer image if set, otherwise returns default fallback
 *
 * @return string Image URL
 */
function aaapos_get_shop_header_bg_image()
{
    // Get customizer setting
    $custom_image = get_theme_mod("shop_header_bg_image", "");

    // If custom image is set, use it
    if (!empty($custom_image)) {
        return esc_url($custom_image);
    }

    // Otherwise, use fallback image
    $fallback_image =
        get_template_directory_uri() . "/assets/images/shop-img-header.png";

    // Check if fallback image exists
    if (
        file_exists(
            get_template_directory() . "/assets/images/shop-img-header.png",
        )
    ) {
        return esc_url($fallback_image);
    }

    // If even fallback doesn't exist, return empty
    return "";
}

/**
 * REPLACE TEXT RATINGS WITH STAR ICONS
 * This removes the default WooCommerce rating HTML completely
 */
remove_action(
    "woocommerce_after_shop_loop_item_title",
    "woocommerce_template_loop_rating",
    5,
);
remove_action(
    "woocommerce_single_product_summary",
    "woocommerce_template_single_rating",
    10,
);

/**
 * Add Custom Star Rating to Product Loop (Shop/Archive Pages)
 */
function aaapos_custom_loop_rating()
{
    global $product;

    if (!get_theme_mod("show_product_rating", true)) {
        return;
    }

    $average_rating = $product->get_average_rating();
    $rating_count = $product->get_rating_count();

    if ($average_rating <= 0) {
        return;
    }

    $gradient_id = "half-fill-loop-" . $product->get_id();
    ?>
    <div class="product-rating">
        <div class="rating-stars" aria-label="<?php echo esc_attr(
            sprintf(
                __("Rated %s out of 5", "aaapos-prime"),
                number_format($average_rating, 2),
            ),
        ); ?>">
            <?php for ($i = 1; $i <= 5; $i++) {
                if ($i <= floor($average_rating)) {
                    // Full star
                    echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                } elseif (
                    $i == ceil($average_rating) &&
                    $average_rating - floor($average_rating) >= 0.5
                ) {
                    // Half star
                    echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><defs><linearGradient id="' .
                        esc_attr($gradient_id) .
                        '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' .
                        esc_attr($gradient_id) .
                        ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                } else {
                    // Empty star
                    echo '<svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#d1d5db"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                }
            } ?>
        </div>
        <?php if ($rating_count > 0): ?>
            <span class="rating-count">(<?php echo esc_html(
                $rating_count,
            ); ?>)</span>
        <?php endif; ?>
    </div>
    <?php
}
add_action(
    "woocommerce_after_shop_loop_item_title",
    "aaapos_custom_loop_rating",
    5,
);

/**
 * Render Category Filter with Modern Card Design
 * Updated version with proper folder icon SVGs
 */

if (!function_exists("aaapos_render_category_filter")) {
    function aaapos_render_category_filter()
    {
        // Check if we're on shop or category page
        if (!is_shop() && !is_product_category()) {
            return;
        }

        // Check if filter is enabled
        if (!get_theme_mod("enable_category_filter", true)) {
            return;
        }

        // Get selected categories from customizer (comma-separated string)
        $selected_categories_string = get_theme_mod(
            "category_filter_categories",
            "",
        );

        // Convert to array
        $selected_categories = [];
        if (!empty($selected_categories_string)) {
            $selected_categories = array_map(
                "intval",
                explode(",", $selected_categories_string),
            );
            $selected_categories = array_filter($selected_categories);
        }

        // Build query args
        $args = [
            "taxonomy" => "product_cat",
            "hide_empty" => true,
            "orderby" => "name",
            "order" => "ASC",
        ];

        // If specific categories selected, filter them
        if (!empty($selected_categories)) {
            $args["include"] = $selected_categories;
        }

        $categories = get_terms($args);

        // If no categories or error, don't show filter
        if (empty($categories) || is_wp_error($categories)) {
            return;
        }

        // Get total product count
        $all_products_count = wp_count_posts("product")->publish;

        // Get current category (if on category page)
        $current_cat = is_product_category()
            ? get_queried_object()->term_id
            : 0;

        // Get shop URL
        $shop_url = get_permalink(wc_get_page_id("shop"));
        ?>
        
        <div class="shop-category-filter">
            <div class="category-filter-buttons">
                
                <!-- All Products Button -->
                <a href="<?php echo esc_url($shop_url); ?>" 
                   class="category-filter-btn<?php echo !$current_cat
                       ? " active"
                       : ""; ?>" 
                   aria-current="<?php echo !$current_cat
                       ? "page"
                       : "false"; ?>">
                    
                    <!-- Icon Box with Grid Icon -->
                    <div class="filter-icon-box">
                        <svg class="filter-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        </svg>
                    </div>
                    
                    <!-- Text Content -->
                    <div class="filter-content">
                        <span class="filter-label"><?php esc_html_e(
                            "All Products",
                            "aaapos-prime",
                        ); ?></span>
                        <span class="filter-count"><?php printf(
                            esc_html(
                                _n(
                                    "%s Item",
                                    "%s Items",
                                    $all_products_count,
                                    "aaapos-prime",
                                ),
                            ),
                            number_format_i18n($all_products_count),
                        ); ?></span>
                    </div>
                </a>
                
                <?php // Loop through selected categories

        foreach ($categories as $category):

                    $category_url = get_term_link($category);

                    if (is_wp_error($category_url)) {
                        continue;
                    }

                    $is_active = $current_cat === $category->term_id;
                    $product_count = $category->count;
                    ?>
                
                <a href="<?php echo esc_url($category_url); ?>" 
                   class="category-filter-btn<?php echo $is_active
                       ? " active"
                       : ""; ?>"
                   aria-current="<?php echo $is_active ? "page" : "false"; ?>">
                    
                    <!-- Icon Box with Folder Icon -->
                    <div class="filter-icon-box">
                        <svg class="filter-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 7H4C2.89543 7 2 7.89543 2 9V19C2 20.1046 2.89543 21 4 21H20C21.1046 21 22 20.1046 22 19V9C22 7.89543 21.1046 7 20 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 7V5C16 3.89543 15.1046 3 14 3H10C8.89543 3 8 3.89543 8 5V7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    
                    <!-- Text Content -->
                    <div class="filter-content">
                        <span class="filter-label"><?php echo esc_html(
                            $category->name,
                        ); ?></span>
                        <span class="filter-count"><?php printf(
                            esc_html(
                                _n(
                                    "%s Item",
                                    "%s Items",
                                    $product_count,
                                    "aaapos-prime",
                                ),
                            ),
                            number_format_i18n($product_count),
                        ); ?></span>
                    </div>
                </a>
                
                <?php
                endforeach; ?>
                
            </div><!-- .category-filter-buttons -->
        </div><!-- .shop-category-filter -->
        
        <?php
    }
}

/**
 * Add Custom Star Rating to Single Product Page
 */
function aaapos_custom_single_rating()
{
    global $product;

    if (!wc_review_ratings_enabled()) {
        return;
    }

    $average_rating = $product->get_average_rating();
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();

    if ($rating_count <= 0) {
        return;
    }

    $gradient_id = "half-fill-single-" . $product->get_id();
    ?>
    <div class="woocommerce-product-rating">
        <div class="rating-stars-wrapper">
            <div class="rating-stars" aria-label="<?php echo esc_attr(
                sprintf(
                    __("Rated %s out of 5", "aaapos-prime"),
                    number_format($average_rating, 2),
                ),
            ); ?>">
                <?php for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($average_rating)) {
                        // Full star
                        echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                    } elseif (
                        $i == ceil($average_rating) &&
                        $average_rating - floor($average_rating) >= 0.5
                    ) {
                        // Half star
                        echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><defs><linearGradient id="' .
                            esc_attr($gradient_id) .
                            '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' .
                            esc_attr($gradient_id) .
                            ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                    } else {
                        // Empty star
                        echo '<svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#d1d5db"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                    }
                } ?>
            </div>
            <span class="rating-text">
                <strong><?php echo esc_html(
                    number_format($average_rating, 1),
                ); ?></strong> 
                <?php printf(
                    _n(
                        "(%s review)",
                        "(%s reviews)",
                        $review_count,
                        "aaapos-prime",
                    ),
                    '<span class="count">' .
                        esc_html($review_count) .
                        "</span>",
                ); ?>
            </span>
        </div>
    </div>
    <?php
}
add_action(
    "woocommerce_single_product_summary",
    "aaapos_custom_single_rating",
    10,
);

/**
 * Enqueue WooCommerce Styles - COMPLETE SYSTEM
 */
function aaapos_woocommerce_nuclear_styles()
{
    // Only on WooCommerce pages
    if (
        !is_woocommerce() &&
        !is_cart() &&
        !is_checkout() &&
        !is_account_page() &&
        !is_search()
    ) {
        return;
    }

    // Main WooCommerce styles (base styles, buttons, forms, etc.)
    wp_enqueue_style(
        "aaapos-woocommerce-base",
        get_template_directory_uri() .
            "/assets/css/woocommerce/woocommerce.css",
        [],
        AAAPOS_VERSION . "." . time(),
        "all",
    );

    // Category filter styles - enqueue when enabled and on shop pages
    if (
        get_theme_mod("enable_category_filter", true) &&
        (is_shop() || is_product_category())
    ) {
        wp_enqueue_style(
            "aaapos-category-filter",
            get_template_directory_uri() .
                "/assets/css/components/categories-shop.css",
            ["aaapos-woocommerce-base"],
            AAAPOS_VERSION . "." . time(),
            "all",
        );
    }

    // Cart page styles
    if (is_cart()) {
        wp_enqueue_style(
            "aaapos-cart",
            get_template_directory_uri() .
                "/assets/css/components/cart/cart-main.css",
            ["aaapos-woocommerce-base"],
            AAAPOS_VERSION . "." . time(),
            "all",
        );
    }

    // My Account page styles
    if (is_account_page()) {
        wp_enqueue_style(
            "aaapos-woocommerce-myaccount",
            get_template_directory_uri() .
                "/assets/css/woocommerce-myaccount.css",
            ["aaapos-woocommerce-base"],
            AAAPOS_VERSION . "." . time(),
        );
    }

    // Cart notifications CSS (toast notifications)
    wp_enqueue_style(
        "aaapos-cart-notifications",
        get_template_directory_uri() . "/assets/css/cart-notifications.css",
        ["aaapos-woocommerce-base"],
        AAAPOS_VERSION,
        "all",
    );

    // WooCommerce cart functionality
    wp_enqueue_script(
        "aaapos-woocommerce-js",
        get_template_directory_uri() . "/assets/js/woocommerce.js",
        ["jquery", "wc-add-to-cart"],
        AAAPOS_VERSION,
        true,
    );

    // Cart notifications JS (handles toast notifications)
    wp_enqueue_script(
        "aaapos-cart-notifications-js",
        get_template_directory_uri() . "/assets/js/cart-notifications.js",
        ["jquery", "aaapos-woocommerce-js"],
        AAAPOS_VERSION,
        true,
    );

    // Quantity selector enhancement (for single product page)
    if (is_product()) {
        wp_enqueue_script(
            "aaapos-quantity-selector",
            get_template_directory_uri() . "/assets/js/quantity-selector.js",
            ["jquery"],
            AAAPOS_VERSION,
            true,
        );
    }
}
add_action("wp_enqueue_scripts", "aaapos_woocommerce_nuclear_styles", 999);

/**
 * Add Clear Shopping Cart Button
 * This adds a "Clear Shopping Cart" link below the cart actions
 */
add_action("woocommerce_cart_actions", "add_clear_cart_button");
function add_clear_cart_button()
{
    ?>
    <a href="<?php echo esc_url(
        add_query_arg("clear-cart", "true", wc_get_cart_url()),
    ); ?>" 
       class="button clear-cart-link" 
       onclick="return confirm('<?php esc_attr_e(
           "Are you sure you want to clear your cart?",
           "macedon-ranges",
       ); ?>');">
        <?php esc_html_e("Clear Shopping Cart", "macedon-ranges"); ?>
    </a>
    <?php
}

/**
 * Handle Clear Cart Action
 */
add_action("init", "handle_clear_cart");
function handle_clear_cart()
{
    if (isset($_GET["clear-cart"]) && $_GET["clear-cart"] === "true") {
        WC()->cart->empty_cart();
        wp_safe_redirect(wc_get_cart_url());
        exit();
    }
}

/**
 * Handle Coupon Removal and Application with Redirect
 */
function mr_handle_coupon_removal()
{
    // Only run on cart page
    if (!is_cart()) {
        return;
    }

    // Handle removal via form button
    if (isset($_POST["remove_coupon"]) && !empty($_POST["remove_coupon"])) {
        $coupon_code = sanitize_text_field($_POST["remove_coupon"]);

        // Verify nonce
        if (
            isset($_POST["woocommerce-cart-nonce"]) &&
            wp_verify_nonce(
                $_POST["woocommerce-cart-nonce"],
                "woocommerce-cart",
            )
        ) {
            WC()->cart->remove_coupon($coupon_code);
            wc_add_notice(
                __("Coupon removed successfully.", "macedon-ranges"),
                "success",
            );

            // Redirect to prevent form resubmission warning
            wp_safe_redirect(wc_get_cart_url());
            exit();
        }
    }

    // Also handle removal via URL (backward compatibility)
    if (isset($_GET["remove_coupon"])) {
        $coupon_code = sanitize_text_field($_GET["remove_coupon"]);
        WC()->cart->remove_coupon($coupon_code);
        wc_add_notice(
            __("Coupon removed successfully.", "macedon-ranges"),
            "success",
        );
        wp_safe_redirect(wc_get_cart_url());
        exit();
    }
}
add_action("wp_loaded", "mr_handle_coupon_removal", 20);

/**
 * Enforce Single Coupon Policy
 * Automatically removes existing coupon when applying a new one
 */
function mr_enforce_single_coupon($valid, $coupon)
{
    if (!$valid) {
        return $valid;
    }

    $applied_coupons = WC()->cart->get_applied_coupons();

    // If there's already a coupon and user is trying to apply a different one
    if (
        !empty($applied_coupons) &&
        !in_array($coupon->get_code(), $applied_coupons)
    ) {
        // Remove all existing coupons
        foreach ($applied_coupons as $applied_coupon) {
            WC()->cart->remove_coupon($applied_coupon);
        }

        wc_add_notice(
            sprintf(
                __(
                    'Previous coupon "%s" was removed. Only one coupon can be applied at a time.',
                    "macedon-ranges",
                ),
                $applied_coupons[0],
            ),
            "notice",
        );
    }

    return $valid;
}
add_filter("woocommerce_coupon_is_valid", "mr_enforce_single_coupon", 10, 2);

/**
 * REMOVED: Force Enable Shipping Calculator functions
 * These were causing shipping to show on cart page
 */
// DELETED: add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
// DELETED: add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

/**
 * REMOVED: Functions that forced shipping display on cart
 */
// DELETED: ensure_cart_totals_display()
// DELETED: add_filter('woocommerce_cart_needs_shipping_address', '__return_true');

/**
 * Enable Update Cart button
 */
add_action("wp_footer", "enable_cart_update_button");
function enable_cart_update_button()
{
    if (is_cart()) { ?>
        <script>
        jQuery(function($) {
            // Enable update cart button when quantity changes
            $('div.woocommerce').on('change', 'input.qty', function(){
                $('[name="update_cart"]').prop('disabled', false);
            });
        });
        </script>
        <?php }
}

/**
 * Simplify Cart Shipping Display - Show only selected shipping cost
 * This removes the radio buttons and shows shipping like checkout
 */
function aaapos_simplify_cart_shipping_display()
{
    if (!is_cart()) {
        return;
    } ?>
    <style>
        /* Hide shipping calculator and radio buttons on cart page */
        .cart_totals .shipping-calculator-button,
        .cart_totals .shipping-calculator-form,
        .cart_totals .woocommerce-shipping-methods {
            display: none !important;
        }
        
        /* Show only the shipping label and cost */
        .cart_totals .shipping th,
        .cart_totals .shipping td {
            display: table-cell !important;
        }
        
        .cart_totals .shipping td {
            font-weight: 600;
        }
    </style>
    <script>
    jQuery(function($) {
        // Simplify shipping display - show only selected method cost
        function simplifyShippingDisplay() {
            var $shippingRow = $('.cart_totals .shipping');
            
            if ($shippingRow.length) {
                // Find selected shipping method
                var $selectedMethod = $shippingRow.find('input[type="radio"]:checked');
                
                if ($selectedMethod.length) {
                    // Get the shipping cost from the label
                    var $label = $selectedMethod.closest('li').find('label');
                    var shippingText = $label.text().trim();
                    
                    // Extract just the cost (everything after the colon)
                    var cost = shippingText.split(':').pop().trim();
                    
                    // Update the cell to show only the cost
                    $shippingRow.find('td').html('<span class="woocommerce-Price-amount amount">' + cost + '</span>');
                }
            }
        }
        
        // Run on page load
        simplifyShippingDisplay();
        
        // Run after cart updates
        $(document.body).on('updated_cart_totals', function() {
            simplifyShippingDisplay();
        });
    });
    </script>
    <?php
}
add_action("wp_footer", "aaapos_simplify_cart_shipping_display");

/**
 * Enqueue Quick View Assets (FIXED - Separate function)
 */
function aaapos_enqueue_quick_view_assets()
{
    // Only load on shop/archive pages AND if enabled in customizer
    if (!get_theme_mod("show_quick_view", true)) {
        return;
    }

    if (
        !is_shop() &&
        !is_product_category() &&
        !is_product_tag() &&
        !is_search() &&
        !is_cart()
    ) {
        return;
    }

    // Quick View Button CSS - Check if file exists first
    $quick_view_button_css =
        get_template_directory() . "/assets/css/quick-view-button.css";
    if (file_exists($quick_view_button_css)) {
        wp_enqueue_style(
            "aaapos-quick-view-button",
            get_template_directory_uri() . "/assets/css/quick-view-button.css",
            ["aaapos-woocommerce-base"],
            AAAPOS_VERSION . "." . time(),
            "all",
        );
    }

    // Quick View Modal CSS
    $quick_view_css = get_template_directory() . "/assets/css/quick-view.css";
    if (file_exists($quick_view_css)) {
        wp_enqueue_style(
            "aaapos-quick-view",
            get_template_directory_uri() . "/assets/css/quick-view.css",
            ["aaapos-woocommerce-base"],
            AAAPOS_VERSION,
            "all",
        );
    }

    // Quick View JS - with proper dependencies
    $quick_view_js = get_template_directory() . "/assets/js/quick-view.js";
    if (file_exists($quick_view_js)) {
        wp_enqueue_script(
            "aaapos-quick-view-js",
            get_template_directory_uri() . "/assets/js/quick-view.js",
            ["jquery", "wc-add-to-cart-variation"],
            AAAPOS_VERSION,
            true,
        );

        // Localize script with AJAX data
        wp_localize_script("aaapos-quick-view-js", "aaaposQuickView", [
            "ajax_url" => admin_url("admin-ajax.php"),
            "nonce" => wp_create_nonce("woocommerce-cart"),
        ]);
    }
}
add_action("wp_enqueue_scripts", "aaapos_enqueue_quick_view_assets", 1000);

/**
 * Enqueue Search Results Assets
 */
function aaapos_enqueue_search_assets()
{
    if (!is_search()) {
        return;
    }

    // Search results CSS
    wp_enqueue_style(
        "aaapos-search-results",
        get_template_directory_uri() .
            "/assets/css/components/search-results.css",
        ["aaapos-woocommerce-base"],
        AAAPOS_VERSION,
        "all",
    );

    // Search results JS (sorting & view toggle)
    wp_enqueue_script(
        "aaapos-search-results-js",
        get_template_directory_uri() . "/assets/js/search-results.js",
        ["jquery"],
        AAAPOS_VERSION,
        true,
    );
}
add_action("wp_enqueue_scripts", "aaapos_enqueue_search_assets", 1001);

/**
 * Add Critical Inline CSS - UPDATED TO USE CUSTOMIZER COLUMNS
 */
function aaapos_woocommerce_inline_critical_css()
{
    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_search()) {
        return;
    }

    // Get products per row from customizer (default: 3)
    $columns = absint(get_theme_mod("products_per_row", 3));
    ?>
    <style id="woocommerce-critical-fix">
        /* CRITICAL IMMEDIATE FIXES - Applied instantly */
        .woocommerce ul.products,
        .woocommerce-page ul.products,
        ul.products,
        ul.products.columns-3 {
            display: grid !important;
            grid-template-columns: repeat(1, 1fr) !important;
            gap: 1.5rem !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        @media (min-width: 640px) {
            .woocommerce ul.products,
            .woocommerce-page ul.products,
            ul.products,
            ul.products.columns-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (min-width: 1024px) {
            .woocommerce ul.products,
            .woocommerce-page ul.products,
            ul.products,
            ul.products.columns-3 {
                grid-template-columns: repeat(<?php echo esc_attr(
                    $columns,
                ); ?>, 1fr) !important;
            }
        }
        
        .woocommerce ul.products li.product,
        .woocommerce-page ul.products li.product,
        ul.products li.product {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            float: none !important;
            clear: none !important;
        }
        
        /* Single Product - Critical fix for quantity */
        .single-product .quantity {
            display: inline-flex !important;
            align-items: center !important;
        }
        
        .single-product .quantity input.qty {
            width: 70px !important;
            text-align: center !important;
            border: none !important;
        }
    </style>
    <?php
}
add_action("wp_head", "aaapos_woocommerce_inline_critical_css", 999);

/**
 * Disable WooCommerce's Default Conflicting Styles
 */
add_filter("woocommerce_enqueue_styles", function ($styles) {
    // Remove default WooCommerce general styles that add grid conflicts
    if (isset($styles["woocommerce-general"])) {
        unset($styles["woocommerce-general"]);
    }
    return $styles;
});

/**
 * Force Remove WooCommerce's Inline Grid CSS
 */
function aaapos_remove_woo_inline_css()
{
    if (is_woocommerce() || is_search()) {
        wp_add_inline_style(
            "woocommerce-inline",
            '
            .woocommerce ul.products[class*="columns-"] li.product {
                width: auto !important;
                float: none !important;
                margin-right: 0 !important;
            }
        ',
        );
    }
}
add_action("wp_enqueue_scripts", "aaapos_remove_woo_inline_css", 9999);

/**
 * Remove Default WooCommerce Wrappers
 */
remove_action(
    "woocommerce_before_main_content",
    "woocommerce_output_content_wrapper",
    10,
);
remove_action(
    "woocommerce_after_main_content",
    "woocommerce_output_content_wrapper_end",
    10,
);

/**
 * Remove Default Sidebar
 */
remove_action("woocommerce_sidebar", "woocommerce_get_sidebar", 10);

/**
 * Products Per Page - USES CUSTOMIZER SETTING
 */
function aaapos_products_per_page()
{
    return absint(get_theme_mod("products_per_page", 12));
}
add_filter("loop_shop_per_page", "aaapos_products_per_page", 20);

/**
 * Products Per Row - USES CUSTOMIZER SETTING (FIXED!)
 */
function aaapos_products_per_row()
{
    return absint(get_theme_mod("products_per_row", 3));
}
add_filter("loop_shop_columns", "aaapos_products_per_row");

/**
 * Related Products Configuration - USES CUSTOMIZER SETTING
 */
function aaapos_related_products_args($args)
{
    $related_count = absint(get_theme_mod("related_products_count", 4));

    $args["posts_per_page"] = $related_count;
    $args["columns"] = min($related_count, 4); // Max 4 columns

    return $args;
}
add_filter(
    "woocommerce_output_related_products_args",
    "aaapos_related_products_args",
);

/**
 * Custom Sale Badge Text - USES CUSTOMIZER SETTING (NEW!)
 */
function aaapos_custom_sale_flash($html, $post, $product)
{
    $sale_text = get_theme_mod("sale_badge_text", __("Sale", "aaapos-prime"));
    return '<span class="onsale">' . esc_html($sale_text) . "</span>";
}
add_filter("woocommerce_sale_flash", "aaapos_custom_sale_flash", 10, 3);

/**
 * Custom Image Sizes for WooCommerce
 */
function aaapos_woocommerce_image_sizes()
{
    add_image_size("woocommerce_thumbnail", 400, 400, true);
    add_image_size("woocommerce_single", 800, 800, true);
    add_image_size("woocommerce_gallery_thumbnail", 150, 150, true);
}
add_action("after_setup_theme", "aaapos_woocommerce_image_sizes", 11);

/**
 * Set WooCommerce Default Image Dimensions
 */
function aaapos_woocommerce_theme_image_dimensions()
{
    $catalog = [
        "width" => "400",
        "height" => "400",
        "crop" => 1,
    ];

    $single = [
        "width" => "800",
        "height" => "800",
        "crop" => 1,
    ];

    $thumbnail = [
        "width" => "150",
        "height" => "150",
        "crop" => 1,
    ];

    update_option("shop_catalog_image_size", $catalog);
    update_option("shop_single_image_size", $single);
    update_option("shop_thumbnail_image_size", $thumbnail);
}
add_action("after_switch_theme", "aaapos_woocommerce_theme_image_dimensions");

/**
 * Modify Product Gallery Classes
 */
function aaapos_product_gallery_classes($classes)
{
    $classes[] = "woocommerce-product-gallery--custom";
    return $classes;
}
add_filter(
    "woocommerce_single_product_image_gallery_classes",
    "aaapos_product_gallery_classes",
);

/**
 * Add Body Classes for WooCommerce Pages
 */
function aaapos_woo_body_classes($classes)
{
    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = "aaapos-shop-page";
        $classes[] = "woocommerce-shop";

        // Add sidebar class if enabled
        if (
            get_theme_mod("show_shop_sidebar", false) &&
            is_active_sidebar("shop-sidebar")
        ) {
            $classes[] = "has-shop-sidebar";
        }
    }

    if (is_product()) {
        $classes[] = "single-product";
        $classes[] = "woocommerce-product-page";
    }

    if (is_account_page()) {
        $classes[] = "woocommerce-account";
    }

    return $classes;
}
add_filter("body_class", "aaapos_woo_body_classes");

/**
 * Customize My Account Menu Order
 */
function aaapos_custom_my_account_menu_order()
{
    return [
        "dashboard" => __("Dashboard", "woocommerce"),
        "orders" => __("Orders", "woocommerce"),
        "downloads" => __("Downloads", "woocommerce"),
        "edit-address" => __("Addresses", "woocommerce"),
        "payment-methods" => __("Payment methods", "woocommerce"),
        "edit-account" => __("Account details", "woocommerce"),
        "customer-logout" => __("Logout", "woocommerce"),
    ];
}
add_filter(
    "woocommerce_account_menu_items",
    "aaapos_custom_my_account_menu_order",
);

/**
 * Custom Dashboard Content with Grid Cards
 */
function aaapos_custom_dashboard_content()
{
    $current_user = wp_get_current_user();
    $display_name = !empty($current_user->first_name)
        ? $current_user->first_name
        : $current_user->display_name;
    ?>
    <div class="woocommerce-MyAccount-dashboard-intro">
        <h2 class="dashboard-greeting">
            <?php if (
                file_exists(
                    get_template_directory() .
                        "/assets/images/icons/profile-icon.png",
                )
            ): ?>
                <img src="<?php echo esc_url(
                    get_template_directory_uri() .
                        "/assets/images/icons/profile-icon.png",
                ); ?>" 
                     alt="Profile" class="greeting-icon">
            <?php endif; ?>
            Hello <span class="greeting-name"><?php echo esc_html(
                $display_name,
            ); ?></span>
        </h2>
    </div>

    <div class="woocommerce-MyAccount-dashboard-grid">
        
        <a href="<?php echo esc_url(
            wc_get_account_endpoint_url("orders"),
        ); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e(
                "Orders",
                "aaapos-prime",
            ); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e(
                "View your order history",
                "aaapos-prime",
            ); ?></p>
        </a>

        <a href="<?php echo esc_url(
            wc_get_account_endpoint_url("downloads"),
        ); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e(
                "Downloads",
                "aaapos-prime",
            ); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e(
                "Access your downloads",
                "aaapos-prime",
            ); ?></p>
        </a>

        <a href="<?php echo esc_url(
            wc_get_account_endpoint_url("edit-address"),
        ); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e(
                "Addresses",
                "aaapos-prime",
            ); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e(
                "Manage billing & shipping",
                "aaapos-prime",
            ); ?></p>
        </a>

        <a href="<?php echo esc_url(
            wc_get_account_endpoint_url("edit-account"),
        ); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e(
                "Account Details",
                "aaapos-prime",
            ); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e(
                "Update your information",
                "aaapos-prime",
            ); ?></p>
        </a>

        <a href="<?php echo esc_url(
            wc_get_account_endpoint_url("payment-methods"),
        ); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e(
                "Payment Methods",
                "aaapos-prime",
            ); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e(
                "Manage saved payment cards",
                "aaapos-prime",
            ); ?></p>
        </a>

        <?php if (get_page_by_path("contact")): ?>
            <a href="<?php echo esc_url(
                home_url("/contact"),
            ); ?>" class="dashboard-card">
                <div class="dashboard-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="dashboard-card__title"><?php esc_html_e(
                    "Support",
                    "aaapos-prime",
                ); ?></h3>
                <p class="dashboard-card__description"><?php esc_html_e(
                    "Get help & contact us",
                    "aaapos-prime",
                ); ?></p>
            </a>
        <?php endif; ?>

    </div>
    <?php
}
remove_action(
    "woocommerce_account_dashboard",
    "woocommerce_account_dashboard",
    10,
);
add_action(
    "woocommerce_account_dashboard",
    "aaapos_custom_dashboard_content",
    10,
);

/**
 * Register Shop Sidebar Widget Area
 */
function aaapos_register_shop_sidebar()
{
    register_sidebar([
        "name" => __("Shop Sidebar", "aaapos-prime"),
        "id" => "shop-sidebar",
        "description" => __(
            "Widgets in this area will be shown on the shop pages.",
            "aaapos-prime",
        ),
        "before_widget" => '<div id="%1$s" class="widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h3 class="widget-title">',
        "after_title" => "</h3>",
    ]);
}
add_action("widgets_init", "aaapos_register_shop_sidebar");

/**
 * Add Data Attributes to Order Table for Responsive Design
 */
function aaapos_add_data_title_to_order_table()
{
    if (!is_account_page()) {
        return;
    } ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.woocommerce-orders-table thead th').each(function(index) {
                var title = $(this).text();
                $('.woocommerce-orders-table tbody tr').each(function() {
                    $(this).find('td').eq(index).attr('data-title', title);
                });
            });
        });
    </script>
    <?php
}
add_action(
    "woocommerce_account_orders_endpoint",
    "aaapos_add_data_title_to_order_table",
);

/**
 * Update Cart Icon Count via AJAX
 * Used by header cart to update dynamically
 */
function aaapos_update_cart_count()
{
    $cart_style = get_theme_mod("cart_icon_style", "icon-count");

    $response = [
        "count" => WC()->cart->get_cart_contents_count(),
        "style" => $cart_style,
    ];

    if ($cart_style === "icon-total") {
        $response["total"] = WC()->cart->get_cart_subtotal();
    }

    wp_send_json_success($response);
}
add_action("wp_ajax_update_cart_count", "aaapos_update_cart_count");
add_action("wp_ajax_nopriv_update_cart_count", "aaapos_update_cart_count");

/**
 * =========================================================================
 * SEARCH RESULTS PRODUCT SORTING
 * Modify search query to support WooCommerce product sorting
 * =========================================================================
 */

/**
 * Modify Search Query for Product Sorting
 */
function aaapos_search_product_sorting($query)
{
    // Only on search results page, main query, not admin
    if (!is_search() || !$query->is_main_query() || is_admin()) {
        return;
    }

    // Only if searching products
    if (
        !isset($query->query_vars["post_type"]) ||
        $query->query_vars["post_type"] !== "product"
    ) {
        // Check if any products are in results
        $has_products = false;
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                if (get_post_type() === "product") {
                    $has_products = true;
                    break;
                }
            }
            wp_reset_postdata();
        }

        if (!$has_products) {
            return;
        }
    }

    // Get orderby parameter
    $orderby = isset($_GET["orderby"])
        ? wc_clean($_GET["orderby"])
        : "menu_order";

    // Apply sorting
    switch ($orderby) {
        case "popularity":
            $query->set("meta_key", "total_sales");
            $query->set("orderby", "meta_value_num");
            $query->set("order", "DESC");
            break;

        case "rating":
            $query->set("meta_key", "_wc_average_rating");
            $query->set("orderby", "meta_value_num");
            $query->set("order", "DESC");
            break;

        case "date":
            $query->set("orderby", "date");
            $query->set("order", "DESC");
            break;

        case "price":
            $query->set("meta_key", "_price");
            $query->set("orderby", "meta_value_num");
            $query->set("order", "ASC");
            break;

        case "price-desc":
            $query->set("meta_key", "_price");
            $query->set("orderby", "meta_value_num");
            $query->set("order", "DESC");
            break;

        case "menu_order":
        default:
            $query->set("orderby", "menu_order title");
            $query->set("order", "ASC");
            break;
    }
}
add_action("pre_get_posts", "aaapos_search_product_sorting", 20);

/**
 * Add WooCommerce Product Meta to Search Results
 * Ensures product data is available for sorting
 */
function aaapos_search_product_meta($query)
{
    if (!is_search() || !$query->is_main_query() || is_admin()) {
        return;
    }

    // Include product post type in search
    $post_types = $query->get("post_type");

    if (empty($post_types)) {
        $query->set("post_type", ["post", "page", "product"]);
    } elseif (is_array($post_types) && !in_array("product", $post_types)) {
        $post_types[] = "product";
        $query->set("post_type", $post_types);
    }
}
add_action("pre_get_posts", "aaapos_search_product_meta", 10);

/**
 * =============================================================================
 * CHECKOUT PROGRESS STEPS
 * Display a visual progress indicator on cart, checkout, and order received pages
 * =============================================================================
 */

/**
 * Display Checkout Progress Steps
 * Shows current step in the checkout process
 */
function aaapos_checkout_progress_steps()
{
    // Determine current step
    $current_step = 1; // Default to step 1 (cart)

    if (is_checkout() && !is_order_received_page()) {
        $current_step = 2; // Checkout page
    } elseif (is_order_received_page()) {
        $current_step = 3; // Order complete page
    } elseif (is_cart()) {
        $current_step = 1; // Cart page
    }

    // Steps data
    $steps = [
        1 => [
            "number" => "1",
            "label" => __("Shopping cart", "macedon-ranges"),
            "url" => wc_get_cart_url(),
        ],
        2 => [
            "number" => "2",
            "label" => __("Checkout details", "macedon-ranges"),
            "url" => wc_get_checkout_url(),
        ],
        3 => [
            "number" => "3",
            "label" => __("Order complete", "macedon-ranges"),
            "url" => "", // No link for this step
        ],
    ];
    ?>
    <div class="woocommerce-checkout-progress">
        <div class="checkout-steps">
            <?php foreach ($steps as $step_num => $step): ?>
                <?php
                $step_class = "checkout-step";

                // Add active class for current step
                if ($step_num === $current_step) {
                    $step_class .= " active";
                }

                // Add completed class for steps before current
                if ($step_num < $current_step) {
                    $step_class .= " completed";
                }

                // Add clickable class if step has URL and is completed
                $is_clickable =
                    !empty($step["url"]) && $step_num < $current_step;
                if ($is_clickable) {
                    $step_class .= " clickable";
                }
                ?>
                
                <?php if ($is_clickable): ?>
                    <a href="<?php echo esc_url(
                        $step["url"],
                    ); ?>" class="<?php echo esc_attr($step_class); ?>">
                <?php else: ?>
                    <div class="<?php echo esc_attr($step_class); ?>">
                <?php endif; ?>
                
                    <span class="step-number"><?php echo esc_html(
                        $step["number"],
                    ); ?></span>
                    <span class="step-label"><?php echo esc_html(
                        $step["label"],
                    ); ?></span>
                
                <?php if ($is_clickable): ?>
                    </a>
                <?php else: ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($step_num < count($steps)): ?>
                    <svg class="step-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?php endif; ?>
                
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Add progress steps to cart page
 */
function aaapos_add_progress_to_cart()
{
    if (is_cart() && !WC()->cart->is_empty()) {
        aaapos_checkout_progress_steps();
    }
}
add_action("woocommerce_before_cart", "aaapos_add_progress_to_cart", 5);

/**
 * Add progress steps to checkout page
 */
function aaapos_add_progress_to_checkout()
{
    if (is_checkout() && !is_order_received_page()) {
        aaapos_checkout_progress_steps();
    }
}
add_action(
    "woocommerce_before_checkout_form",
    "aaapos_add_progress_to_checkout",
    5,
);

/**
 * Add progress steps to order received page
 */
function aaapos_add_progress_to_order_received()
{
    if (is_order_received_page()) {
        aaapos_checkout_progress_steps();
    }
}
add_action(
    "woocommerce_before_thankyou",
    "aaapos_add_progress_to_order_received",
    5,
);

/**
 * Display suggested products on cart page
 * UPDATED: Uses wrapper for full-width display without breaking layout
 */
function aaapos_cart_suggested_products()
{
    if (!is_cart()) {
        return;
    }

    // Get products from cart to find related products
    $cart_items = WC()->cart->get_cart();

    if (empty($cart_items)) {
        return;
    }

    // Collect category IDs from cart products
    $category_ids = [];
    $product_ids_in_cart = [];

    foreach ($cart_items as $cart_item) {
        $product_id = $cart_item["product_id"];
        $product_ids_in_cart[] = $product_id;

        // Get product categories
        $terms = get_the_terms($product_id, "product_cat");
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $category_ids[] = $term->term_id;
            }
        }
    }

    // Remove duplicates
    $category_ids = array_unique($category_ids);

    if (empty($category_ids)) {
        return;
    }

    // Query for related products
    $args = [
        "post_type" => "product",
        "posts_per_page" => 4,
        "post__not_in" => $product_ids_in_cart,
        "orderby" => "rand",
        "post_status" => "publish",
        "tax_query" => [
            [
                "taxonomy" => "product_cat",
                "field" => "term_id",
                "terms" => $category_ids,
                "operator" => "IN",
            ],
        ],
    ];

    $suggested_products = new WP_Query($args);

    if (!$suggested_products->have_posts()) {
        wp_reset_postdata();
        return;
    }

    // Get customizer settings
    $show_rating = get_theme_mod("show_product_rating", true);
    $sale_badge_text = get_theme_mod(
        "sale_badge_text",
        __("Sale", "macedon-ranges"),
    );
    $show_quick_view = get_theme_mod("show_quick_view", true);
    ?>
    
    <!-- Suggested Products Section with Wrapper -->
    <div class="cart-suggested-products-wrapper">
        <div class="cart-suggested-products">
            <div class="cart-suggested-products__header">
                <h2 class="cart-suggested-products__title"><?php esc_html_e(
                    "You May Also Like",
                    "macedon-ranges",
                ); ?></h2>
                <p class="cart-suggested-products__subtitle"><?php esc_html_e(
                    "Customers who bought these items also bought",
                    "macedon-ranges",
                ); ?></p>
            </div>
            
            <!-- Use WooCommerce standard structure with products class -->
            <ul class="products columns-4 cart-suggested-products__grid">
                <?php while ($suggested_products->have_posts()):

                    $suggested_products->the_post();
                    global $product;

                    // Get rating data
                    $average_rating = $product->get_average_rating();
                    $rating_count = $product->get_rating_count();
                    ?>
                    
                    <li <?php wc_product_class("", $product); ?>>
                        
                        <!-- Product Image Link (Image + Badge ONLY) -->
                        <a href="<?php echo esc_url(
                            $product->get_permalink(),
                        ); ?>" class="woocommerce-LoopProduct-link">
                            
                            <!-- Product Image -->
                            <?php echo $product->get_image(
                                "woocommerce_thumbnail",
                            ); ?>
                            
                            <!-- Sale Badge with Custom Text -->
                            <?php if ($product->is_on_sale()): ?>
                                <span class="onsale"><?php echo esc_html(
                                    $sale_badge_text,
                                ); ?></span>
                            <?php endif; ?>
                            
                        </a>
                        
                        <!-- Product Info Container (Outside image link) -->
                        <div class="product-info">
                            
                            <!-- Product Title with Link -->
                            <h2 class="woocommerce-loop-product__title">
                                <a href="<?php echo esc_url(
                                    $product->get_permalink(),
                                ); ?>">
                                    <?php echo esc_html(
                                        $product->get_name(),
                                    ); ?>
                                </a>
                            </h2>
                            
                            <!-- Star Rating Section (Conditional based on Customizer) -->
                            <?php if ($show_rating && $average_rating > 0): ?>
                                <div class="product-rating">
                                    <div class="rating-stars" aria-label="<?php echo esc_attr(
                                        sprintf(
                                            __(
                                                "Rated %s out of 5",
                                                "macedon-ranges",
                                            ),
                                            number_format($average_rating, 2),
                                        ),
                                    ); ?>">
                                        <?php
                                        // Generate unique ID for gradient
                                        $gradient_id =
                                            "half-fill-" . $product->get_id();

                                        // Display 5 stars
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($average_rating)) {
                                                // Full star
                                                echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            } elseif (
                                                $i == ceil($average_rating) &&
                                                $average_rating -
                                                    floor($average_rating) >=
                                                    0.5
                                            ) {
                                                // Half star
                                                echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><defs><linearGradient id="' .
                                                    esc_attr($gradient_id) .
                                                    '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' .
                                                    esc_attr($gradient_id) .
                                                    ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            } else {
                                                // Empty star
                                                echo '<svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#d1d5db"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if ($rating_count > 0): ?>
                                        <span class="rating-count">(<?php echo esc_html(
                                            $rating_count,
                                        ); ?>)</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Price -->
                            <div class="product-price-wrapper">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            
                        </div>
                        
                       <!-- Quick View Button (Conditional based on Customizer) -->
<?php if ($show_quick_view): ?>
    <button type="button" 
            class="quick-view-button" 
            data-product-id="<?php echo esc_attr($product->get_id()); ?>"
            aria-label="<?php echo esc_attr(
                sprintf(
                    __("Quick view %s", "macedon-ranges"),
                    $product->get_name(),
                ),
            ); ?>"
            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; color: #374151; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; width: 100%; margin-bottom: 0.875rem; line-height: 1; background: transparent; border: none;"
            onmouseover="this.style.color='var(--brand-color, #0ea5e9)'; this.style.transform='translateY(-2px)';"
            onmouseout="this.style.color='#374151'; this.style.transform='translateY(0)';">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span><?php esc_html_e("Quick View", "macedon-ranges"); ?></span>
    </button>
<?php endif; ?>
                        
                        <!-- Add to Cart Button with Icon -->
<?php if ($product->is_type("variable")): ?>
    <a href="<?php echo esc_url($product->get_permalink()); ?>" 
       class="button product_type_variable"
       style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span><?php esc_html_e("Select options", "macedon-ranges"); ?></span>
    </a>
<?php else: ?>
    <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>" 
       data-quantity="1" 
       class="button product_type_simple add_to_cart_button ajax_add_to_cart" 
       data-product_id="<?php echo esc_attr($product->get_id()); ?>" 
       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
       aria-label="<?php echo esc_attr(
           sprintf(
               __('Add "%s" to your cart', "macedon-ranges"),
               $product->get_name(),
           ),
       ); ?>" 
       rel="nofollow"
       style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span><?php echo esc_html($product->add_to_cart_text()); ?></span>
    </a>
<?php endif; ?>
                        
                    </li>
                    
                <?php
                endwhile; ?>
            </ul>
        </div>
    </div>
    
    <?php wp_reset_postdata();
}
add_action("woocommerce_after_cart", "aaapos_cart_suggested_products", 20);
