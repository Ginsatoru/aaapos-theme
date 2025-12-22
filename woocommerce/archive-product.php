<?php
/**
 * The Template for displaying product archives
 * WITH ENHANCED HEADER & CATEGORY FILTER
 * UPDATED: Uses fallback background image function
 * 
 * @package AAAPOS_Prime
 * @version 3.0.1
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

// Check if category filter is enabled
$show_category_filter = get_theme_mod('enable_category_filter', true);

// Get shop header customizer settings with fallback
$header_bg_image = aaapos_get_shop_header_bg_image(); // Uses new function with fallback
$header_title = get_theme_mod('shop_header_title', 'Shop');
$header_subtitle = get_theme_mod('shop_header_subtitle', 'Evoke emotion, highlight artisan quality, create a unique experience.');
?>

<div class="shop-page-wrapper <?php echo esc_attr($container_class); ?>">
    <div class="container-wide">
        
        <div class="shop-content-area">
            
            <!-- Main Shop Content -->
            <div class="shop-main-content">
                
                <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                    <header class="woocommerce-products-header <?php echo !empty($header_bg_image) ? 'has-background-image' : ''; ?>"
                            <?php if (!empty($header_bg_image)) : ?>
                                style="--shop-header-bg-image: url('<?php echo esc_url($header_bg_image); ?>');"
                            <?php endif; ?>>
                        
                        <div class="woocommerce-products-header__inner">
                            <h1 class="woocommerce-products-header__title page-title">
                                <?php echo esc_html($header_title); ?>
                            </h1>
                            
                            <?php if (!empty($header_subtitle)) : ?>
                                <div class="woocommerce-archive-description">
                                    <p><?php echo wp_kses_post($header_subtitle); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </header>
                <?php endif; ?>

                <?php
                if (woocommerce_product_loop()) {

                    /**
                     * Shop Toolbar with Column Toggle
                     */
                    ?>
                    <div class="shop-toolbar">
                        
                        <!-- Result Count -->
                        <div class="woocommerce-result-count">
                            <?php
                            $total = wc_get_loop_prop('total');
                            printf(
                                esc_html__('Showing all %d results', 'aaapos-prime'),
                                $total
                            );
                            ?>
                        </div>
                        
                        <!-- Sorting & Column Controls -->
                        <div class="toolbar-controls">
                            
                            <!-- Sort By Dropdown -->
                            <?php woocommerce_catalog_ordering(); ?>
                            
                            <!-- Column Toggle (2, 3, 4 columns) -->
                            <div class="column-toggle-wrapper">
                                <!-- 2 Columns Button -->
                                <button 
                                    type="button" 
                                    class="column-toggle" 
                                    data-columns="2" 
                                    data-tooltip="<?php esc_attr_e('2 columns', 'aaapos-prime'); ?>"
                                    aria-label="<?php esc_attr_e('2 columns view', 'aaapos-prime'); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <rect x="3" y="3" width="8" height="8" rx="1"></rect>
                                        <rect x="13" y="3" width="8" height="8" rx="1"></rect>
                                        <rect x="3" y="13" width="8" height="8" rx="1"></rect>
                                        <rect x="13" y="13" width="8" height="8" rx="1"></rect>
                                    </svg>
                                </button>
                                
                                <!-- 3 Columns Button -->
                                <button 
                                    type="button" 
                                    class="column-toggle" 
                                    data-columns="3" 
                                    data-tooltip="<?php esc_attr_e('3 columns', 'aaapos-prime'); ?>"
                                    aria-label="<?php esc_attr_e('3 columns view', 'aaapos-prime'); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <rect x="2" y="3" width="5" height="5" rx="0.5"></rect>
                                        <rect x="9.5" y="3" width="5" height="5" rx="0.5"></rect>
                                        <rect x="17" y="3" width="5" height="5" rx="0.5"></rect>
                                        <rect x="2" y="10" width="5" height="5" rx="0.5"></rect>
                                        <rect x="9.5" y="10" width="5" height="5" rx="0.5"></rect>
                                        <rect x="17" y="10" width="5" height="5" rx="0.5"></rect>
                                        <rect x="2" y="17" width="5" height="5" rx="0.5"></rect>
                                        <rect x="9.5" y="17" width="5" height="5" rx="0.5"></rect>
                                        <rect x="17" y="17" width="5" height="5" rx="0.5"></rect>
                                    </svg>
                                </button>
                                
                                <!-- 4 Columns Button (Default Active) -->
                                <button 
                                    type="button" 
                                    class="column-toggle active" 
                                    data-columns="4" 
                                    data-tooltip="<?php esc_attr_e('4 columns', 'aaapos-prime'); ?>"
                                    aria-label="<?php esc_attr_e('4 columns view', 'aaapos-prime'); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <rect x="2" y="3" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="7.5" y="3" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="13" y="3" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="18.5" y="3" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="2" y="8.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="7.5" y="8.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="13" y="8.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="18.5" y="8.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="2" y="14" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="7.5" y="14" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="13" y="14" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="18.5" y="14" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="2" y="19.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="7.5" y="19.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="13" y="19.5" width="3.5" height="3.5" rx="0.5"></rect>
                                        <rect x="18.5" y="19.5" width="3.5" height="3.5" rx="0.5"></rect>
                                    </svg>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    <?php

                    // ============================================
                    // CATEGORY FILTER SECTION - BELOW TOOLBAR
                    // ============================================
                    if ($show_category_filter && (is_shop() || is_product_category())) {
                        aaapos_render_category_filter();
                    }

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

<!-- Initialize column preference on page load -->
<script>
(function() {
    'use strict';
    
    // Get saved column preference (default: 4)
    var savedColumns = localStorage.getItem('shopColumnsView');
    
    if (!savedColumns || savedColumns === 'undefined' || savedColumns === 'null') {
        savedColumns = '4';
        localStorage.setItem('shopColumnsView', '4');
    }
    
    // Apply to grid
    var productsGrid = document.querySelector('.woocommerce ul.products, .woocommerce-page ul.products');
    if (productsGrid) {
        productsGrid.setAttribute('data-columns', savedColumns);
    }
    
    // Update active button
    var columnToggles = document.querySelectorAll('.column-toggle');
    columnToggles.forEach(function(toggle) {
        var toggleColumns = toggle.getAttribute('data-columns');
        if (toggleColumns === savedColumns) {
            toggle.classList.add('active');
        } else {
            toggle.classList.remove('active');
        }
    });
})();
</script>

<?php

get_footer('shop');