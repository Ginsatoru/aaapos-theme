<?php
/**
 * Search modal: opened by any [data-search-open] element (header search icon, Ctrl/Cmd+K).
 *
 * @package aaapos-prime
 */

if (!defined('ABSPATH')) {
    exit;
}

$popular_terms = array_filter(array_map('trim', explode(',', (string) get_theme_mod('search_popular_terms', ''))));
$categories    = [];

if (class_exists('WooCommerce')) {
    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => true,
        'number'     => 6,
        'exclude'    => [(int) get_option('default_product_cat')],
    ]);
    $categories = is_wp_error($categories) ? [] : $categories;
}

$search_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>';
$trend_svg  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/></svg>';
?>
<div class="site-search-modal" id="site-search-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Search', 'aaapos'); ?>" data-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" hidden>
    <div class="ssm-backdrop" data-search-close></div>
    <div class="ssm-panel">
        <form role="search" method="get" class="ssm-form" action="<?php echo esc_url(home_url('/')); ?>">
            <span class="ssm-form-icon"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
            <label for="ssm-input" class="sr-only"><?php esc_html_e('Search for:', 'aaapos'); ?></label>
            <input type="search" id="ssm-input" class="ssm-input" name="s" placeholder="<?php esc_attr_e('Search products, support, tutorials...', 'aaapos'); ?>" autocomplete="off">
            <button type="button" class="ssm-esc" data-search-close aria-label="<?php esc_attr_e('Close search', 'aaapos'); ?>">Esc</button>
        </form>

        <div class="ssm-body">
            <div class="ssm-results" aria-live="polite" hidden></div>

            <div class="ssm-browse">
                <div class="ssm-section ssm-recent" hidden>
                    <div class="ssm-section-head">
                        <h3 class="ssm-heading"><?php esc_html_e('Recent', 'aaapos'); ?></h3>
                        <button type="button" class="ssm-clear"><?php esc_html_e('Clear', 'aaapos'); ?></button>
                    </div>
                    <div class="ssm-chips ssm-recent-list"></div>
                </div>

                <?php if ($popular_terms) : ?>
                <div class="ssm-section">
                    <h3 class="ssm-heading"><?php esc_html_e('Popular', 'aaapos'); ?></h3>
                    <div class="ssm-chips">
                        <?php foreach ($popular_terms as $term) : ?>
                            <button type="button" class="ssm-chip" data-term="<?php echo esc_attr($term); ?>"><?php echo $trend_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html($term); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($categories) : ?>
                <div class="ssm-section">
                    <h3 class="ssm-heading"><?php esc_html_e('Categories', 'aaapos'); ?></h3>
                    <div class="ssm-cards">
                        <?php foreach ($categories as $cat) :
                            $thumb_id = (int) get_term_meta($cat->term_id, 'thumbnail_id', true);
                            $thumb    = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';
                        ?>
                            <a class="ssm-card" href="<?php echo esc_url(get_term_link($cat)); ?>">
                                <?php if ($thumb) : ?><img src="<?php echo esc_url($thumb); ?>" alt="" width="32" height="32" loading="lazy"><?php endif; ?>
                                <span><?php echo esc_html($cat->name); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>