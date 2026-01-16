<?php
/**
 * Single Product Sale Flash Override
 * 
 * This template displays a sale badge with percentage off on SINGLE PRODUCT pages.
 * Shows "SALE" text with the discount percentage.
 * 
 * @package AAAPOS_Prime
 * @version 1.0.1
 * 
 * Location: woocommerce/single-product/sale-flash.php
 * 
 * UPDATED: Now shows sale badge with percentage off
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

// Only show if product exists and is on sale
if (!$product || !$product->is_on_sale()) {
    return;
}

// Calculate percentage off
$percentage = 0;

if ($product->is_type('variable')) {
    // For variable products, get the maximum discount
    $variations = $product->get_available_variations();
    $max_percentage = 0;
    
    foreach ($variations as $variation) {
        $variation_obj = wc_get_product($variation['variation_id']);
        if ($variation_obj->is_on_sale()) {
            $regular_price = (float) $variation_obj->get_regular_price();
            $sale_price = (float) $variation_obj->get_sale_price();
            
            if ($regular_price > 0) {
                $variation_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                $max_percentage = max($max_percentage, $variation_percentage);
            }
        }
    }
    
    $percentage = $max_percentage;
} else {
    // For simple products
    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();
    
    if ($regular_price > 0 && $sale_price > 0) {
        $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
    }
}

// Only display if we have a valid percentage
if ($percentage > 0) : ?>
    <span class="onsale">
        <span class="sale-text"><?php esc_html_e('SALE', 'aaapos'); ?></span>
        <span class="sale-percentage">-<?php echo esc_html($percentage); ?>%</span>
    </span>
<?php endif;