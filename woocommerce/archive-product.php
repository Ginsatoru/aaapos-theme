<?php
/**
 * The Template for displaying product archives
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 * 
 * LOCATION: /woocommerce/archive-product.php (NOT in templates folder!)
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 */
do_action('woocommerce_before_main_content');
?>

<div class="shop-page-wrapper">
    <div class="container-wide">
        
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <header class="woocommerce-products-header">
                <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
                
                <?php
                /**
                 * Hook: woocommerce_archive_description.
                 */
                do_action('woocommerce_archive_description');
                ?>
            </header>
        <?php endif; ?>

        <?php
        if (woocommerce_product_loop()) {

            /**
             * Hook: woocommerce_before_shop_loop.
             */
            ?>
            <div class="shop-toolbar">
                <?php do_action('woocommerce_before_shop_loop'); ?>
            </div>
            <?php

            woocommerce_product_loop_start();

            if (wc_get_loop_prop('total')) {
                while (have_posts()) {
                    the_post();

                    /**
                     * Hook: woocommerce_shop_loop.
                     */
                    do_action('woocommerce_shop_loop');

                    wc_get_template_part('content', 'product');
                }
            }

            woocommerce_product_loop_end();

            /**
             * Hook: woocommerce_after_shop_loop.
             */
            do_action('woocommerce_after_shop_loop');
        } else {
            /**
             * Hook: woocommerce_no_products_found.
             */
            do_action('woocommerce_no_products_found');
        }

        /**
         * Hook: woocommerce_after_main_content.
         */
        do_action('woocommerce_after_main_content');
        ?>
        
    </div><!-- .container-wide -->
</div><!-- .shop-page-wrapper -->

<?php

get_footer('shop');