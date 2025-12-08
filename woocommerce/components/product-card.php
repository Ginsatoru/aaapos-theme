<?php
/**
 * Product Card Component - WORKING VERSION
 * This outputs ALL product information correctly
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

// Ensure we have a valid product
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<li <?php wc_product_class('product', $product); ?>>
    
    <!-- Product Image Link -->
    <a href="<?php echo esc_url(get_permalink()); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
        <?php echo $product->get_image('woocommerce_thumbnail'); ?>
        
        <!-- Sale Badge -->
        <?php if ($product->is_on_sale()) : ?>
            <span class="onsale"><?php esc_html_e('Sale!', 'aaapos-prime'); ?></span>
        <?php endif; ?>
    </a>
    
    <!-- Product Info -->
    <div class="product-info">
        
        <!-- Product Title -->
        <h2 class="woocommerce-loop-product__title">
            <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h2>
        
        <!-- Star Rating -->
        <?php if ($rating_html = wc_get_rating_html($product->get_average_rating())) : ?>
            <div class="star-rating-wrapper">
                <?php echo $rating_html; ?>
            </div>
        <?php endif; ?>
        
        <!-- Price -->
        <?php echo $product->get_price_html(); ?>
        
        <!-- Add to Cart Button -->
        <?php
        woocommerce_template_loop_add_to_cart();
        ?>
        
    </div>
    
</li>