<?php
/**
 * Single Product Sale Flash Override
 * 
 * This template overrides WooCommerce's default sale badge on SINGLE PRODUCT pages only.
 * The sale badge will still appear on shop/archive/category pages (handled in content-product.php).
 * 
 * To show/hide the badge, simply:
 * - Keep this file to HIDE the badge on single product pages
 * - Delete this file to SHOW the badge on single product pages
 * 
 * @package AAAPOS_Prime
 * @version 1.0.0
 * 
 * Location: woocommerce/single-product/sale-flash.php
 * 
 * IMPORTANT: This overrides WooCommerce's template:
 * woocommerce/templates/single-product/sale-flash.php
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Return nothing - this completely hides the sale badge on single product pages
return;

/**
 * OPTIONAL: Conditional Display
 * 
 * If you want to show the badge based on customizer settings in the future,
 * uncomment the code below and add the setting to your customizer:
 */

/*
global $post, $product;

// Check if we should show the badge (add this setting to your customizer)
$show_single_sale_badge = get_theme_mod('show_single_product_sale_badge', false);

if (!$show_single_sale_badge) {
    return; // Hide if setting is false
}

// Get custom badge text from customizer
$sale_badge_text = get_theme_mod('sale_badge_text', __('Sale', 'aaapos-prime'));

// Display the badge if product is on sale
if ($product && $product->is_on_sale()) : ?>
    <span class="onsale"><?php echo esc_html($sale_badge_text); ?></span>
<?php endif;
*/