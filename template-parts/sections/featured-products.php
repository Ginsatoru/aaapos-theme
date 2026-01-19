<?php
/**
 * Featured Products Section - Best Selling Products
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
    "Browse our most popular pet food, animal feed, and farm supplies trusted by local pet owners and farmers",
);
$count = get_theme_mod("featured_products_count", 4);

// Get best-selling products (ordered by total sales)
$products = wc_get_products([
    "status" => "publish",
    "limit" => $count,
    "visibility" => "visible",
    "meta_key" => "total_sales",
    "orderby" => "meta_value_num",
    "order" => "DESC",
]);

// Get customizer settings
$show_rating = get_theme_mod("show_product_rating", true);
$show_quick_view = get_theme_mod("show_quick_view", true);
?>

<section class="featured-products section">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            
            <h2 class="section-title"><?php echo esc_html($title); ?></h2>
            
            <?php if ($description): ?>
                <p class="section-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($products)): ?>
            <ul class="products products-grid">
                <?php 
                $delay = 200;
                foreach ($products as $product):
                    $product_id = $product->get_id();
                    $image = wp_get_attachment_image_src(
                        get_post_thumbnail_id($product_id),
                        "full",
                    );
                    $rating_count = $product->get_rating_count();
                    $average_rating = $product->get_average_rating();
                    $is_on_sale = $product->is_on_sale();
                    $gradient_id = "half-fill-" . $product_id;
                    ?>
                    
                    <li class="product" 
                        data-animate="fade-up" 
                        data-animate-delay="<?php echo esc_attr($delay); ?>">
                        
                        <!-- Product Image Link -->
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" 
                           class="woocommerce-LoopProduct-link">
                            
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image[0]); ?>" 
                                     alt="<?php echo esc_attr($product->get_name()); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" 
                                     alt="<?php echo esc_attr($product->get_name()); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                            
                            <?php if ($is_on_sale): ?>
                                <span class="onsale">SALE!</span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- Product Info -->
                        <div class="product-info">
                            
                            <!-- Product Title -->
                            <h2 class="woocommerce-loop-product__title">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                            </h2>
                            
                            <!-- Star Rating -->
                            <?php if ($show_rating && $average_rating > 0): ?>
                                <div class="product-rating">
                                    <div class="rating-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($average_rating)) {
                                                echo '<svg class="star star-full" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            } elseif (
                                                $i == ceil($average_rating) &&
                                                $average_rating - floor($average_rating) >= 0.5
                                            ) {
                                                echo '<svg class="star star-half" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><defs><linearGradient id="' . esc_attr($gradient_id) . '"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#d1d5db" stop-opacity="1"/></linearGradient></defs><path fill="url(#' . esc_attr($gradient_id) . ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            } else {
                                                echo '<svg class="star star-empty" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#d1d5db"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php if ($rating_count > 0): ?>
                                        <span class="rating-count">(<?php echo esc_html($rating_count); ?>)</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Price -->
                            <div class="product-price-wrapper">
                                <span class="price"><?php echo $product->get_price_html(); ?></span>
                            </div>
                            
                        </div>
                        
                        <!-- Quick View Button -->
                        <?php if ($show_quick_view): ?>
                            <button type="button" 
                                    class="quick-view-button" 
                                    data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                                    aria-label="<?php echo esc_attr(
                                        sprintf(__("Quick view %s", "aaapos-prime"), $product->get_name()),
                                    ); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span>Quick View</span>
                            </button>
                        <?php endif; ?>
                        
                        <!-- Add to Cart Button -->
                        <?php if ($product->is_type("variable")): ?>
                            <a href="<?php echo esc_url($product->get_permalink()); ?>" 
                               class="button product_type_variable">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span>Select options</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>" 
                               data-quantity="1" 
                               class="button product_type_simple add_to_cart_button ajax_add_to_cart" 
                               data-product_id="<?php echo esc_attr($product->get_id()); ?>" 
                               data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
                               aria-label="<?php echo esc_attr(
                                   sprintf(__('Add "%s" to your cart', "aaapos-prime"), $product->get_name()),
                               ); ?>" 
                               rel="nofollow">
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
                    $delay += 100;
                endforeach; ?>
            </ul>
            
            <!-- View All Products Button -->
            <div class="section-footer" 
                 data-animate="fade-up" 
                 data-animate-delay="600">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id("shop"))); ?>" class="btn btn-outline">
                    View All Products
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <div class="no-products-message">
                <p>No best-selling products found.</p>
            </div>
        <?php endif; ?>
    </div>
</section>