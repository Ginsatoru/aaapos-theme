<?php
/**
 * The Template for displaying product archives
 * WITH ENHANCED HEADER & CATEGORY FILTER
 * UPDATED: Toolbar (breadcrumb, search, filters, column toggle) now
 * lives in template-parts/shop/toolbar.php, shared with search.php,
 * instead of being duplicated in both files.
 * 
 * @package AAAPOS_Prime
 * @version 3.3.0
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_main_content');

$show_sidebar = get_theme_mod('show_shop_sidebar', false) && is_active_sidebar('shop-sidebar');
$container_class = $show_sidebar ? 'has-sidebar' : 'no-sidebar';
$show_category_filter = get_theme_mod('enable_category_filter', true);

$header_bg_image = aaapos_get_shop_header_bg_image();
$header_title = get_theme_mod('shop_header_title', 'Shop');
$header_subtitle = get_theme_mod('shop_header_subtitle', 'Evoke emotion, highlight artisan quality, create a unique experience.');

// Variables consumed by template-parts/shop/toolbar.php
$current_orderby   = ! empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$current_min_price = isset($_GET['min_price']) ? (int) $_GET['min_price'] : 0;

global $wp_query;
$max_product_price = function_exists('aaapos_get_context_max_price')
    ? aaapos_get_context_max_price($wp_query->query_vars)
    : 10000;
if ($max_product_price <= 0) {
    $max_product_price = 10000;
}

$current_max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : $max_product_price;
if ($current_max_price <= 0 || $current_max_price > $max_product_price) {
    $current_max_price = $max_product_price;
}
$has_active_filters = $current_orderby || $current_min_price > 0 || $current_max_price < $max_product_price;

$shop_search_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
$base_archive_url = get_pagenum_link(1);
$current_archive_url = esc_url($base_archive_url);
$clear_filters_url = esc_url(remove_query_arg(['min_price', 'max_price', 'orderby'], $base_archive_url));
?>

<div class="shop-page-wrapper <?php echo esc_attr($container_class); ?>">
    <div class="container-wide">
        <div class="shop-content-area">
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

                    if ($show_category_filter && (is_shop() || is_product_category())) {
                        aaapos_render_category_filter();
                    }

                    get_template_part('template-parts/shop/toolbar');

                    woocommerce_product_loop_start();

                    if (wc_get_loop_prop('total')) {
                        while (have_posts()) {
                            the_post();
                            do_action('woocommerce_shop_loop');
                            wc_get_template_part('content', 'product');
                        }
                    }

                    woocommerce_product_loop_end();
                    do_action('woocommerce_after_shop_loop');
                } else {
                    do_action('woocommerce_no_products_found');
                }

                do_action('woocommerce_after_main_content');
                ?>

            </div><!-- .shop-main-content -->

            <?php if ($show_sidebar) : ?>
                <aside class="shop-sidebar" role="complementary" aria-label="<?php esc_attr_e('Shop Sidebar', 'aaapos-prime'); ?>">
                    <?php dynamic_sidebar('shop-sidebar'); ?>
                </aside>
            <?php endif; ?>

        </div><!-- .shop-content-area -->
    </div><!-- .container-wide -->
</div><!-- .shop-page-wrapper -->

<script>
(function() {
    'use strict';
    var savedColumns = localStorage.getItem('shopColumnsView');
    if (!savedColumns || savedColumns === 'undefined' || savedColumns === 'null') {
        savedColumns = '4';
        localStorage.setItem('shopColumnsView', '4');
    }
    var productsGrid = document.querySelector('.woocommerce ul.products, .woocommerce-page ul.products');
    if (productsGrid) {
        productsGrid.setAttribute('data-columns', savedColumns);
    }
    document.querySelectorAll('.column-toggle').forEach(function(toggle) {
        var toggleColumns = toggle.getAttribute('data-columns');
        toggle.classList.toggle('active', toggleColumns === savedColumns);
    });
})();
</script>

<?php
get_footer('shop');