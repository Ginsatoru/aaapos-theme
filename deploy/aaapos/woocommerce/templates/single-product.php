<?php
/**
 * The Template for displaying all single products
 * single-product.php
 * FIXED: Prevents "confirm form resubmission" warning on page refresh
 * 
 * @package AAAPOS_Prime
 * @version 1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('shop'); ?>

<main id="main" class="site-main single-product-page">
    
    <?php
    /**
     * Breadcrumb
     */
    do_action('woocommerce_before_main_content');
    ?>
    
    <div class="product-container">
        
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <?php wc_get_template_part('content', 'single-product'); ?>

        <?php endwhile; ?>
        
    </div>

    <?php
    do_action('woocommerce_after_main_content');
    ?>

</main>

<?php
get_footer('shop');