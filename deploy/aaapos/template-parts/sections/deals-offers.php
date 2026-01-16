<?php
/**
 * Special Deals Section - Redesigned with Full Width Background
 *
 * @package AAAPOS
 */

// Don't display if not on front page OR if section is disabled
if (!is_front_page() || !get_theme_mod("show_deals", true)) {
    return;
}

// Get customizer settings
$title = get_theme_mod("deals_title", "Special Deals");
$description = get_theme_mod("deals_description", "Limited offer!");
$deal_end_date = get_theme_mod("deal_end_date", "");
$selection_method = get_theme_mod("deal_selection_method", "manual");

// Get background image URL - Customizer with fallback
$custom_bg = get_theme_mod('deals_background_image', '');
if (!empty($custom_bg)) {
    $bg_image_url = esc_url($custom_bg);
} else {
    // Fallback to default image
    $bg_image_url = get_template_directory_uri() . '/assets/images/deal.png';
}

// Get overlay opacity
$overlay_opacity = get_theme_mod('deals_overlay_opacity', 0.6);

// Initialize product variable
$deal_product = null;

// Get product based on selection method
switch ($selection_method) {
    case "manual":
        // Manual selection by Product ID
        $deal_product_id = get_theme_mod("deal_product_id", 0);
        if (
            $deal_product_id &&
            is_numeric($deal_product_id) &&
            $deal_product_id > 0
        ) {
            $deal_product = wc_get_product($deal_product_id);
        }
        break;

    case "latest_sale":
        // Automatically get the latest product on sale
        $args = [
            "post_type" => "product",
            "posts_per_page" => 1,
            "post_status" => "publish",
            "meta_query" => [
                "relation" => "AND",
                [
                    "key" => "_sale_price",
                    "value" => "",
                    "compare" => "!=",
                ],
                [
                    "key" => "_stock_status",
                    "value" => "instock",
                ],
            ],
            "orderby" => "date",
            "order" => "DESC",
        ];
        $sale_query = new WP_Query($args);
        if ($sale_query->have_posts()) {
            $deal_product = wc_get_product($sale_query->posts[0]->ID);
        }
        wp_reset_postdata();
        break;

    case "featured_sale":
        // Get a featured product that's on sale
        $args = [
            "post_type" => "product",
            "posts_per_page" => 1,
            "post_status" => "publish",
            "tax_query" => [
                [
                    "taxonomy" => "product_visibility",
                    "field" => "name",
                    "terms" => "featured",
                ],
            ],
            "meta_query" => [
                "relation" => "AND",
                [
                    "key" => "_sale_price",
                    "value" => "",
                    "compare" => "!=",
                ],
                [
                    "key" => "_stock_status",
                    "value" => "instock",
                ],
            ],
            "orderby" => "date",
            "order" => "DESC",
        ];
        $featured_query = new WP_Query($args);
        if ($featured_query->have_posts()) {
            $deal_product = wc_get_product($featured_query->posts[0]->ID);
        }
        wp_reset_postdata();
        break;
}

// Check if we have a valid, visible product
$has_valid_deal =
    $deal_product &&
    is_a($deal_product, "WC_Product") &&
    $deal_product->is_visible() &&
    $deal_product->is_in_stock();
?>

<section class="special-deals-section" style="background-image: url('<?php echo esc_url($bg_image_url); ?>') !important; --overlay-opacity: <?php echo esc_attr($overlay_opacity); ?>;">
    <div class="special-deals-container">
        
        <?php if ($has_valid_deal): ?>
            
            <!-- Section Header -->
            <div class="special-deals-header">
                <h2 class="special-deals-title"><?php echo esc_html($title); ?></h2>
                <?php if (!empty($description)): ?>
                    <p class="special-deals-subtitle"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>

            <!-- Deal Card -->
            <div class="special-deal-card">
                
                <!-- Product Image -->
                <div class="deal-card-image">
                    <?php if ($deal_product->get_image_id()) {
                        echo $deal_product->get_image("large");
                    } else {
                        echo wc_placeholder_img("large");
                    } ?>
                    
                    <?php if ($deal_product->is_on_sale()): ?>
                        <span class="deal-badge">
                            <?php
                            $percentage = "";
                            if (
                                $deal_product->get_regular_price() &&
                                $deal_product->get_sale_price()
                            ) {
                                $percentage = round(
                                    (($deal_product->get_regular_price() -
                                        $deal_product->get_sale_price()) /
                                        $deal_product->get_regular_price()) *
                                        100,
                                );
                                echo sprintf(
                                    esc_html__("SAVE %s%%", "AAAPOS"),
                                    $percentage,
                                );
                            } else {
                                esc_html_e("SALE!", "AAAPOS");
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Product Details -->
                <div class="deal-card-content">
                    
                    <h3 class="deal-product-title">
                        <?php echo esc_html($deal_product->get_name()); ?>
                    </h3>
                    
                    <?php if ($deal_product->get_rating_count() > 0): ?>
                        <div class="deal-rating">
                            <?php 
                            $rating = $deal_product->get_average_rating();
                            $full_stars = floor($rating);
                            $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
                            $empty_stars = 5 - $full_stars - $half_star;
                            ?>
                            <div class="deal-stars">
                                <?php for ($i = 0; $i < $full_stars; $i++): ?>
                                    <svg class="star-full" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                <?php endfor; ?>
                                <?php if ($half_star): ?>
                                    <svg class="star-half" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77V2z"/>
                                    </svg>
                                <?php endif; ?>
                                <?php for ($i = 0; $i < $empty_stars; $i++): ?>
                                    <svg class="star-empty" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text">
                                <?php echo number_format($rating, 1); ?> (<?php echo $deal_product->get_rating_count(); ?>)
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="deal-price">
                        <?php if ($deal_product->is_on_sale()): ?>
                            <span class="price-current"><?php echo wc_price(
                                $deal_product->get_sale_price(),
                            ); ?></span>
                            <span class="price-original"><?php echo wc_price(
                                $deal_product->get_regular_price(),
                            ); ?></span>
                            <?php
                            $saved =
                                $deal_product->get_regular_price() -
                                $deal_product->get_sale_price();
                            if ($saved > 0): ?>
                                <span class="price-saved">
                                    <?php printf(
                                        esc_html__(
                                            "You save: %s",
                                            "AAAPOS",
                                        ),
                                        wc_price($saved),
                                    ); ?>
                                </span>
                            <?php endif;
                            ?>
                        <?php else: ?>
                            <span class="price-current"><?php echo $deal_product->get_price_html(); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php
                    // Display stock status
                    $stock_status = $deal_product->get_stock_status();
                    $stock_quantity = $deal_product->get_stock_quantity();
                    ?>
                    <div class="deal-stock">
                        <?php if ($stock_status === "instock"): ?>
                            <span class="stock-badge stock-in">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <?php esc_html_e("In Stock", "AAAPOS"); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($deal_end_date)): ?>
                        <div class="deal-countdown">
                            <h4 class="countdown-title"><?php esc_html_e(
                                "Offer ends in:",
                                "AAAPOS",
                            ); ?></h4>
                            <div class="countdown-timer" data-end-date="<?php echo esc_attr(
                                $deal_end_date,
                            ); ?>">
                                <div class="countdown-item">
                                    <span class="countdown-value days">00</span>
                                    <span class="countdown-label"><?php esc_html_e(
                                        "DAYS",
                                        "AAAPOS",
                                    ); ?></span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-value hours">00</span>
                                    <span class="countdown-label"><?php esc_html_e(
                                        "HOURS",
                                        "AAAPOS",
                                    ); ?></span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-value minutes">00</span>
                                    <span class="countdown-label"><?php esc_html_e(
                                        "MINUTES",
                                        "AAAPOS",
                                    ); ?></span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-value seconds">00</span>
                                    <span class="countdown-label"><?php esc_html_e(
                                        "SECONDS",
                                        "AAAPOS",
                                    ); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="deal-actions">
                        <?php if (
                            $deal_product->is_purchasable() &&
                            $deal_product->is_in_stock()
                        ): ?>
                            <a href="<?php echo esc_url(
                                $deal_product->add_to_cart_url(),
                            ); ?>" 
                               class="deal-btn deal-btn-primary"
                               data-product-id="<?php echo esc_attr(
                                   $deal_product->get_id(),
                               ); ?>">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <?php esc_html_e("Add to Cart", "AAAPOS"); ?>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(
                            $deal_product->get_permalink(),
                        ); ?>" class="deal-btn deal-btn-secondary">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <?php esc_html_e("View Details", "AAAPOS"); ?>
                        </a>
                    </div>
                    
                </div>
                
            </div>
            
        <?php else: ?>
            
            <!-- No Deals Available -->
            <div class="no-deals">
                <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3><?php esc_html_e(
                    "No Special Deals Available",
                    "AAAPOS",
                ); ?></h3>
                <p><?php esc_html_e(
                    "Check back soon for amazing deals and special offers!",
                    "AAAPOS",
                ); ?></p>
                <a href="<?php echo esc_url(
                    wc_get_page_permalink("shop"),
                ); ?>" class="deal-btn deal-btn-primary">
                    <?php esc_html_e("Browse All Products", "AAAPOS"); ?>
                </a>
            </div>
            
        <?php endif; ?>
        
    </div>
</section>