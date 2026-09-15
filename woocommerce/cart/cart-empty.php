<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * @package Macedon_Ranges
 */

defined( 'ABSPATH' ) || exit;

// Add body class for empty cart styling
add_filter('body_class', function($classes) {
    $classes[] = 'cart-is-empty';
    return $classes;
});

/**
 * Removed the default WooCommerce empty cart message hook
 * to prevent the notification box from showing
 */
// do_action( 'woocommerce_cart_is_empty' );
?>

<div class="woocommerce-cart-form">
	<?php if ( wc_get_page_permalink( 'shop' ) ) : ?>
		
		<!-- EMPTY CART STATE - Centered Design -->
		<div class="cart-empty-wrapper">
			<div class="cart-empty-content">
				<!-- Shopping Cart Icon -->
				<div class="cart-empty-icon">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/images/cart.png' ); ?>" alt="" width="64" height="64" />
				</div>
				
				<!-- Main Heading -->
				<h2 class="cart-empty-title"><?php esc_html_e( 'Your cart is empty', 'macedon-ranges' ); ?></h2>
				
				<!-- Subtitle -->
				<p class="cart-empty-subtitle"><?php esc_html_e( 'Looks like you haven\'t added anything to your cart yet. Start shopping to fill it up!', 'macedon-ranges' ); ?></p>
				
				<!-- Call to Action Button -->
				<div class="cart-empty-actions">
					<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="cart-empty-button">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
							<polyline points="9 22 9 12 15 12 15 22"></polyline>
						</svg>
						<?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to Shop', 'macedon-ranges' ) ) ); ?>
					</a>
				</div>
			</div>
		</div>

	<?php endif; ?>
</div>

<?php
/**
 * Display Recommended Products on Empty Cart
 */
if ( function_exists( 'aaapos_empty_cart_recommended_products' ) ) {
    aaapos_empty_cart_recommended_products();
} else {
    // Fallback: Display products directly if function doesn't exist
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    $products = new WP_Query( $args );
    
    if ( $products->have_posts() ) : ?>
        <div class="cart-empty-recommended">
            <div class="container">
                <div class="cart-empty-recommended__header">
                    <span class="cart-empty-recommended__badge">START SHOPPING</span>
                    <h2 class="cart-empty-recommended__title">Popular Products</h2>
                    <p class="cart-empty-recommended__subtitle">Check out our most popular items to get started</p>
                </div>
                
                <ul class="products columns-4">
                    <?php while ( $products->have_posts() ) : $products->the_post(); 
                        wc_get_template_part( 'content', 'product' );
                    endwhile; ?>
                </ul>
            </div>
        </div>
    <?php 
    endif;
    wp_reset_postdata();
}
?>