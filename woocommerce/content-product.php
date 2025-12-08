<?php
/**
 * The template for displaying product content within loops
 * 
 * FULLY FIXED VERSION - Perfect structure for star ratings and pricing
 * Place this file in: /woocommerce/content-product.php
 * 
 * @package AAAPOS_Prime
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get rating data
$average_rating = $product->get_average_rating();
$rating_count = $product->get_rating_count();
?>
<li <?php wc_product_class('', $product); ?>>
    
    <!-- Product Image Link (Image + Badge ONLY) -->
    <a href="<?php echo esc_url(get_permalink()); ?>" class="woocommerce-LoopProduct-link">
        
        <!-- Product Image -->
        <?php echo $product->get_image('woocommerce_thumbnail'); ?>
        
        <!-- Sale Badge -->
        <?php if ($product->is_on_sale()) : ?>
            <span class="onsale"><?php esc_html_e('Sale!', 'aaapos-prime'); ?></span>
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
        
        <!-- Star Rating Section -->
        <?php if ($average_rating > 0) : ?>
            <div class="product-rating">
                <div class="rating-stars" aria-label="<?php echo esc_attr(sprintf(__('Rated %s out of 5', 'aaapos-prime'), number_format($average_rating, 2))); ?>">
                    <?php
                    // Generate unique ID for gradient
                    $gradient_id = 'half-fill-' . $product->get_id();
                    
                    // Display 5 stars
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($average_rating)) {
                            // Full star
                            echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                        } elseif ($i == ceil($average_rating) && ($average_rating - floor($average_rating)) >= 0.5) {
                            // Half star
                            echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><defs><linearGradient id="' . esc_attr($gradient_id) . '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' . esc_attr($gradient_id) . ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                        } else {
                            // Empty star
                            echo '<svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#d1d5db"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                        }
                    }
                    ?>
                </div>
                <?php if ($rating_count > 0) : ?>
                    <span class="rating-count">(<?php echo esc_html($rating_count); ?>)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Price -->
        <div class="product-price-wrapper">
            <?php echo $product->get_price_html(); ?>
        </div>
        
    </div>
    
    <!-- Add to Cart Button -->
    <?php woocommerce_template_loop_add_to_cart(); ?>
    
</li>