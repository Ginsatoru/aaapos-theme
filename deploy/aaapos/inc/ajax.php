<?php
/**
 * AJAX handlers
 * UPDATED: Fixed quick view to properly support variable products with variations
 */

/**
 * CRITICAL FIX: Redirect after add-to-cart on single product page
 * This prevents the "confirm form resubmission" browser warning
 */
add_action('template_redirect', 'aaapos_redirect_after_add_to_cart');
function aaapos_redirect_after_add_to_cart() {
    // Only on single product pages
    if (!is_product()) {
        return;
    }
    
    // Check if add-to-cart was just processed
    if (isset($_POST['add-to-cart']) && is_numeric($_POST['add-to-cart'])) {
        $product_id = absint($_POST['add-to-cart']);
        
        // Get the current product URL
        $redirect_url = get_permalink($product_id);
        
        // Add success parameter to show notification
        $redirect_url = add_query_arg('added-to-cart', $product_id, $redirect_url);
        
        // Perform redirect (PRG pattern)
        wp_safe_redirect($redirect_url);
        exit;
    }
}



/**
 * Get Quick View Product Content - FIXED VERSION
 * Now properly validates nonce and supports both simple and variable products
 */
function aaapos_get_quick_view_product()
{
    // FIXED: Security check with correct nonce name
    check_ajax_referer('aaapos_quick_view_nonce', 'security');

    $product_id = isset($_POST["product_id"])
        ? absint($_POST["product_id"])
        : 0;

    if (!$product_id) {
        wp_send_json_error(["message" => "Invalid product ID"]);
    }

    global $post, $product;

    $post = get_post($product_id);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_send_json_error(["message" => "Product not found"]);
    }

    // Setup postdata for proper WooCommerce context
    setup_postdata($post);

    // Start output buffering
    ob_start();
    ?>
    
    <div class="quick-view-product">
        
        <!-- Product Gallery -->
        <div class="quick-view-product-gallery">
            <?php if ($product->get_image_id()) {
                echo wp_get_attachment_image(
                    $product->get_image_id(),
                    "woocommerce_single",
                    false,
                    ["class" => "quick-view-image"],
                );
            } else {
                echo wc_placeholder_img("woocommerce_single");
            } ?>
        </div>
        
        <!-- Product Info -->
        <div class="quick-view-product-info">
            
            <!-- Product Title -->
            <h2 class="product_title entry-title"><?php echo esc_html(
                $product->get_name(),
            ); ?></h2>
            
            <!-- Price -->
            <div class="price-wrapper">
                <?php echo $product->get_price_html(); ?>
            </div>
            
            <!-- Rating -->
<?php if (
    get_theme_mod("show_product_rating", true) &&
    $product->get_average_rating() > 0
):

    $average_rating = $product->get_average_rating();
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();
    $gradient_id = "half-fill-qv-" . $product->get_id();
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
endif; ?>
            
            <!-- Short Description -->
            <?php if ($product->get_short_description()): ?>
                <div class="woocommerce-product-details__short-description">
                    <?php echo wp_kses_post(
                        $product->get_short_description(),
                    ); ?>
                </div>
            <?php endif; ?>
            
            <!-- Add to Cart Form - FIXED: Now properly handles variations -->
            <div class="quick-view-add-to-cart">
                <?php 
                // For variable products, we need to include the variation form
                if ($product->is_type('variable')) {
                    // Load the variation form template
                    wc_get_template(
                        'single-product/add-to-cart/variable.php',
                        array(
                            'available_variations' => $product->get_available_variations(),
                            'attributes'           => $product->get_variation_attributes(),
                            'selected_attributes'  => $product->get_default_attributes()
                        )
                    );
                } else {
                    // For simple products, use the standard template
                    woocommerce_template_single_add_to_cart();
                }
                ?>
            </div>
            
            <!-- View Full Details Link -->
            <a href="<?php echo esc_url(
                $product->get_permalink(),
            ); ?>" class="quick-view-full-details">
                <?php esc_html_e("View Full Details", "aaapos-prime"); ?>
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>
            
        </div>
        
    </div>
    
    <?php
    $html = ob_get_clean();
    
    // Reset postdata
    wp_reset_postdata();

    wp_send_json_success(["html" => $html]);
}
add_action("wp_ajax_get_quick_view_product", "aaapos_get_quick_view_product");
add_action(
    "wp_ajax_nopriv_get_quick_view_product",
    "aaapos_get_quick_view_product",
);

// Newsletter subscription
function mr_newsletter_subscribe()
{
    // Verify nonce
    if (
        !isset($_POST["nonce"]) ||
        !wp_verify_nonce($_POST["nonce"], "mr_nonce")
    ) {
        wp_send_json_error("Invalid nonce");
    }

    $email = sanitize_email($_POST["email"]);
    $gdpr = isset($_POST["gdpr"]) ? true : false;

    if (!is_email($email)) {
        wp_send_json_error("Please enter a valid email address.");
    }

    if (!$gdpr && get_theme_mod("newsletter_gdpr", true)) {
        wp_send_json_error("Please accept the terms and conditions.");
    }

    // Here you would typically integrate with a newsletter service
    // For now, we'll just return success
    wp_send_json_success("Thank you for subscribing!");
}
add_action("wp_ajax_mr_newsletter_subscribe", "mr_newsletter_subscribe");
add_action("wp_ajax_nopriv_mr_newsletter_subscribe", "mr_newsletter_subscribe");

// Product search AJAX
function mr_product_search()
{
    $search_term = isset($_GET["s"]) ? sanitize_text_field($_GET["s"]) : "";

    $args = [
        "post_type" => "product",
        "posts_per_page" => 8,
        "s" => $search_term,
        "post_status" => "publish",
    ];

    $search_query = new WP_Query($args);

    ob_start();

    if ($search_query->have_posts()) {
        echo '<div class="search-results">';
        while ($search_query->have_posts()) {

            $search_query->the_post();
            global $product;
            ?>
            <div class="search-result-item">
                <a href="<?php the_permalink(); ?>" class="search-result-link">
                    <div class="search-result-image">
                        <?php echo $product->get_image("thumbnail"); ?>
                    </div>
                    <div class="search-result-content">
                        <h4><?php the_title(); ?></h4>
                        <div class="price"><?php echo $product->get_price_html(); ?></div>
                    </div>
                </a>
            </div>
            <?php
        }
        echo "</div>";
    } else {
        echo '<p class="no-results">' .
            esc_html__("No products found.", "macedon-ranges") .
            "</p>";
    }

    wp_reset_postdata();

    $results = ob_get_clean();
    wp_send_json_success($results);
}
add_action("wp_ajax_mr_product_search", "mr_product_search");
add_action("wp_ajax_nopriv_mr_product_search", "mr_product_search");

// ============================================================================
// CART DROPDOWN AJAX HANDLERS
// ============================================================================

/**
 * AJAX Handler: Remove item from cart
 */
function mr_ajax_remove_cart_item()
{
    // Verify nonce - accept both nonce names for compatibility
    $nonce = isset($_POST["nonce"]) ? $_POST["nonce"] : "";
    $nonce_valid =
        wp_verify_nonce($nonce, "mr_cart_nonce") ||
        wp_verify_nonce($nonce, "mr_nonce");

    if (!$nonce_valid) {
        wp_send_json_error([
            "message" => __("Security check failed", "macedon-ranges"),
        ]);
    }

    // Check if WooCommerce is active
    if (!class_exists("WooCommerce")) {
        wp_send_json_error([
            "message" => __("WooCommerce is not active", "macedon-ranges"),
        ]);
    }

    // Get cart item key
    $cart_item_key = isset($_POST["cart_item_key"])
        ? sanitize_text_field($_POST["cart_item_key"])
        : "";

    if (empty($cart_item_key)) {
        wp_send_json_error([
            "message" => __("Invalid cart item", "macedon-ranges"),
        ]);
    }

    // Remove item from cart
    $removed = WC()->cart->remove_cart_item($cart_item_key);

    if ($removed) {
        // Calculate new totals
        WC()->cart->calculate_totals();

        // Get updated fragments
        $fragments = apply_filters("woocommerce_add_to_cart_fragments", []);

        wp_send_json_success([
            "message" => __("Item removed from cart", "macedon-ranges"),
            "cart_count" => WC()->cart->get_cart_contents_count(),
            "cart_subtotal" => WC()->cart->get_cart_subtotal(),
            "cart_total" => WC()->cart->get_total(),
            "fragments" => $fragments,
        ]);
    } else {
        wp_send_json_error([
            "message" => __(
                "Failed to remove item from cart",
                "macedon-ranges",
            ),
        ]);
    }
}

// Register AJAX actions for both logged in and logged out users
add_action("wp_ajax_remove_cart_item", "mr_ajax_remove_cart_item");
add_action("wp_ajax_nopriv_remove_cart_item", "mr_ajax_remove_cart_item");

/**
 * AJAX Handler: Update cart item quantity
 * FIXED: Now returns line subtotal and cart totals HTML
 */
function mr_ajax_update_cart_quantity()
{
    // Verify nonce - accept both nonce names for compatibility
    $nonce = isset($_POST["nonce"]) ? $_POST["nonce"] : "";
    $nonce_valid =
        wp_verify_nonce($nonce, "mr_cart_nonce") ||
        wp_verify_nonce($nonce, "mr_nonce");

    if (!$nonce_valid) {
        wp_send_json_error([
            "message" => __("Security check failed", "macedon-ranges"),
        ]);
    }

    // Check if WooCommerce is active
    if (!class_exists("WooCommerce")) {
        wp_send_json_error([
            "message" => __("WooCommerce is not active", "macedon-ranges"),
        ]);
    }

    $cart_item_key = isset($_POST["cart_item_key"])
        ? sanitize_text_field($_POST["cart_item_key"])
        : "";
    $quantity = isset($_POST["quantity"]) ? absint($_POST["quantity"]) : 1;

    if (empty($cart_item_key)) {
        wp_send_json_error([
            "message" => __("Invalid cart item", "macedon-ranges"),
        ]);
    }

    // Get cart item before update to access product
    $cart = WC()->cart->get_cart();
    if (!isset($cart[$cart_item_key])) {
        wp_send_json_error([
            "message" => __("Cart item not found", "macedon-ranges"),
        ]);
    }

    $cart_item = $cart[$cart_item_key];
    $_product = $cart_item['data'];

    // Update quantity
    $updated = WC()->cart->set_quantity($cart_item_key, $quantity);

    if ($updated) {
        WC()->cart->calculate_totals();

        // Get the updated line subtotal for this specific item
        $updated_cart = WC()->cart->get_cart();
        $updated_item = $updated_cart[$cart_item_key];
        $line_subtotal = WC()->cart->get_product_subtotal($_product, $updated_item['quantity']);

        // Get updated cart totals HTML
        ob_start();
        woocommerce_cart_totals();
        $cart_totals_html = ob_get_clean();

        // Get updated fragments
        $fragments = apply_filters("woocommerce_add_to_cart_fragments", []);

        wp_send_json_success([
            "message" => __("Cart updated", "macedon-ranges"),
            "cart_count" => WC()->cart->get_cart_contents_count(),
            "cart_subtotal" => WC()->cart->get_cart_subtotal(),
            "cart_total" => WC()->cart->get_total(),
            "line_subtotal" => $line_subtotal,
            "cart_totals_html" => $cart_totals_html,
            "fragments" => $fragments,
        ]);
    } else {
        wp_send_json_error([
            "message" => __("Failed to update cart", "macedon-ranges"),
        ]);
    }
}

add_action("wp_ajax_update_cart_quantity", "mr_ajax_update_cart_quantity");
add_action(
    "wp_ajax_nopriv_update_cart_quantity",
    "mr_ajax_update_cart_quantity",
);

/**
 * Refresh cart fragments on add to cart (WooCommerce hook)
 */
function mr_refresh_cart_fragments($fragments)
{
    $cart_count = WC()->cart->get_cart_contents_count();

    // Cart count fragment - always return the element (shown/hidden via CSS or JS)
    ob_start();
    ?>
    <span class="cart-count"<?php echo $cart_count === 0
        ? ' style="display:none;"'
        : ""; ?>><?php echo esc_html($cart_count); ?></span>
    <?php
    $fragments[".cart-count"] = ob_get_clean();

    // Cart dropdown header count
    ob_start();
    ?>
    <span class="cart-item-count"><?php echo esc_html(
        $cart_count,
    ); ?> <?php echo $cart_count === 1 ? esc_html__("item", "macedon-ranges") : esc_html__("items", "macedon-ranges"); ?></span>
    <?php
    $fragments[".cart-item-count"] = ob_get_clean();

    // Cart subtotal fragment
    ob_start();
    ?>
    <strong class="cart-subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
    <?php
    $fragments[".cart-subtotal-amount"] = ob_get_clean();

    // Full cart dropdown fragment for complete refresh
    // Return only the inner HTML, not wrapped in UL
    ob_start();
    mr_render_cart_dropdown_items();
    $fragments[".cart-dropdown-items"] = ob_get_clean();

    return $fragments;
}
add_filter("woocommerce_add_to_cart_fragments", "mr_refresh_cart_fragments");

/**
 * Render cart dropdown items (reusable function)
 * NOTE: This returns only the <li> items, NOT wrapped in <ul>
 */
function mr_render_cart_dropdown_items($max_items = 99)
{
    $cart_count = WC()->cart->get_cart_contents_count();

    if ($cart_count > 0):
        $cart_items = WC()->cart->get_cart();
        $item_count = 0;
        foreach ($cart_items as $cart_item_key => $cart_item):
            if ($item_count >= $max_items) {
                break;
            }
            $_product = apply_filters(
                "woocommerce_cart_item_product",
                $cart_item["data"],
                $cart_item,
                $cart_item_key,
            );
            if (
                $_product &&
                $_product->exists() &&
                $cart_item["quantity"] > 0
            ): ?>
<li class="cart-dropdown-item" data-cart-key="<?php echo esc_attr(
    $cart_item_key,
); ?>">
    <a href="<?php echo esc_url(
        $_product->get_permalink($cart_item),
    ); ?>" class="cart-item-image">
        <?php echo wp_kses_post($_product->get_image("thumbnail")); ?>
    </a>
    <div class="cart-item-details">
        <a href="<?php echo esc_url(
            $_product->get_permalink($cart_item),
        ); ?>" class="cart-item-name">
            <?php echo wp_kses_post($_product->get_name()); ?>
        </a>
        <div class="cart-item-meta">
            <span class="cart-item-quantity"><?php echo esc_html(
                $cart_item["quantity"],
            ); ?> × </span>
            <span class="cart-item-price"><?php echo WC()->cart->get_product_price(
                $_product,
            ); ?></span>
        </div>
    </div>
    <button type="button" class="cart-item-remove" data-cart-item-key="<?php echo esc_attr(
        $cart_item_key,
    ); ?>" aria-label="<?php esc_attr_e("Remove item", "macedon-ranges"); ?>">
        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
</li>
    <?php $item_count++;endif;
        endforeach;
    else:
         ?>
<li class="cart-dropdown-empty">
    <p><?php esc_html_e("Your cart is empty.", "macedon-ranges"); ?></p>
</li>
    <?php
    endif;
}

/**
 * COUPON AJAX HANDLERS FOR CHECKOUT - FIXED VERSION
 * These handlers now properly validate coupons and return appropriate error messages
 */

/**
 * AJAX Handler: Apply coupon - WITH PROPER VALIDATION
 */
function mr_ajax_apply_coupon()
{
    // Check if WooCommerce is active
    if (!class_exists("WooCommerce")) {
        wp_send_json_error([
            "message" => __("WooCommerce is not active", "macedon-ranges"),
        ]);
    }

    // Get coupon code
    $coupon_code = isset($_POST["coupon_code"])
        ? wc_sanitize_coupon_code(wp_unslash($_POST["coupon_code"]))
        : "";

    if (empty($coupon_code)) {
        wp_send_json_error([
            "message" => __("Please enter a coupon code", "macedon-ranges"),
        ]);
    }

    // Check if coupon exists
    $coupon = new WC_Coupon($coupon_code);
    
    if (!$coupon || !$coupon->get_id()) {
        wp_send_json_error([
            "message" => sprintf(
                __('Coupon "%s" does not exist!', "macedon-ranges"),
                esc_html($coupon_code)
            ),
        ]);
    }

    // Validate coupon
    $discounts = new WC_Discounts(WC()->cart);
    $valid = $discounts->is_coupon_valid($coupon);
    
    if (is_wp_error($valid)) {
        wp_send_json_error([
            "message" => $valid->get_error_message(),
        ]);
    }

    // Check if coupon is already applied
    $applied_coupons = WC()->cart->get_applied_coupons();
    if (in_array($coupon_code, $applied_coupons)) {
        wp_send_json_error([
            "message" => __("Coupon code already applied!", "macedon-ranges"),
        ]);
    }

    // Apply coupon
    $result = WC()->cart->apply_coupon($coupon_code);

    if ($result) {
        WC()->cart->calculate_totals();

        wp_send_json_success([
            "message" => __("Coupon code applied successfully.", "macedon-ranges"),
            "coupon_code" => $coupon_code,
        ]);
    } else {
        // Get WooCommerce error messages
        $error_messages = wc_get_notices('error');
        $error_message = !empty($error_messages) 
            ? strip_tags($error_messages[0]['notice']) 
            : __("Failed to apply coupon. Please try again.", "macedon-ranges");
        
        // Clear notices so they don't show elsewhere
        wc_clear_notices();
        
        wp_send_json_error([
            "message" => $error_message,
        ]);
    }
}

add_action("wp_ajax_apply_coupon_checkout", "mr_ajax_apply_coupon");
add_action("wp_ajax_nopriv_apply_coupon_checkout", "mr_ajax_apply_coupon");

/**
 * AJAX Handler: Remove coupon - WITH PROPER VALIDATION
 */
function mr_ajax_remove_coupon()
{
    // Check if WooCommerce is active
    if (!class_exists("WooCommerce")) {
        wp_send_json_error([
            "message" => __("WooCommerce is not active", "macedon-ranges"),
        ]);
    }

    // Get coupon code
    $coupon_code = isset($_POST["coupon_code"])
        ? wc_sanitize_coupon_code(wp_unslash($_POST["coupon_code"]))
        : "";

    if (empty($coupon_code)) {
        wp_send_json_error([
            "message" => __("Invalid coupon code", "macedon-ranges"),
        ]);
    }

    // Check if coupon is actually applied
    $applied_coupons = WC()->cart->get_applied_coupons();
    if (!in_array($coupon_code, $applied_coupons)) {
        wp_send_json_error([
            "message" => __("This coupon is not applied to your cart", "macedon-ranges"),
        ]);
    }

    // Remove coupon
    $result = WC()->cart->remove_coupon($coupon_code);

    if ($result) {
        WC()->cart->calculate_totals();

        wp_send_json_success([
            "message" => __("Coupon removed successfully", "macedon-ranges"),
            "coupon_code" => $coupon_code,
        ]);
    } else {
        wp_send_json_error([
            "message" => __("Failed to remove coupon", "macedon-ranges"),
        ]);
    }
}

add_action("wp_ajax_remove_coupon_checkout", "mr_ajax_remove_coupon");
add_action("wp_ajax_nopriv_remove_coupon_checkout", "mr_ajax_remove_coupon");