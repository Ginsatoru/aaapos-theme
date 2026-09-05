<?php
/**
 * The template for displaying product content within loops
 *
 * UPDATED: Quick View button removed completely
 *
 * @package AAAPOS_Prime
 * @version 1.1.0
 */

defined("ABSPATH") || exit();

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get customizer settings
$show_rating = get_theme_mod("show_product_rating", true);
$sale_badge_text = get_theme_mod("sale_badge_text", __("Sale", "aaapos-prime"));

// Get rating data
$average_rating = $product->get_average_rating();
$rating_count = $product->get_rating_count();
?>
<li <?php wc_product_class("", $product); ?>>
    
    <!-- Product Image Link (Image + Badge ONLY) -->
    <a href="<?php echo esc_url(
        get_permalink(),
    ); ?>" class="woocommerce-LoopProduct-link">
        
        <!-- Product Image -->
        <?php echo $product->get_image("woocommerce_thumbnail"); ?>
        
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
            <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h2>
        
        <!-- Star Rating Section (Conditional based on Customizer) -->
        <?php if ($show_rating && $average_rating > 0): ?>
            <div class="product-rating">
                <div class="rating-stars" aria-label="<?php echo esc_attr(
                    sprintf(
                        __("Rated %s out of 5", "aaapos-prime"),
                        number_format($average_rating, 2),
                    ),
                ); ?>">
                    <?php
                    // Generate unique ID for gradient
                    $gradient_id = "half-fill-" . $product->get_id();

                    // Display 5 stars
                    for ($i = 1; $i <= 5; $i++) {
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
        <span><?php esc_html_e("Select options", "aaapos-prime"); ?></span>
    </a>
<?php else: ?>
    <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>" 
       data-quantity="1" 
       class="button product_type_simple add_to_cart_button ajax_add_to_cart" 
       data-product_id="<?php echo esc_attr($product->get_id()); ?>" 
       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
       aria-label="<?php echo esc_attr(
           sprintf(
               __('Add "%s" to your cart', "aaapos-prime"),
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

<style>
/* ============================================
   ADD TO CART BUTTON - FULL WIDTH
   Isolated to product cards only
   (Quick View button and its styles removed)
   ============================================ */
.woocommerce ul.products li.product .button.add_to_cart_button {
    width: 100%;
    margin: 0;
    box-sizing: border-box;
}
</style>