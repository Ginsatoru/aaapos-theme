<?php
/**
 * Dynamic shop filters: attributes, brand, tags, availability and rating,
 * built from what the store actually has set up. Only groups with options
 * for the products in the current context (category, tag or search) show.
 *
 * URL params (prefixed so they never clash with WooCommerce's own filter_*):
 *   f_{taxonomy}[]  term slugs (attributes, product_brand, product_tag)
 *   f_stock[]       instock | onbackorder | outofstock
 *   f_sale          1
 *   f_rating        minimum average rating (1-4)
 *
 * @package aaapos-prime
 */

defined('ABSPATH') || exit;

/**
 * Taxonomies offered as filter groups, label keyed by taxonomy name.
 */
function aaapos_filter_taxonomies()
{
    $taxonomies = [];

    foreach (['product_brand', 'pwb-brand', 'yith_product_brand'] as $brand) {
        if (taxonomy_exists($brand)) {
            $taxonomies[$brand] = __('Brand', 'aaapos-prime');
            break;
        }
    }

    foreach (wc_get_attribute_taxonomies() as $attribute) {
        $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
        if (taxonomy_exists($taxonomy)) {
            $taxonomies[$taxonomy] = $attribute->attribute_label;
        }
    }

    $taxonomies['product_tag'] = __('Tags', 'aaapos-prime');

    return $taxonomies;
}

/**
 * Read one filter param as a clean array (accepts f_x[]=a&f_x[]=b or f_x=a,b).
 */
function aaapos_filter_param($key)
{
    if (empty($_GET[$key])) {
        return [];
    }

    $value = wp_unslash($_GET[$key]);
    $value = is_array($value) ? $value : explode(',', $value);

    return array_values(array_filter(array_map('sanitize_title', $value)));
}

/**
 * Every query param the filters use, for "Clear All" links.
 */
function aaapos_filter_param_keys()
{
    $keys = ['f_stock', 'f_sale', 'f_rating'];

    foreach (array_keys(aaapos_filter_taxonomies()) as $taxonomy) {
        $keys[] = 'f_' . $taxonomy;
    }

    return $keys;
}

function aaapos_has_custom_filters()
{
    foreach (aaapos_filter_param_keys() as $key) {
        if (!empty($_GET[$key])) {
            return true;
        }
    }

    return false;
}

/**
 * Add the active filters to WP_Query args (works for the main query and
 * for search.php's own query).
 */
function aaapos_apply_shop_filters(array $args)
{
    $tax_query  = !empty($args['tax_query']) ? (array) $args['tax_query'] : [];
    $meta_query = !empty($args['meta_query']) ? (array) $args['meta_query'] : [];

    foreach (array_keys(aaapos_filter_taxonomies()) as $taxonomy) {
        $terms = aaapos_filter_param('f_' . $taxonomy);
        if ($terms) {
            $tax_query[] = ['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $terms, 'operator' => 'IN'];
        }
    }

    $stock = array_intersect(aaapos_filter_param('f_stock'), ['instock', 'onbackorder', 'outofstock']);
    if ($stock) {
        $meta_query[] = ['key' => '_stock_status', 'value' => $stock, 'compare' => 'IN'];
    }

    $rating = isset($_GET['f_rating']) ? absint($_GET['f_rating']) : 0;
    if ($rating >= 1 && $rating <= 5) {
        $meta_query[] = ['key' => '_wc_average_rating', 'value' => $rating, 'compare' => '>=', 'type' => 'DECIMAL'];
    }

    if (!empty($_GET['f_sale'])) {
        $on_sale = wc_get_product_ids_on_sale();
        $current = !empty($args['post__in']) ? (array) $args['post__in'] : null;
        $ids     = $current === null ? $on_sale : array_intersect($current, $on_sale);
        $args['post__in'] = $ids ? array_values($ids) : [0];
    }

    if ($tax_query) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

    if ($meta_query) {
        $meta_query['relation'] = 'AND';
        $args['meta_query'] = $meta_query;
    }

    return $args;
}

/**
 * Apply the filters to the main shop/category/tag query.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !aaapos_has_custom_filters()) {
        return;
    }

    if (!$query->is_post_type_archive('product') && !$query->is_tax(get_object_taxonomies('product'))) {
        return;
    }

    $args = aaapos_apply_shop_filters([
        'tax_query'  => $query->get('tax_query'),
        'meta_query' => $query->get('meta_query'),
        'post__in'   => $query->get('post__in'),
    ]);

    foreach (['tax_query', 'meta_query', 'post__in'] as $key) {
        if (!empty($args[$key])) {
            $query->set($key, $args[$key]);
        }
    }
}, 20);

/**
 * Product IDs in the current context (category/tag/brand page or search),
 * ignoring the filters themselves. Cached until a product changes.
 */
function aaapos_filter_context_ids($search = '')
{
    $args = ['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true];

    $term = get_queried_object();
    if ($search !== '') {
        $args['s'] = $search;
    } elseif ($term instanceof WP_Term) {
        $args['tax_query'] = [['taxonomy' => $term->taxonomy, 'terms' => $term->term_id]];
    }

    $key = 'aaapos_fctx_' . get_option('aaapos_filter_cache_v', 1) . '_' . md5(wp_json_encode($args));
    $ids = get_transient($key);

    if ($ids === false) {
        $ids = get_posts($args);
        set_transient($key, $ids, DAY_IN_SECONDS);
    }

    return $ids;
}

add_action('save_post_product', function () {
    update_option('aaapos_filter_cache_v', (int) get_option('aaapos_filter_cache_v', 1) + 1, false);
});

/**
 * Filter groups to show for the current context.
 * Each group: ['key', 'label', 'type' => checkbox|radio, 'options' => [value => label], 'selected' => []]
 */
function aaapos_get_filter_groups($search = '')
{
    $ids = aaapos_filter_context_ids($search);
    if (!$ids) {
        return [];
    }

    $groups  = [];
    $current = get_queried_object();

    foreach (aaapos_filter_taxonomies() as $taxonomy => $label) {
        if ($current instanceof WP_Term && $current->taxonomy === $taxonomy) {
            continue;
        }

        $terms = get_terms(['taxonomy' => $taxonomy, 'object_ids' => $ids, 'hide_empty' => true]);
        if (is_wp_error($terms) || count($terms) < 1) {
            continue;
        }

        $groups[] = [
            'key'      => 'f_' . $taxonomy,
            'label'    => $label,
            'type'     => 'checkbox',
            'options'  => wp_list_pluck($terms, 'name', 'slug'),
            'selected' => aaapos_filter_param('f_' . $taxonomy),
        ];
    }

    $statuses = array_intersect_key(wc_get_product_stock_status_options(), array_flip(aaapos_context_stock_statuses($ids)));
    if (count($statuses) > 1) {
        $groups[] = [
            'key'      => 'f_stock',
            'label'    => __('Availability', 'aaapos-prime'),
            'type'     => 'checkbox',
            'options'  => $statuses,
            'selected' => aaapos_filter_param('f_stock'),
        ];
    }

    if (array_intersect($ids, wc_get_product_ids_on_sale())) {
        $groups[] = [
            'key'      => 'f_sale',
            'label'    => __('Deals', 'aaapos-prime'),
            'type'     => 'checkbox',
            'options'  => ['1' => __('On sale', 'aaapos-prime')],
            'selected' => empty($_GET['f_sale']) ? [] : ['1'],
        ];
    }

    if (wc_review_ratings_enabled() && aaapos_context_has_ratings($ids)) {
        $options = [];
        foreach ([4, 3, 2, 1] as $stars) {
            /* translators: %d: number of stars */
            $options[$stars] = sprintf(__('%d stars & up', 'aaapos-prime'), $stars);
        }
        $groups[] = [
            'key'      => 'f_rating',
            'label'    => __('Rating', 'aaapos-prime'),
            'type'     => 'radio',
            'options'  => $options,
            'selected' => isset($_GET['f_rating']) ? [(string) absint($_GET['f_rating'])] : [],
        ];
    }

    return $groups;
}

function aaapos_context_stock_statuses(array $ids)
{
    global $wpdb;
    $ids = implode(',', array_map('absint', $ids));

    return $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_stock_status' AND post_id IN ($ids)"); // phpcs:ignore WordPress.DB
}

function aaapos_context_has_ratings(array $ids)
{
    global $wpdb;
    $ids = implode(',', array_map('absint', $ids));

    return (bool) $wpdb->get_var("SELECT 1 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND meta_value > 0 AND post_id IN ($ids) LIMIT 1"); // phpcs:ignore WordPress.DB
}