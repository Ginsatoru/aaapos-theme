<?php
/**
 * Special Deals Section - Auto-rotates through products with scheduled sales
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

// Query for ALL products with ACTIVE scheduled sales
$current_time = current_time('timestamp');

$args = [
    'post_type' => 'product',
    'posts_per_page' => -1, // Get ALL products (changed from 1)
    'post_status' => 'publish',
    'meta_query' => [
        'relation' => 'AND',
        // Must have a sale price
        [
            'key' => '_sale_price',
            'value' => '',
            'compare' => '!=',
        ],
        // Must be in stock
        [
            'key' => '_stock_status',
            'value' => 'instock',
        ],
        // Must have sale start date (scheduled sale)
        [
            'key' => '_sale_price_dates_from',
            'value' => '',
            'compare' => '!=',
        ],
        // Sale must have started
        [
            'key' => '_sale_price_dates_from',
            'value' => $current_time,
            'compare' => '<=',
            'type' => 'NUMERIC'
        ],
        // Must have sale end date
        [
            'key' => '_sale_price_dates_to',
            'value' => '',
            'compare' => '!=',
        ],
        // Sale must not have ended yet
        [
            'key' => '_sale_price_dates_to',
            'value' => $current_time,
            'compare' => '>=',
            'type' => 'NUMERIC'
        ],
    ],
    'orderby' => 'meta_value_num',
    'meta_key' => '_sale_price_dates_to',
    'order' => 'ASC', // Shows the sale ending soonest first
];

$sale_query = new WP_Query($args);

// Build array of deal products
$deal_products = [];
if ($sale_query->have_posts()) {
    while ($sale_query->have_posts()) {
        $sale_query->the_post();
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);
        
        // Only add if product is valid and visible
        if ($product && is_a($product, "WC_Product") && $product->is_visible() && $product->is_in_stock()) {
            $sale_end_timestamp = get_post_meta($product_id, '_sale_price_dates_to', true);
            
            $deal_products[] = [
                'product' => $product,
                'end_date' => $sale_end_timestamp ? date('Y-m-d H:i:s', $sale_end_timestamp) : '',
            ];
        }
    }
}
wp_reset_postdata();

$has_valid_deals = !empty($deal_products);
$total_deals = count($deal_products);
?>

<section class="special-deals-section" style="background-image: url('<?php echo esc_url($bg_image_url); ?>') !important; --overlay-opacity: <?php echo esc_attr($overlay_opacity); ?>;">
    <div class="special-deals-container">
        
        <?php if ($has_valid_deals): ?>
            
            <!-- Section Header -->
            <div class="special-deals-header">
                <h2 class="special-deals-title"><?php echo esc_html($title); ?></h2>
                <?php if (!empty($description)): ?>
                    <p class="special-deals-subtitle"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>

            <!-- Deals Slider Wrapper -->
            <div class="deals-slider-wrapper" data-total-deals="<?php echo esc_attr($total_deals); ?>">
                
                <?php foreach ($deal_products as $index => $deal_data): 
                    $deal_product = $deal_data['product'];
                    $deal_end_date = $deal_data['end_date'];
                    $is_active = $index === 0 ? 'active' : '';
                ?>
                
                <!-- Deal Card -->
                <div class="special-deal-card <?php echo esc_attr($is_active); ?>" data-deal-index="<?php echo esc_attr($index); ?>">
                    
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
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                        <line x1="3" y1="6" x2="21" y2="6"/>
                                        <path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                    <?php esc_html_e("Add to Cart", "AAAPOS"); ?>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo esc_url(
                                $deal_product->get_permalink(),
                            ); ?>" class="deal-btn deal-btn-secondary">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <?php esc_html_e("View Details", "AAAPOS"); ?>
                            </a>
                        </div>
                        
                    </div>
                    
                </div>
                
                <?php endforeach; ?>
                
                <!-- Navigation Controls (only show if multiple deals) -->
                <?php if ($total_deals > 1): ?>
                    
                    <!-- Navigation Dots -->
                    <div class="deals-nav-dots">
                        <?php for ($i = 0; $i < $total_deals; $i++): ?>
                            <button class="deals-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                                    data-slide-to="<?php echo esc_attr($i); ?>"
                                    aria-label="<?php echo esc_attr(sprintf(__('Go to deal %d', 'AAAPOS'), $i + 1)); ?>">
                            </button>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <div class="deals-nav-arrows">
                        <button class="deals-arrow deals-arrow-prev" aria-label="<?php esc_attr_e('Previous deal', 'AAAPOS'); ?>">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button class="deals-arrow deals-arrow-next" aria-label="<?php esc_attr_e('Next deal', 'AAAPOS'); ?>">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    
                <?php endif; ?>
                
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