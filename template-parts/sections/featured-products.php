<?php
/**
 * Featured Products Section with Scroll Animations
 * Template: template-parts/sections/featured-products.php
 *
 * @package aaapos-prime
 */

// Check if WooCommerce is active
if (!function_exists("wc_get_products")) {
    return;
}

$title = get_theme_mod("featured_products_title", "Featured Products");
$description = get_theme_mod(
    "featured_products_description",
    "Browse our most popular pet food, animal feed, and farm supplies trusted by local pet owners and farmers",
);
$count = get_theme_mod("featured_products_count", 4);

// Get featured products
$products = wc_get_products([
    "status" => "publish",
    "limit" => $count,
    "visibility" => "visible",
    "featured" => true,
    "orderby" => "date",
    "order" => "DESC",
]);
?>

<section class="featured-products section">
    <div class="container">
        <!-- Section Header with Animation -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            
            <h2 class="section-title"><?php echo esc_html($title); ?></h2>
            
            <?php if ($description): ?>
                <p class="section-description"><?php echo esc_html(
                    $description,
                ); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php 
                $delay = 200; // Start delay for staggered animation
                foreach ($products as $product):
                    $product_id = $product->get_id();
                    $image = wp_get_attachment_image_src(
                        get_post_thumbnail_id($product_id),
                        "full",
                    );
                    $rating_count = $product->get_rating_count();
                    $average_rating = $product->get_average_rating();
                    $is_on_sale = $product->is_on_sale();
                    ?>
                    
                    <!-- Product Card with Staggered Animation -->
                    <div class="product-card" 
                         data-animate="fade-up" 
                         data-animate-delay="<?php echo esc_attr($delay); ?>"
                         data-product-id="<?php echo esc_attr($product_id); ?>">
                        
                        <!-- Product Image -->
                        <div class="product-image">
                            <a href="<?php echo esc_url(
                                $product->get_permalink(),
                            ); ?>">
                                <?php if ($image): ?>
                                    <img src="<?php echo esc_url(
                                        $image[0],
                                    ); ?>" 
                                         alt="<?php echo esc_attr(
                                             $product->get_name(),
                                         ); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <img src="<?php echo esc_url(
                                        wc_placeholder_img_src(),
                                    ); ?>" 
                                         alt="<?php echo esc_attr(
                                             $product->get_name(),
                                         ); ?>"
                                         loading="lazy">
                                <?php endif; ?>
                            </a>
                            
                            <!-- Badges -->
                            <?php if ($product->is_featured()): ?>
                                <span class="product-badge best-seller">Best Seller</span>
                            <?php endif; ?>
                            
                            <?php if ($is_on_sale):
                                $regular_price = (float) $product->get_regular_price();
                                $sale_price = (float) $product->get_sale_price();
                                if ($regular_price > 0) {
                                    $discount = round(
                                        (($regular_price - $sale_price) /
                                            $regular_price) *
                                            100,
                                    ); ?>
                                    <span class="product-badge sale-badge">Save <?php echo esc_html(
                                        $discount,
                                    ); ?>%</span>
                                <?php
                                }
                            endif; ?>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="product-info">
                            <!-- Star Rating -->
                            <?php if ($rating_count > 0): ?>
                                <div class="product-rating">
                                    <div class="star-rating">
                                        <?php
                                        $full_stars = floor($average_rating);
                                        $empty_stars = 5 - ceil($average_rating);

                                        // Full stars
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<span class="star">★</span>';
                                        }

                                        // Half star
                                        if ($average_rating - $full_stars >= 0.5) {
                                            echo '<span class="star">★</span>';
                                        }

                                        // Empty stars
                                        for ($i = 0; $i < $empty_stars; $i++) {
                                            echo '<span class="star empty">★</span>';
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-count">(<?php echo esc_html(
                                        $rating_count,
                                    ); ?>)</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Product Title -->
                            <h3 class="product-title">
                                <a href="<?php echo esc_url(
                                    $product->get_permalink(),
                                ); ?>">
                                    <?php echo esc_html(
                                        $product->get_name(),
                                    ); ?>
                                </a>
                            </h3>
                            
                            <!-- Product Short Description -->
                            <?php if ($product->get_short_description()): ?>
                                <p class="product-description">
                                    <?php echo wp_trim_words(
                                        $product->get_short_description(),
                                        15,
                                        "...",
                                    ); ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Product Price -->
                            <div class="product-price">
                                <?php if ($is_on_sale): ?>
                                    <span class="original-price">$<?php echo wp_kses_post(
                                        $product->get_regular_price(),
                                    ); ?></span>
                                    <span class="sale-price">$<?php echo wp_kses_post(
                                        $product->get_sale_price(),
                                    ); ?></span>
                                <?php else: ?>
                                    <span>$<?php echo wp_kses_post(
                                        $product->get_price(),
                                    ); ?></span>
                                <?php endif; ?>
                                <span class="unit">/<?php echo esc_html(
                                    $product->get_meta("_unit", true) ?: "bag",
                                ); ?></span>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <?php if (
                                $product->is_type("simple") &&
                                $product->is_purchasable() &&
                                $product->is_in_stock()
                            ): ?>
                                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" 
                                   class="add-to-cart-button add_to_cart_button ajax_add_to_cart"
                                   data-product_id="<?php echo esc_attr($product_id); ?>"
                                   data-product_name="<?php echo esc_attr($product->get_name()); ?>"
                                   data-quantity="1"
                                   aria-label="<?php echo esc_attr(
                                       sprintf(__("Add %s to cart", "aaapos-prime"), $product->get_name()),
                                   ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    <?php echo esc_html($product->add_to_cart_text()); ?>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url($product->get_permalink()); ?>" 
                                   class="add-to-cart-button"
                                   aria-label="<?php echo esc_attr(
                                       sprintf(__("View %s", "aaapos-prime"), $product->get_name()),
                                   ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    <?php echo esc_html($product->add_to_cart_text()); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php
                    $delay += 100; // Increment delay for stagger effect
                endforeach; ?>
            </div>
            
            <!-- View All Products Button with Animation -->
            <div class="section-footer" 
                 data-animate="fade-up" 
                 data-animate-delay="600">
                <a href="<?php echo esc_url(
                    get_permalink(wc_get_page_id("shop")),
                ); ?>" class="btn btn-outline">
                    <?php esc_html_e("View All Products", "aaapos-prime"); ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <div class="no-products-message" 
                 data-animate="fade-up">
                <p><?php esc_html_e(
                    "No featured products found.",
                    "aaapos-prime",
                ); ?></p>
                <?php if (current_user_can("manage_woocommerce")): ?>
                    <p class="admin-notice">
                        <small>
                            <?php printf(
                                esc_html__(
                                    "Admin: Mark products as featured in %sProducts → Edit Product → Product data → Catalog%s",
                                    "aaapos-prime",
                                ),
                                '<a href="' .
                                    esc_url(
                                        admin_url("edit.php?post_type=product"),
                                    ) .
                                    '" target="_blank">',
                                "</a>",
                            ); ?>
                        </small>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Hide "View cart" button in featured products section */
.featured-products .product-card .added_to_cart.wc-forward {
    display: none !important;
}
</style>