<?php
/**
 * WooCommerce Integration - FULLY INTEGRATED WITH CUSTOMIZER
 * NOW WITH WORKING CATEGORY FILTER FUNCTIONALITY
 * UPDATED: Added fallback background image support
 * FIXED: Removed shipping calculator from cart page
 *
 * woocommerce.php
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
 * Reorganize single product layout
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

add_action('woocommerce_single_product_summary', function() {
    echo '<div class="product-purchase-group">';
    woocommerce_template_single_add_to_cart();
    echo '</div>';
}, 31);

/**
 * Enable AJAX Add to Cart on Single Product Pages
 */
add_filter('woocommerce_add_to_cart_redirect', '__return_false');


/**
 * AJAX Handler: Add to Cart for Single Product Page (Simple + Variable)
 * This handles AJAX add-to-cart from single product pages
 */
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'aaapos_ajax_add_to_cart_handler');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'aaapos_ajax_add_to_cart_handler');

function aaapos_ajax_add_to_cart_handler() {
    // Check if WooCommerce is active
    if (!function_exists('WC')) {
        wp_send_json_error(array('message' => 'WooCommerce not active'));
        return;
    }
    
    // Get product ID
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    
    // Get variation attributes if variable product
    $variation = array();
    if ($variation_id) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[$key] = sanitize_text_field($value);
            }
        }
    }
    
    // Validate
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    
    if (!$passed_validation) {
        $error_notices = wc_get_notices('error');
        wc_clear_notices();
        
        wp_send_json_error(array(
            'error' => true,
            'message' => !empty($error_notices) ? strip_tags($error_notices[0]['notice']) : 'Validation failed',
            'product_url' => get_permalink($product_id)
        ));
        return;
    }
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
    
    if (!$cart_item_key) {
        $error_notices = wc_get_notices('error');
        wc_clear_notices();
        
        wp_send_json_error(array(
            'error' => true,
            'message' => !empty($error_notices) ? strip_tags($error_notices[0]['notice']) : 'Could not add to cart',
            'product_url' => get_permalink($product_id)
        ));
        return;
    }
    
    // Success! Trigger action
    do_action('woocommerce_ajax_added_to_cart', $product_id);
    
    // Calculate cart totals
    WC()->cart->calculate_totals();
    
    // Get updated cart count
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_style = get_theme_mod('cart_icon_style', 'icon-count');
    
    // Build fragments array
    $fragments = array();
    
    // Fragment 1: Cart count badge
    ob_start();
    ?>
    <span class="cart-count"<?php echo $cart_count === 0 ? ' style="display:none;"' : ''; ?>>
        <?php echo esc_html($cart_count); ?>
    </span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    
    // Fragment 2: Cart item count in dropdown header
    ob_start();
    ?>
    <span class="cart-item-count">
        <?php 
        echo esc_html($cart_count) . ' ';
        echo $cart_count === 1 ? esc_html__('item', 'aaapos') : esc_html__('items', 'aaapos'); 
        ?>
    </span>
    <?php
    $fragments['.cart-item-count'] = ob_get_clean();
    
    // Fragment 3: Cart subtotal
    ob_start();
    ?>
    <strong class="cart-subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
    <?php
    $fragments['.cart-subtotal-amount'] = ob_get_clean();
    
    // Fragment 4: Cart total (if using icon-total style)
    if ($cart_style === 'icon-total') {
        ob_start();
        ?>
        <span class="cart-total">
            <?php echo WC()->cart->get_cart_subtotal(); ?>
        </span>
        <?php
        $fragments['.cart-total'] = ob_get_clean();
    }
    
    // Fragment 5: Full cart dropdown items list
    ob_start();
    if ($cart_count > 0):
        $cart_items = WC()->cart->get_cart();
        foreach ($cart_items as $cart_item_key_loop => $cart_item):
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key_loop);
            if ($_product && $_product->exists() && $cart_item['quantity'] > 0): ?>
        <li class="cart-dropdown-item">
            <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>" class="cart-item-image">
                <?php echo wp_kses_post($_product->get_image('thumbnail')); ?>
            </a>
            <div class="cart-item-details">
                <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>" class="cart-item-name">
                    <?php echo wp_kses_post($_product->get_name()); ?>
                </a>
                <div class="cart-item-meta">
                    <span class="cart-item-quantity"><?php echo esc_html($cart_item['quantity']); ?> × </span>
                    <span class="cart-item-price"><?php echo WC()->cart->get_product_price($_product); ?></span>
                </div>
            </div>
            <button type="button" class="cart-item-remove" data-cart-item-key="<?php echo esc_attr($cart_item_key_loop); ?>" aria-label="<?php esc_attr_e('Remove item', 'aaapos'); ?>">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </li>
        <?php endif;
        endforeach;
    else: ?>
        <li class="cart-dropdown-empty">
            <p><?php esc_html_e('Your cart is empty.', 'aaapos'); ?></p>
        </li>
    <?php endif;
    $fragments['.cart-dropdown-items'] = ob_get_clean();
    
    // Apply WooCommerce filter to allow other plugins to add fragments
    $fragments = apply_filters('woocommerce_add_to_cart_fragments', $fragments);
    
    $data = array(
        'fragments' => $fragments,
        'cart_hash' => WC()->cart->get_cart_hash(),
        'cart_item_key' => $cart_item_key
    );
    
    wp_send_json_success($data);
}

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

// Move stock status to product meta
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_stock', 10);

add_action('woocommerce_product_meta_end', 'custom_add_stock_to_meta');
function custom_add_stock_to_meta() {
    global $product;
    if ($product) {
        echo '<span class="stock-wrapper">STATUS: ' . wc_get_stock_html($product) . '</span>';
    }
}

/**
 * Display Cart Activity Message
 * Shows how many people have added this product to their cart
 * Displays as a full-width bar below the purchase group
 */
function aaapos_display_cart_activity() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Get product ID
    $product_id = $product->get_id();
    
    // Get or generate cart activity count
    $cart_count = get_post_meta($product_id, '_cart_activity_count', true);
    
    // If no count exists, generate a realistic number based on product data
    if (empty($cart_count)) {
        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $total_sales = (int) get_post_meta($product_id, 'total_sales', true);
        
        // Calculate a realistic number
        if ($total_sales > 0) {
            $cart_count = max(5, min(150, floor($total_sales * 0.2) + wp_rand(5, 20)));
        } else {
            $cart_count = wp_rand(8, 35);
        }
        
        // Store it for consistency
        update_post_meta($product_id, '_cart_activity_count', $cart_count);
    }
    
    // Display the message
    ?>
    <div class="product-cart-activity">
        <svg class="product-cart-activity__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="9" cy="21" r="1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="20" cy="21" r="1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="product-cart-activity__text">
            <span class="product-cart-activity__count"><?php echo esc_html($cart_count); ?></span>
            <?php esc_html_e(' people have added this product to their cart', 'aaapos'); ?>
        </span>
    </div>
    <?php
}

// Add new hook to display after the entire purchase group
add_action('woocommerce_after_add_to_cart_form', 'aaapos_display_cart_activity', 10);

/**
 * Increment cart activity count when product is added to cart
 */
function aaapos_track_cart_activity($cart_item_key, $product_id) {
    $current_count = (int) get_post_meta($product_id, '_cart_activity_count', true);
    $new_count = max(1, $current_count + 1);
    update_post_meta($product_id, '_cart_activity_count', $new_count);
}
add_action('woocommerce_add_to_cart', 'aaapos_track_cart_activity', 10, 2);

/**
 * Display Product Trust Badges / Benefits
 * Shows shipping, returns, and other trust signals below product meta
 * Now customizable via WordPress Customizer
 */
function aaapos_display_product_trust_badges() {
    
    // Check if trust badges are enabled
    if (!get_theme_mod('show_trust_badges', true)) {
        return;
    }
    
    // Get badge texts from customizer
    $badge_1_text = get_theme_mod('trust_badge_1_text', __('Free shipping on all orders over $100', 'aaapos'));
    $badge_2_text = get_theme_mod('trust_badge_2_text', __('14 days easy refund & returns', 'aaapos'));
    $badge_3_text = get_theme_mod('trust_badge_3_text', __('Product taxes and customs duties included', 'aaapos'));
    
    $badge_1_enable = get_theme_mod('trust_badge_1_enable', true);
    $badge_2_enable = get_theme_mod('trust_badge_2_enable', true);
    $badge_3_enable = get_theme_mod('trust_badge_3_enable', true);
    
    ?>
    <div class="product-trust-badges">
        
        <?php if ($badge_1_enable && !empty($badge_1_text)): ?>
        <div class="trust-badge-item">
            <svg class="trust-badge-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="trust-badge-text"><?php echo esc_html($badge_1_text); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if ($badge_2_enable && !empty($badge_2_text)): ?>
        <div class="trust-badge-item">
            <svg class="trust-badge-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="trust-badge-text"><?php echo esc_html($badge_2_text); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if ($badge_3_enable && !empty($badge_3_text)): ?>
        <div class="trust-badge-item">
            <svg class="trust-badge-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="trust-badge-text"><?php echo esc_html($badge_3_text); ?></span>
        </div>
        <?php endif; ?>
        
    </div>
    <?php
}

// Hook after product meta section
add_action('woocommerce_single_product_summary', 'aaapos_display_product_trust_badges', 45);

/**
 * Display Secure Payment Badges
 * Shows payment method icons below trust badges
 * Uses the same payment icons as footer for consistency
 */
function aaapos_display_secure_payment_badges() {
    
    // Check if secure payments are enabled
    if (!get_theme_mod('show_secure_payments', true)) {
        return;
    }
    
    // Get title from customizer
    $title = get_theme_mod('secure_payments_title', __('Secure payments:', 'aaapos'));
    
    // Payment cards mapping
    $payment_cards = array(
        'visa' => array(
            'setting' => 'payment_icon_visa',
            'show' => 'payment_show_visa',
            'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg'
        ),
        'mastercard' => array(
            'setting' => 'payment_icon_mastercard',
            'show' => 'payment_show_mastercard',
            'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg'
        ),
        'amex' => array(
            'setting' => 'payment_icon_amex',
            'show' => 'payment_show_amex',
            'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg'
        ),
        'paypal' => array(
            'setting' => 'payment_icon_paypal',
            'show' => 'payment_show_paypal',
            'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg'
        ),
        'discover' => array(
            'setting' => 'payment_icon_discover',
            'show' => 'payment_show_discover',
            'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/5/57/Discover_Card_logo.svg'
        ),
    );
    
    // Build array of payment icons to display
    $payment_icons = array();
    
    foreach ($payment_cards as $card => $data) {
        // Check if this card is enabled in footer settings
        if (!get_theme_mod($data['show'], true)) {
            continue;
        }
        
        // Get the uploaded icon (media ID)
        $icon_id = get_theme_mod($data['setting']);
        
        if ($icon_id) {
            // Get the uploaded image URL
            $icon_url = wp_get_attachment_image_src($icon_id, 'full');
            if ($icon_url) {
                $payment_icons[] = $icon_url[0];
            } else {
                // If uploaded image not found, use fallback
                $payment_icons[] = $data['fallback'];
            }
        } else {
            // No uploaded image, use fallback
            $payment_icons[] = $data['fallback'];
        }
    }
    
    // If no icons, don't display anything
    if (empty($payment_icons)) {
        return;
    }
    
    ?>
    <div class="product-secure-payments">
        <span class="secure-payments-title"><?php echo esc_html($title); ?></span>
        <div class="payment-icons-wrapper">
            <?php foreach ($payment_icons as $icon_url): ?>
                <img src="<?php echo esc_url($icon_url); ?>" alt="<?php esc_attr_e('Payment method', 'aaapos'); ?>" class="payment-icon">
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

// Hook it after trust badges
add_action('woocommerce_single_product_summary', 'aaapos_display_secure_payment_badges', 46);


/**
 * Custom Review Layout - Works WITH WooCommerce Tabs
 * Replace the previous code in: inc/woocommerce.php
 * 
 * @package AAAPOS_Prime
 * @version 2.0.4
 */

/**
 * Modify the reviews tab content
 */
add_filter('woocommerce_product_tabs', 'aaapos_customize_reviews_tab', 98);

function aaapos_customize_reviews_tab($tabs) {
    if (isset($tabs['reviews'])) {
        // Replace the callback for reviews tab
        $tabs['reviews']['callback'] = 'aaapos_custom_reviews_tab_content';
    }
    return $tabs;
}

/**
 * Custom reviews tab content
 */
function aaapos_custom_reviews_tab_content() {
    global $product;
    
    if (!comments_open()) {
        return;
    }
    
    $rating_count = $product->get_rating_count();
    $average_rating = $product->get_average_rating();
    $review_count = $product->get_review_count();
    
    ?>
    <div id="reviews" class="woocommerce-Reviews">
        <div class="reviews-left-column">
            
            <!-- RATING SUMMARY BOX -->
            <div class="reviews-rating-summary">
                <div class="summary-rating-left">
                    <div class="summary-rating-number"><?php echo number_format($average_rating, 2); ?></div>
                    
                    <div class="summary-stars-display">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= floor($average_rating)) {
                                echo '<span class="star filled">★</span>';
                            } elseif ($i - 0.5 <= $average_rating) {
                                echo '<span class="star half">★</span>';
                            } else {
                                echo '<span class="star empty">★</span>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="summary-rating-count">
                        (<?php echo $rating_count; ?> <?php echo _n('Rating', 'Ratings', $rating_count, 'aaapos'); ?>)
                    </div>
                </div>
                
                <?php if ($rating_count > 0) : ?>
                <div class="rating-breakdown">
                    <?php for ($i = 5; $i >= 1; $i--) : 
                        $count = $product->get_rating_count($i);
                        $percentage = ($count / $rating_count) * 100;
                    ?>
                    <div class="rating-breakdown-item">
                        <span class="stars-label"><?php echo $i; ?> ★</span>
                        <div class="rating-breakdown-bar">
                            <div class="rating-breakdown-bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                        </div>
                        <span class="percentage"><?php echo round($percentage); ?>%</span>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- REVIEW FORM -->
            <div id="review_form_wrapper">
                <div id="review_form">
                    <?php
                    $commenter = wp_get_current_commenter();
                    
                    $comment_form = array(
                        'title_reply'          => esc_html__('Add a review', 'aaapos'),
                        'title_reply_to'       => esc_html__('Leave a Reply to %s', 'aaapos'),
                        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
                        'title_reply_after'    => '</h3>',
                        'comment_notes_before' => '<p class="comment-notes">' . esc_html__('Your email address will not be published. Required fields are marked', 'aaapos') . ' <span class="required">*</span></p>',
                        'comment_notes_after'  => '',
                        'label_submit'         => esc_html__('Submit', 'aaapos'),
                        'logged_in_as'         => '',
                        'comment_field'        => '',
                        'submit_button'        => '<button type="submit" class="submit">%4$s</button>',
                        'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
                    );
                    
                    $account_page_url = wc_get_page_permalink('myaccount');
                    if ($account_page_url) {
                        $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'aaapos'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
                    }
                    
                    comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                    ?>
                </div>
            </div>
            
        </div>
        
        <!-- RIGHT COLUMN - REVIEWS LIST -->
        <div id="comments">
            <h2 class="woocommerce-Reviews-title">
                <?php
                if ($review_count && wc_review_ratings_enabled()) {
                    printf(esc_html(_n('%1$s review for %2$s', '%1$s reviews for %2$s', $review_count, 'aaapos')), esc_html($review_count), '<span>' . get_the_title() . '</span>');
                } else {
                    esc_html_e('Reviews', 'aaapos');
                }
                ?>
            </h2>
            
            <?php
            // Get product reviews
            $comments = get_comments(array(
                'post_id' => $product->get_id(),
                'status'  => 'approve',
                'type'    => 'review',
            ));
            
            if ($comments) : ?>
                <ol class="commentlist">
                    <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array(
                        'callback' => 'aaapos_custom_review_callback',
                        'style'    => 'ol',
                        'per_page' => -1,
                    )), $comments); ?>
                </ol>
                
                <?php
                if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                    echo '<nav class="woocommerce-pagination">';
                    paginate_comments_links(apply_filters('woocommerce_comment_pagination_args', array(
                        'prev_text' => '&larr;',
                        'next_text' => '&rarr;',
                        'type'      => 'list',
                    )));
                    echo '</nav>';
                endif;
                ?>
            <?php else : ?>
                <p class="woocommerce-noreviews"><?php esc_html_e('There are no reviews yet.', 'aaapos'); ?></p>
            <?php endif; ?>
        </div>
        
    </div>
    <?php
}

/**
 * Custom review callback - Restructured layout
 */
function aaapos_custom_review_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    
    $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
    $verified = wc_review_is_from_verified_owner($comment->comment_ID);
    
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment_container">
            <?php
            echo get_avatar($comment, apply_filters('woocommerce_review_gravatar_size', '60'), '', '', array('class' => 'avatar'));
            ?>
            <div class="comment-text">
                
                <!-- Top row: Name/Verified + Stars -->
                <div class="review-header-row">
                    <div class="review-meta-left">
                        <strong class="woocommerce-review__author"><?php comment_author(); ?></strong>
                        
                        <?php if ($verified) : ?>
                            <em class="woocommerce-review__verified verified">
                                <?php esc_html_e('(verified owner)', 'aaapos'); ?>
                            </em>
                        <?php endif; ?>
                        
                        <time class="woocommerce-review__published-date" datetime="<?php echo get_comment_date('c'); ?>">
                            <?php echo get_comment_date(wc_date_format()); ?>
                        </time>
                    </div>
                    
                    <?php if ($rating && wc_review_ratings_enabled()) : ?>
                        <div class="review-stars-right">
                            <?php
                            echo '<div class="star-rating" role="img" aria-label="' . sprintf(esc_attr__('Rated %d out of 5', 'aaapos'), $rating) . '">';
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<span class="star filled">★</span>';
                                } else {
                                    echo '<span class="star empty">★</span>';
                                }
                            }
                            echo '</div>';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Review text -->
                <div class="description">
                    <?php comment_text(); ?>
                </div>
                
            </div>
        </div>
    </li>
    <?php
}

/**
 * Customize comment form fields
 */
add_filter('comment_form_default_fields', 'aaapos_custom_comment_fields');

function aaapos_custom_comment_fields($fields) {
    $commenter = wp_get_current_commenter();
    
    $fields['author'] = '<div class="comment-form-author">
        <label for="author">' . esc_html__('Name', 'aaapos') . ' <span class="required">*</span></label>
        <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" required placeholder="' . esc_attr__('Enter your name', 'aaapos') . '" />
    </div>';
    
    $fields['email'] = '<div class="comment-form-email">
        <label for="email">' . esc_html__('Email', 'aaapos') . ' <span class="required">*</span></label>
        <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" required placeholder="' . esc_attr__('Enter your email', 'aaapos') . '" />
    </div>';
    
    $fields['cookies'] = '<div class="comment-form-cookies-consent">
        <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
        <label for="wp-comment-cookies-consent">' . esc_html__('Save my name, email, and website in this browser for the next time I comment.', 'aaapos') . '</label>
    </div>';
    
    unset($fields['url']);
    
    return $fields;
}

/**
 * Add rating field and comment field
 */
add_filter('woocommerce_product_review_comment_form_args', 'aaapos_add_rating_and_comment_field');

function aaapos_add_rating_and_comment_field($comment_form) {
    $comment_form['comment_field'] = '<div class="comment-form-rating">
        <label for="rating">' . esc_html__('Your rating', 'aaapos') . ' <span class="required">*</span></label>
        <div class="stars-rating-input">
            <input type="radio" id="rating-5" name="rating" value="5" required />
            <label for="rating-5" title="' . esc_attr__('5 stars', 'aaapos') . '">★</label>
            
            <input type="radio" id="rating-4" name="rating" value="4" />
            <label for="rating-4" title="' . esc_attr__('4 stars', 'aaapos') . '">★</label>
            
            <input type="radio" id="rating-3" name="rating" value="3" />
            <label for="rating-3" title="' . esc_attr__('3 stars', 'aaapos') . '">★</label>
            
            <input type="radio" id="rating-2" name="rating" value="2" />
            <label for="rating-2" title="' . esc_attr__('2 stars', 'aaapos') . '">★</label>
            
            <input type="radio" id="rating-1" name="rating" value="1" />
            <label for="rating-1" title="' . esc_attr__('1 star', 'aaapos') . '">★</label>
        </div>
    </div>
    
    <div class="comment-form-comment">
        <label for="comment">' . esc_html__('Your review', 'aaapos') . ' <span class="required">*</span></label>
        <textarea id="comment" name="comment" cols="45" rows="8" required placeholder="' . esc_attr__('Share your experience with this product...', 'aaapos') . '"></textarea>
    </div>
    
    <div class="comment-form-captcha">
        <label for="captcha">6 + 10 = ?</label>
        <input id="captcha" name="captcha" type="text" required placeholder="' . esc_attr__('Enter answer', 'aaapos') . '" />
    </div>';
    
    return $comment_form;
}

/**
 * Hide default rating select field
 */
add_action('wp_head', 'aaapos_hide_default_rating_field');

function aaapos_hide_default_rating_field() {
    if (!is_product()) {
        return;
    }
    ?>
    <style>
        .comment-form-rating select,
        .comment-form-rating .stars {
            display: none !important;
        }
    </style>
    <?php
}

/**
 * Display Social Share Buttons on Single Product Page
 * Shows Facebook, X (Twitter), Pinterest, WhatsApp, Telegram, Email, and Copy Link buttons
 * WITH TOOLTIP LABELS
 */
function aaapos_display_product_share_buttons() {
    global $product;
    
    // Check if feature is enabled in customizer
    if (!get_theme_mod('show_product_share', true)) {
        return;
    }
    
    if (!$product) {
        return;
    }
    
    // Get product data
    $product_title = get_the_title();
    $product_url = get_permalink();
    $product_image = wp_get_attachment_url($product->get_image_id());
    
    // Encode URLs for sharing
    $encoded_url = urlencode($product_url);
    $encoded_title = urlencode($product_title);
    $encoded_image = urlencode($product_image);
    
    // Build share URLs
    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
    $twitter_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
    $pinterest_url = 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&media=' . $encoded_image . '&description=' . $encoded_title;
    $whatsapp_url = 'https://api.whatsapp.com/send?text=' . $encoded_title . ' ' . $encoded_url;
    $telegram_url = 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
    $email_url = 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url;
    
    // Get customizable title
    $share_title = get_theme_mod('product_share_title', __('Share this post', 'aaapos'));
    
    ?>
    <div class="product-share-section">
        <span class="product-share-title"><?php echo esc_html($share_title); ?></span>
        
        <div class="product-share-buttons">
            <!-- Facebook -->
            <a href="<?php echo esc_url($facebook_url); ?>" 
               class="share-button share-button--facebook" 
               target="_blank" 
               rel="noopener noreferrer"
               data-tooltip="<?php esc_attr_e('Share on Facebook', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share on Facebook', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
            
            <!-- X (Twitter) -->
            <a href="<?php echo esc_url($twitter_url); ?>" 
               class="share-button share-button--twitter" 
               target="_blank" 
               rel="noopener noreferrer"
               data-tooltip="<?php esc_attr_e('Share on X', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share on X', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </a>
            
            <!-- Pinterest -->
            <a href="<?php echo esc_url($pinterest_url); ?>" 
               class="share-button share-button--pinterest" 
               target="_blank" 
               rel="noopener noreferrer"
               data-tooltip="<?php esc_attr_e('Share on Pinterest', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share on Pinterest', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/>
                </svg>
            </a>
            
            <!-- WhatsApp -->
            <a href="<?php echo esc_url($whatsapp_url); ?>" 
               class="share-button share-button--whatsapp" 
               target="_blank" 
               rel="noopener noreferrer"
               data-tooltip="<?php esc_attr_e('Share on WhatsApp', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share on WhatsApp', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
            
            <!-- Telegram -->
            <a href="<?php echo esc_url($telegram_url); ?>" 
               class="share-button share-button--telegram" 
               target="_blank" 
               rel="noopener noreferrer"
               data-tooltip="<?php esc_attr_e('Share on Telegram', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share on Telegram', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
            </a>
            
            <!-- Email -->
            <a href="<?php echo esc_url($email_url); ?>" 
               class="share-button share-button--email"
               data-tooltip="<?php esc_attr_e('Share via Email', 'aaapos'); ?>"
               aria-label="<?php esc_attr_e('Share via Email', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </a>
            
            <!-- Copy Link -->
            <button type="button" 
                    class="share-button share-button--copy" 
                    data-url="<?php echo esc_attr($product_url); ?>"
                    data-tooltip="<?php esc_attr_e('Copy link', 'aaapos'); ?>"
                    aria-label="<?php esc_attr_e('Copy link', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
            </button>
        </div>
    </div>
    <?php
}

// Hook it after secure payment badges (priority 47)
add_action('woocommerce_single_product_summary', 'aaapos_display_product_share_buttons', 47);

/**
 * Display Product Navigation (Previous/Next + Return to Shop)
 * Shows navigation buttons INLINE with product title on same row
 * WITH HOVER TOOLTIPS - Shop label and product preview cards
 * 
 * UPDATED: Added product image, title, and price in hover tooltips
 * 
 * @since 1.0.2
 */

// Remove default product title
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

// Add custom title with navigation wrapper
add_action('woocommerce_single_product_summary', 'aaapos_product_title_with_navigation', 5);

function aaapos_product_title_with_navigation() {
    global $post;
    
    // Get adjacent products (in same category for better relevance)
    $prev_post = get_previous_post(true, '', 'product_cat');
    $next_post = get_next_post(true, '', 'product_cat');
    
    // Get shop page URL
    $shop_url = get_permalink(wc_get_page_id('shop'));
    
    ?>
    <div class="product-title-nav-wrapper">
        
        <!-- Product Title (Left side) -->
        <h1 class="product_title entry-title"><?php echo esc_html(get_the_title()); ?></h1>
        
        <!-- Navigation Buttons (Right side) -->
        <div class="product-navigation">
            <div class="product-nav-buttons">
                
                <!-- Previous Product Button -->
                <?php if ($prev_post) : 
                    $prev_product = wc_get_product($prev_post->ID);
                    $prev_image = get_the_post_thumbnail_url($prev_post->ID, 'thumbnail');
                    $prev_title = get_the_title($prev_post->ID);
                    $prev_price = $prev_product ? $prev_product->get_price_html() : '';
                ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" 
                       class="product-nav-btn product-nav-prev"
                       aria-label="<?php esc_attr_e('Previous product', 'aaapos'); ?>">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                        <!-- Product Preview Card -->
                        <div class="product-nav-preview">
                            <?php if ($prev_image) : ?>
                                <img src="<?php echo esc_url($prev_image); ?>" 
                                     alt="<?php echo esc_attr($prev_title); ?>" 
                                     class="product-nav-preview-img">
                            <?php endif; ?>
                            <div class="product-nav-preview-title"><?php echo esc_html($prev_title); ?></div>
                            <div class="product-nav-preview-price"><?php echo wp_kses_post($prev_price); ?></div>
                        </div>
                    </a>
                <?php else : ?>
                    <span class="product-nav-btn product-nav-prev disabled" aria-hidden="true">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </span>
                <?php endif; ?>
                
                <!-- Products Grid Button (Return to shop) -->
                <a href="<?php echo esc_url($shop_url); ?>" 
                   class="product-nav-btn product-nav-grid"
                   aria-label="<?php esc_attr_e('View all products', 'aaapos'); ?>">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </a>
                
                <!-- Next Product Button -->
                <?php if ($next_post) : 
                    $next_product = wc_get_product($next_post->ID);
                    $next_image = get_the_post_thumbnail_url($next_post->ID, 'thumbnail');
                    $next_title = get_the_title($next_post->ID);
                    $next_price = $next_product ? $next_product->get_price_html() : '';
                ?>
                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" 
                       class="product-nav-btn product-nav-next"
                       aria-label="<?php esc_attr_e('Next product', 'aaapos'); ?>">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                        <!-- Product Preview Card -->
                        <div class="product-nav-preview">
                            <?php if ($next_image) : ?>
                                <img src="<?php echo esc_url($next_image); ?>" 
                                     alt="<?php echo esc_attr($next_title); ?>" 
                                     class="product-nav-preview-img">
                            <?php endif; ?>
                            <div class="product-nav-preview-title"><?php echo esc_html($next_title); ?></div>
                            <div class="product-nav-preview-price"><?php echo wp_kses_post($next_price); ?></div>
                        </div>
                    </a>
                <?php else : ?>
                    <span class="product-nav-btn product-nav-next disabled" aria-hidden="true">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </span>
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
    <?php
}

// Add custom title with navigation wrapper
add_action('woocommerce_single_product_summary', 'aaapos_product_title_with_navigation', 5);

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
            "parent" => 0,
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
 * Custom shipping method output for checkout page
 * This ensures the shipping section updates via AJAX
 */
function woocommerce_order_review_shipping() {
    wc_cart_totals_shipping_html();
}

/**
 * Add shipping method div to WooCommerce fragments for AJAX updates
 * This ensures the shipping method section updates when address changes
 * FIXED: Properly wraps content and forces recalculation
 */
add_filter('woocommerce_update_order_review_fragments', 'aaapos_shipping_method_fragment', 10, 1);

function aaapos_shipping_method_fragment($fragments) {
    // Force WooCommerce to recalculate shipping
    WC()->cart->calculate_shipping();
    
    ob_start();
    woocommerce_order_review_shipping();
    $shipping_html = ob_get_clean();
    
    // Add to fragments array
    $fragments['.shipping-method-options'] = '<div class="shipping-method-options" id="shipping_method">' . $shipping_html . '</div>';
    
    return $fragments;
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
            <strong><?php echo esc_html(number_format($average_rating, 1)); ?></strong>
        </span>
        
        <?php if ($review_count > 0): ?>
            <a href="#reviews" class="woocommerce-review-link" rel="nofollow">
                <?php printf(
                    _n('(%s review)', '(%s reviews)', $review_count, 'aaapos-prime'),
                    '<span class="count">' . esc_html($review_count) . '</span>'
                ); ?>
            </a>
        <?php endif; ?>
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
            "/assets/css/myaccount/myaccount-main.css",
        ["aaapos-woocommerce-base"],
        AAAPOS_VERSION . "." . time(),
    );
    
    // Enqueue responsive styles separately to ensure proper cascade
    wp_enqueue_style(
        "aaapos-myaccount-responsive",
        get_template_directory_uri() .
            "/assets/css/myaccount/myaccount-responsive.css",
        ["aaapos-woocommerce-myaccount"],
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
           "aaapos",
       ); ?>');">
        <?php esc_html_e("Clear Shopping Cart", "aaapos"); ?>
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
 * FIXED: Only redirects to cart when on cart page, stays on checkout when on checkout
 */
function mr_handle_coupon_removal()
{
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
                __("Coupon removed successfully.", "aaapos"),
                "success",
            );
            if (is_cart()) {
                wp_safe_redirect(wc_get_cart_url());
                exit();
            }
        }
    }

    // Also handle removal via URL (backward compatibility) - only on cart page
    if (isset($_GET["remove_coupon"]) && is_cart()) {
        $coupon_code = sanitize_text_field($_GET["remove_coupon"]);
        WC()->cart->remove_coupon($coupon_code);
        wc_add_notice(__("Coupon removed successfully.", "aaapos"), "success");
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
                    "aaapos",
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
 * Enqueue Quick View Assets (FIXED - Works everywhere)
 */
function aaapos_enqueue_quick_view_assets()
{
    // Only load if enabled in customizer
    if (!get_theme_mod("show_quick_view", true)) {
        return;
    }

    // FIXED: Load on ALL pages where Quick View might appear
    // Including: shop, archives, search, cart (suggested products), and SINGLE PRODUCT (related products)
    if (
        !is_shop() &&
        !is_product_category() &&
        !is_product_tag() &&
        !is_search() &&
        !is_cart() &&
        !is_product() // ADDED THIS - Critical for related products!
    ) {
        return;
    }

    // Quick View Button CSS
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

        // FIXED: Always localize with the correct nonce
        wp_localize_script("aaapos-quick-view-js", "aaaposQuickView", [
            "ajax_url" => admin_url("admin-ajax.php"),
            "nonce" => wp_create_nonce("aaapos_quick_view_nonce"),
        ]);
    }
}
add_action("wp_enqueue_scripts", "aaapos_enqueue_quick_view_assets", 1000);

/**
 * AJAX Handler: Update checkout fragments when shipping method changes
 * This ensures the order review updates when shipping is selected
 */
function aaapos_update_order_review_shipping() {
    check_ajax_referer( 'update-order-review', 'security' );
    
    if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
        foreach ( $_POST['shipping_method'] as $i => $value ) {
            WC()->session->set( 'chosen_shipping_methods', array( $value ) );
        }
    }
    
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();
    
    woocommerce_order_review();
    
    die();
}
add_action( 'wp_ajax_update_order_review', 'aaapos_update_order_review_shipping' );
add_action( 'wp_ajax_nopriv_update_order_review', 'aaapos_update_order_review_shipping' );

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
        <?php
        // Get user's Gravatar or profile image
        $user_id = get_current_user_id();
        $user_email = $current_user->user_email;
        
        // Try to get Gravatar
        $avatar = get_avatar($user_id, 48, '', $current_user->display_name, array('class' => 'greeting-icon-img'));
    
        echo $avatar;
        ?>
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
            "label" => __("Shopping cart", "aaapos"),
            "url" => wc_get_cart_url(),
        ],
        2 => [
            "number" => "2",
            "label" => __("Checkout details", "aaapos"),
            "url" => wc_get_checkout_url(),
        ],
        3 => [
            "number" => "3",
            "label" => __("Order complete", "aaapos"),
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
    // Always show progress steps on cart page
    // This ensures proper layout even when transitioning from empty to non-empty
    if (is_cart()) {
        // Only skip if explicitly showing the empty cart template
        if (WC()->cart->is_empty() && !isset($_GET["add-to-cart"])) {
            return;
        }
        aaapos_checkout_progress_steps();
    }
}
add_action("woocommerce_before_cart", "aaapos_add_progress_to_cart", 5);

/**
 * Add progress steps to checkout page
 */
// function aaapos_add_progress_to_checkout()
// {
//     if (is_checkout() && !is_order_received_page()) {
//         aaapos_checkout_progress_steps();
//     }
// }
// add_action(
//     "woocommerce_before_checkout_form",
//     "aaapos_add_progress_to_checkout",
//     5,
// );

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
    $sale_badge_text = get_theme_mod("sale_badge_text", __("Sale", "aaapos"));
    $show_quick_view = get_theme_mod("show_quick_view", true);
    ?>
    
    <!-- Suggested Products Section with Wrapper -->
    <div class="cart-suggested-products-wrapper">
        <div class="cart-suggested-products">
            <div class="cart-suggested-products__header">
                <h2 class="cart-suggested-products__title"><?php esc_html_e(
                    "You May Also Like",
                    "aaapos",
                ); ?></h2>
                <p class="cart-suggested-products__subtitle"><?php esc_html_e(
                    "Customers who bought these items also bought",
                    "aaapos",
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
                                            __("Rated %s out of 5", "aaapos"),
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
                sprintf(__("Quick view %s", "aaapos"), $product->get_name()),
            ); ?>"
            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; color: #374151; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; width: 100%; margin-bottom: 0.875rem; line-height: 1; background: transparent; border: none;"
            onmouseover="this.style.color='var(--brand-color, #0ea5e9)'; this.style.transform='translateY(-2px)';"
            onmouseout="this.style.color='#374151'; this.style.transform='translateY(0)';">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span><?php esc_html_e("Quick View", "aaapos"); ?></span>
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
        <span><?php esc_html_e("Select options", "aaapos"); ?></span>
    </a>
<?php else: ?>
    <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>" 
       data-quantity="1" 
       class="button product_type_simple add_to_cart_button ajax_add_to_cart" 
       data-product_id="<?php echo esc_attr($product->get_id()); ?>" 
       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
       aria-label="<?php echo esc_attr(
           sprintf(__('Add "%s" to your cart', "aaapos"), $product->get_name()),
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

/**
 * Setup WooCommerce template path
 */
function aaapos_woocommerce_template_path()
{
    return "woocommerce/";
}
add_filter("woocommerce_template_path", "aaapos_woocommerce_template_path");

/**
 * Display Recommended Products on Empty Cart Page
 * Shows 4 popular or recent products when cart is empty
 * UPDATED: Full width layout to match viewport edges
 */
function aaapos_empty_cart_recommended_products()
{
    // Query for popular products (by sales) or recent products
    $args = [
        "post_type" => "product",
        "posts_per_page" => 4,
        "post_status" => "publish",
        "orderby" => "meta_value_num",
        "meta_key" => "total_sales",
        "order" => "DESC",
    ];

    $recommended_products = new WP_Query($args);

    // If no products with sales, get recent products
    if (!$recommended_products->have_posts()) {
        $args = [
            "post_type" => "product",
            "posts_per_page" => 4,
            "post_status" => "publish",
            "orderby" => "date",
            "order" => "DESC",
        ];
        $recommended_products = new WP_Query($args);
    }

    if (!$recommended_products->have_posts()) {
        return;
    }

    // Get customizer settings
    $show_rating = get_theme_mod("show_product_rating", true);
    $sale_badge_text = get_theme_mod(
        "sale_badge_text",
        __("Sale", "aaapos"),
    );
    $show_quick_view = get_theme_mod("show_quick_view", true);
    ?>
    
    <div class="cart-empty-recommended">
        <div class="cart-empty-recommended-inner">
            <div class="cart-empty-recommended__header">
                <span class="cart-empty-recommended__badge"><?php esc_html_e(
                    "START SHOPPING",
                    "aaapos",
                ); ?></span>
                <h2 class="cart-empty-recommended__title"><?php esc_html_e(
                    "Popular Products",
                    "aaapos",
                ); ?></h2>
                <p class="cart-empty-recommended__subtitle"><?php esc_html_e(
                    "Check out our most popular items to get started",
                    "aaapos",
                ); ?></p>
            </div>
            
            <ul class="products columns-4">
                <?php while ($recommended_products->have_posts()):

                    $recommended_products->the_post();
                    global $product;

                    // Get rating data
                    $average_rating = $product->get_average_rating();
                    $rating_count = $product->get_rating_count();
                    ?>
                
                <li <?php wc_product_class("", $product); ?>>
                    
                    <!-- Product Image Link -->
                    <a href="<?php echo esc_url(
                        $product->get_permalink(),
                    ); ?>" class="woocommerce-LoopProduct-link">
                        <?php echo $product->get_image(
                            "woocommerce_thumbnail",
                        ); ?>
                        
                        <?php if ($product->is_on_sale()): ?>
                            <span class="onsale"><?php echo esc_html(
                                $sale_badge_text,
                            ); ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Product Info -->
                    <div class="product-info">
                        <h2 class="woocommerce-loop-product__title">
                            <a href="<?php echo esc_url(
                                $product->get_permalink(),
                            ); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h2>
                        
                        <!-- Rating -->
                        <?php if ($show_rating && $average_rating > 0): ?>
                            <div class="product-rating">
                                <div class="rating-stars">
                                    <?php
                                    $gradient_id =
                                        "half-fill-empty-cart-" .
                                        $product->get_id();
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($average_rating)) {
                                            echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                        } elseif (
                                            $i == ceil($average_rating) &&
                                            $average_rating -
                                                floor($average_rating) >=
                                                0.5
                                        ) {
                                            echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><defs><linearGradient id="' .
                                                esc_attr($gradient_id) .
                                                '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' .
                                                esc_attr($gradient_id) .
                                                ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                        } else {
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
                    
                    <!-- Quick View Button -->
                    <?php if ($show_quick_view): ?>
                        <button type="button" class="quick-view-button" data-product-id="<?php echo esc_attr(
                            $product->get_id(),
                        ); ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; color: #374151; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; width: 100%; margin-bottom: 0.875rem; line-height: 1; background: transparent; border: none;" onmouseover="this.style.color='var(--brand-color, #0ea5e9)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.color='#374151'; this.style.transform='translateY(0)';">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span><?php esc_html_e(
                                "Quick View",
                                "aaapos",
                            ); ?></span>
                        </button>
                    <?php endif; ?>
                    
                    <!-- Add to Cart Button -->
                    <?php if ($product->is_type("variable")): ?>
                        <a href="<?php echo esc_url(
                            $product->get_permalink(),
                        ); ?>" class="button product_type_variable" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span><?php esc_html_e(
                                "Select options",
                                "aaapos",
                            ); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo esc_url(
                            "?add-to-cart=" . $product->get_id(),
                        ); ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr(
    $product->get_id(),
); ?>" data-product_sku="<?php echo esc_attr(
    $product->get_sku(),
); ?>" rel="nofollow" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span><?php echo esc_html(
                                $product->add_to_cart_text(),
                            ); ?></span>
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
