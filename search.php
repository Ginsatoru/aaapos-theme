<?php
/**
 * The template for displaying search results
 * Reuses the shop archive markup/classes (see archive-product.php) so
 * styling is inherited from the shared shop/woocommerce CSS instead of
 * a duplicated stylesheet.
 *
 * @package aaapos-prime
 */

get_header();

$header_bg_image = aaapos_get_shop_header_bg_image();

$paged = (get_query_var('paged')) ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

// get_search_query() defaults to esc_html output, which turns "&" into
// "&amp;" and breaks WP_Query's search matching against the raw DB value.
// Always use the unescaped raw term for the query; escape only on output.
$raw_search_query = get_search_query( false );

$product_search_args = array(
    's'              => $raw_search_query,
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'paged'          => $paged,
    'posts_per_page' => wc_get_default_products_per_row() * wc_get_default_product_rows_per_page(),
);

$product_search_query = new WP_Query($product_search_args);
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

                    <div class="shop-toolbar">

                        <div class="woocommerce-result-count">
                            <?php
                            printf(
                                esc_html__('Showing all %d results', 'aaapos-prime'),
                                $product_search_query->found_posts
                            );
                            ?>
                        </div>

                        <div class="toolbar-controls">

                            <form class="woocommerce-ordering" method="get" action="#">
                                <select name="orderby" class="orderby" aria-label="<?php esc_attr_e('Shop order', 'aaapos-prime'); ?>">
                                    <?php
                                    $orderby_options = array(
                                        'menu_order' => __('Default sorting', 'aaapos-prime'),
                                        'popularity' => __('Sort by popularity', 'aaapos-prime'),
                                        'rating'     => __('Sort by average rating', 'aaapos-prime'),
                                        'date'       => __('Sort by latest', 'aaapos-prime'),
                                        'price'      => __('Sort by price: low to high', 'aaapos-prime'),
                                        'price-desc' => __('Sort by price: high to low', 'aaapos-prime'),
                                    );

                                    $current_orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'menu_order';

                                    foreach ($orderby_options as $id => $name) {
                                        echo '<option value="' . esc_attr($id) . '" ' . selected($current_orderby, $id, false) . '>' . esc_html($name) . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="s" value="<?php echo esc_attr(get_search_query()); ?>" />
                                <input type="hidden" name="post_type" value="product" />
                            </form>

                            <div class="column-toggle-wrapper">
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

                            <h2 class="no-results-title">
                                <?php esc_html_e('No results found', 'aaapos-prime'); ?>
                            </h2>

                            <p class="no-results-text">
                                <?php esc_html_e('We couldn\'t find anything matching your search. Try adjusting your keywords.', 'aaapos-prime'); ?>
                            </p>
                        </div>
                    </div>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </div><!-- .shop-main-content -->
        </div><!-- .shop-content-area -->
    </div><!-- .container-wide -->
</div><!-- .shop-page-wrapper -->
</div><!-- .woocommerce -->

<!-- Initialize column preference on page load (shared with shop page) -->
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
get_footer();