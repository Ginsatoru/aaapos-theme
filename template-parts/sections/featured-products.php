<?php
/**
 * Featured Products Section - Best Selling Products
 * UPDATED: Card redesigned to match the shop grid's "wbr-card" style -
 * image slideshow with dots, badge, short description, price pill,
 * pill-shaped button. Ratings dropped - no styled slot for them in the
 * new design (matches the shop grid card).
 * Template: template-parts/sections/featured-products.php
 *
 * @package aaapos-prime
 */

// Check if WooCommerce is active
if (!function_exists("wc_get_products")) {
    return;
}

$title = get_theme_mod("featured_products_title", "Best Selling Products");
$description = get_theme_mod(
    "featured_products_description",
);
$count = get_theme_mod("featured_products_count", 4);
$exclude_ids = get_theme_mod("featured_products_exclude", "");
$sale_badge_text = get_theme_mod("sale_badge_text", __("Sale", "aaapos-prime"));

// Parse excluded product IDs
$excluded_products = array();
if (!empty($exclude_ids)) {
    $excluded_products = array_map('intval', explode(',', $exclude_ids));
    $excluded_products = array_filter($excluded_products); // Remove empty values
}

// Build query args
$args = [
    "status" => "publish",
    "limit" => $count,
    "visibility" => "visible",
    "meta_key" => "total_sales",
    "orderby" => "meta_value_num",
    "order" => "DESC",
];

// Add exclusions if any
if (!empty($excluded_products)) {
    $args['exclude'] = $excluded_products;
}

// Get best-selling products (ordered by total sales)
$products = wc_get_products($args);
?>

<section class="featured-products section">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            
            <div class="section-header-text">
                <h2 class="section-title"><?php echo esc_html($title); ?></h2>
                
                <?php if ($description): ?>
                    <p class="section-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($products)): ?>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id("shop"))); ?>" class="btn btn-outline section-header-btn">
                    View All Products
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($products)): ?>
            <ul class="products products-grid">
                <?php 
                $delay = 200;
                foreach ($products as $product):
                    $wbr_slide_urls = aaapos_get_wbr_card_slide_urls($product);

                    $wbr_desc_source = $product->get_short_description();
                    if ("" === trim(wp_strip_all_tags($wbr_desc_source))) {
                        $wbr_desc_source = $product->get_description();
                    }
                    $wbr_card_desc = wp_trim_words(wp_strip_all_tags($wbr_desc_source), 16);
                    ?>
                    
                    <li class="product" 
                        data-animate="fade-up" 
                        data-animate-delay="<?php echo esc_attr($delay); ?>">

                        <div class="wbr-card">

                            <!-- Image slideshow -->
                            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="wbr-card__img-wrap">

                                <?php if ($product->is_on_sale()): ?>
                                    <span class="wbr-card__badge wbr-card__badge--sale"><?php echo esc_html($sale_badge_text); ?></span>
                                <?php endif; ?>

                                <div class="wbr-card__slides">
                                    <?php foreach ($wbr_slide_urls as $i => $img_url) : ?>
                                        <div class="wbr-card__slide<?php echo $i === 0 ? ' is-active' : ''; ?>">
                                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" />
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (count($wbr_slide_urls) > 1) : ?>
                                    <div class="wbr-card__dots">
                                        <?php foreach ($wbr_slide_urls as $i => $img_url) : ?>
                                            <span class="wbr-card__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            </a>

                            <!-- Body -->
                            <div class="wbr-card__body">

                                <h2 class="wbr-card__title">
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a>
                                </h2>

                                <?php if (!empty($wbr_card_desc)) : ?>
                                    <p class="wbr-card__desc"><?php echo esc_html($wbr_card_desc); ?></p>
                                <?php endif; ?>

                                <div class="wbr-card__footer">

                                    <span class="wbr-card__price"><?php echo $product->get_price_html(); ?></span>

                                    <?php if ($product->is_type("variable")): ?>
                                        <a href="<?php echo esc_url($product->get_permalink()); ?>"
                                           class="wbr-card__btn button product_type_variable">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                                            </svg>
                                            <span>Select options</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>"
                                           data-quantity="1"
                                           class="wbr-card__btn button product_type_simple add_to_cart_button ajax_add_to_cart"
                                           data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                                           data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                           aria-label="<?php echo esc_attr(
                                               sprintf(__('Add "%s" to your cart', "aaapos-prime"), $product->get_name()),
                                           ); ?>"
                                           rel="nofollow">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                                            </svg>
                                            <span><?php echo esc_html($product->add_to_cart_text()); ?></span>
                                        </a>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </li>
                    
                <?php
                    $delay += 100;
                endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="no-products-message">
                <p>No best-selling products found.</p>
            </div>
        <?php endif; ?>
    </div>
</section>