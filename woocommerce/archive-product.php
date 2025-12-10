<?php
/**
 * The Template for displaying product archives
 * 
 * NOW SUPPORTS SIDEBAR FROM CUSTOMIZER SETTINGS
 * 
 * @package AAAPOS_Prime
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 */
do_action('woocommerce_before_main_content');

// Check if sidebar should be shown
$show_sidebar = get_theme_mod('show_shop_sidebar', false) && is_active_sidebar('shop-sidebar');
$container_class = $show_sidebar ? 'has-sidebar' : 'no-sidebar';
?>

<div class="shop-page-wrapper <?php echo esc_attr($container_class); ?>">
    <div class="container-wide">
        
        <div class="shop-content-area">
            
            <!-- Main Shop Content -->
            <div class="shop-main-content">
                
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
                
            </div><!-- .shop-main-content -->
            
            <!-- Shop Sidebar (Conditional) -->
            <?php if ($show_sidebar) : ?>
                <aside class="shop-sidebar" role="complementary" aria-label="<?php esc_attr_e('Shop Sidebar', 'aaapos-prime'); ?>">
                    <?php dynamic_sidebar('shop-sidebar'); ?>
                </aside>
            <?php endif; ?>
            
        </div><!-- .shop-content-area -->
        
    </div><!-- .container-wide -->
</div><!-- .shop-page-wrapper -->

<?php

get_footer('shop');