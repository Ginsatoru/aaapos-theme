<?php
/**
 * The template for displaying search results
 * UPDATED: Toolbar (breadcrumb, search, filters, column toggle) now
 * lives in template-parts/shop/toolbar.php, shared with
 * archive-product.php, instead of being duplicated in both files.
 *
 * NOTE: This page builds its own WP_Query (not the main query), so
 * WooCommerce's core price/sort filtering (WC_Query) never reaches it -
 * orderby and price range are applied manually below.
 *
 * @package aaapos-prime
 */

get_header();

$header_bg_image = aaapos_get_shop_header_bg_image();
$paged = (get_query_var('paged')) ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

// get_search_query() defaults to esc_html output, which turns "&" into
// "&amp;" and breaks WP_Query's search matching against the raw DB value.
$raw_search_query = get_search_query(false);

// Variables consumed by template-parts/shop/toolbar.php
$current_orderby   = ! empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$current_min_price = isset($_GET['min_price']) ? (int) $_GET['min_price'] : 0;

$max_product_price = function_exists('aaapos_get_context_max_price')
    ? aaapos_get_context_max_price(['s' => $raw_search_query, 'post_type' => 'product', 'post_status' => 'publish'])
    : 10000;
if ($max_product_price <= 0) {
    $max_product_price = 10000;
}

$current_max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : $max_product_price;
if ($current_max_price <= 0 || $current_max_price > $max_product_price) {
    $current_max_price = $max_product_price;
}
$has_active_filters = $current_orderby || $current_min_price > 0 || $current_max_price < $max_product_price;

$product_search_args = array(
    's'              => $raw_search_query,
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'paged'          => $paged,
    'posts_per_page' => wc_get_default_products_per_row() * wc_get_default_product_rows_per_page(),
);

// Sort/price range applied manually - this is a secondary query, so
// WooCommerce's core filtering (which only hooks the main query) can't
// reach it.
switch ($current_orderby) {
    case 'popularity':
        $product_search_args['meta_key'] = 'total_sales';
        $product_search_args['orderby']  = 'meta_value_num';
        $product_search_args['order']    = 'DESC';
        break;
    case 'rating':
        $product_search_args['meta_key'] = '_wc_average_rating';
        $product_search_args['orderby']  = 'meta_value_num';
        $product_search_args['order']    = 'DESC';
        break;
    case 'date':
        $product_search_args['orderby'] = 'date';
        $product_search_args['order']   = 'DESC';
        break;
    case 'price':
        $product_search_args['meta_key'] = '_price';
        $product_search_args['orderby']  = 'meta_value_num';
        $product_search_args['order']    = 'ASC';
        break;
    case 'price-desc':
        $product_search_args['meta_key'] = '_price';
        $product_search_args['orderby']  = 'meta_value_num';
        $product_search_args['order']    = 'DESC';
        break;
}

if ($current_min_price > 0 || $current_max_price < $max_product_price) {
    $product_search_args['meta_query'] = array(
        array(
            'key'     => '_price',
            'value'   => array($current_min_price, $current_max_price),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ),
    );
}

$product_search_query = new WP_Query($product_search_args);

$shop_search_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/');
$base_archive_url = get_pagenum_link(1);
$current_archive_url = esc_url($base_archive_url);
$clear_filters_url = esc_url(remove_query_arg(['min_price', 'max_price', 'orderby'], $base_archive_url));
?>

<div class="woocommerce">
<div class="shop-page-wrapper no-sidebar search-page">
    <div class="container-wide">
        <div class="shop-content-area">
            <div class="shop-main-content">

                <?php if ($product_search_query->have_posts()) : ?>

                    <header class="woocommerce-products-header<?php echo !empty($header_bg_image) ? ' has-background-image' : ''; ?>"
                            <?php if (!empty($header_bg_image)) : ?>
                                style="--shop-header-bg-image: url('<?php echo esc_url($header_bg_image); ?>');"
                            <?php endif; ?>>
                        <div class="woocommerce-products-header__inner">
                            <h1 class="woocommerce-products-header__title page-title">
                                <?php
                                printf(
                                    esc_html__('Search Results for %s', 'aaapos-prime'),
                                    '<span class="search-query">' . esc_html(get_search_query()) . '</span>'
                                );
                                ?>
                            </h1>
                            <div class="woocommerce-archive-description">
                                <p>
                                    <?php
                                    $total = $product_search_query->found_posts;
                                    printf(
                                        _n(
                                            'We found %s product matching your search',
                                            'We found %s products matching your search',
                                            $total,
                                            'aaapos-prime'
                                        ),
                                        '<strong>' . number_format_i18n($total) . '</strong>'
                                    );
                                    ?>
                                </p>
                            </div>
                        </div>
                    </header>

                    <?php get_template_part('template-parts/shop/toolbar'); ?>

                    <ul class="products" data-columns="4">
                        <?php
                        while ($product_search_query->have_posts()) : $product_search_query->the_post();
                            wc_get_template_part('content', 'product');
                        endwhile;
                        ?>
                    </ul>

                    <div class="woocommerce-pagination">
                        <?php
                        echo paginate_links(array(
                            'base'      => str_replace(PHP_INT_MAX, '%#%', esc_url(get_pagenum_link(PHP_INT_MAX))),
                            'format'    => '',
                            'current'   => max(1, $paged),
                            'total'     => $product_search_query->max_num_pages,
                            'mid_size'  => 2,
                            'prev_text' => sprintf(
                                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> %s',
                                esc_html__('Previous', 'aaapos-prime')
                            ),
                            'next_text' => sprintf(
                                '%s <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                                esc_html__('Next', 'aaapos-prime')
                            ),
                        ));
                        ?>
                    </div>

                <?php else : ?>

                    <div class="search-no-results woocommerce-info">
                        <div class="no-results-content">
                            <div class="no-results-icon">
                                <img src="<?php echo esc_url( AAAPOS_ASSETS_URI ); ?>/../images/icons/sad.gif" alt="<?php esc_attr_e('No results', 'aaapos-prime'); ?>" width="64" height="64" />
                            </div>
                            <h2 class="no-results-title"><?php esc_html_e('No results found', 'aaapos-prime'); ?></h2>
                            <p class="no-results-text"><?php esc_html_e('We couldn\'t find anything matching your search. Try adjusting your keywords.', 'aaapos-prime'); ?></p>
                        </div>
                    </div>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </div><!-- .shop-main-content -->
        </div><!-- .shop-content-area -->
    </div><!-- .container-wide -->
</div><!-- .shop-page-wrapper -->
</div><!-- .woocommerce -->

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
get_footer();