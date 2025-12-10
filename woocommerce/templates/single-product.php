<?php
/**
 * The Template for displaying all single products
 * 
 * This template uses a 71% centered container for a clean, focused layout
 * Fully supports variable products with enhanced variation selectors
 * 
 * @package AAAPOS_Prime
 * @version 1.0.0
 * 
 * Place this file in: /woocommerce/templates/single-product.php
 * OR in your theme root: /woocommerce/single-product.php
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop'); ?>

<main id="main" class="site-main single-product-page">
    
    <?php
    /**
     * woocommerce_before_main_content hook.
     *
     * @hooked woocommerce_output_content_wrapper - 10 (removed - we use custom wrapper)
     * @hooked woocommerce_breadcrumb - 20
     */
    do_action('woocommerce_before_main_content');
    ?>
    
    <div class="product-container">
        
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <?php wc_get_template_part('content', 'single-product'); ?>

        <?php endwhile; // end of the loop. ?>
        
    </div>

    <?php
    /**
     * woocommerce_after_main_content hook.
     *
     * @hooked woocommerce_output_content_wrapper_end - 10 (removed - we use custom wrapper)
     */
    do_action('woocommerce_after_main_content');
    ?>

</main>

<?php
get_footer('shop');