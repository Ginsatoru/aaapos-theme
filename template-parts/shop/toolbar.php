<?php
/**
 * Shop toolbar: breadcrumb (left), search + Filters dropdown
 * (price range, sort by) + column toggle (right).
 *
 * Shared by woocommerce/archive-product.php and search.php so this
 * markup exists in one place instead of being duplicated.
 *
 * Expects (set by the including file before include()):
 * @var string $shop_search_url
 * @var string $current_archive_url
 * @var string $clear_filters_url
 * @var string $current_orderby
 * @var int    $current_min_price
 * @var int    $current_max_price
 * @var int    $max_product_price
 * @var bool   $has_active_filters
 * @var string $raw_search_query  Optional - only set on search.php.
 *             When present, hidden s/post_type fields are added to the
 *             Filters form so sort/price submits don't lose the search term.
 *
 * @package aaapos-prime
 */

defined('ABSPATH') || exit;

// get_template_part() runs this file inside its own function scope, so
// these need to be pulled in explicitly - they don't arrive automatically
// just because the calling template (archive-product.php/search.php) set
// them as top-level variables.
global $shop_search_url, $current_archive_url, $clear_filters_url,
    $current_orderby, $current_min_price, $current_max_price,
    $max_product_price, $has_active_filters, $raw_search_query;
?>
<div class="shop-toolbar">

    <!-- Left: breadcrumb -->
    <div class="shop-toolbar__left">
        <?php woocommerce_breadcrumb(); ?>
    </div>

    <!-- Right: search, filters, column toggle -->
    <div class="shop-toolbar__right">

        <!-- Search bar -->
        <form class="shop-filter__search-form"
              method="GET"
              action="<?php echo esc_url($shop_search_url); ?>"
              role="search">
            <input type="hidden" name="post_type" value="product">
            <div class="shop-filter__search">
                <input
                    type="search"
                    name="s"
                    class="shop-filter__input"
                    placeholder="<?php esc_attr_e('Search products…', 'aaapos-prime'); ?>"
                    value="<?php echo isset($raw_search_query) ? esc_attr($raw_search_query) : ''; ?>"
                    autocomplete="off"
                />
                <button type="submit" class="shop-filter__search-btn" aria-label="<?php esc_attr_e('Search', 'aaapos-prime'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Filter dropdown (price range + sort by) -->
        <div class="shop-filter__dropdown-wrap" id="aaapos-shop-filter-wrap">

            <button type="button"
                    class="shop-filter__toggle<?php echo $has_active_filters ? ' has-filters' : ''; ?>"
                    id="aaapos-shop-filter-toggle"
                    aria-expanded="false"
                    aria-controls="aaapos-shop-filter-panel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="4" y1="6" x2="20" y2="6"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                    <line x1="11" y1="18" x2="13" y2="18"/>
                </svg>
                <span><?php esc_html_e('Filters', 'aaapos-prime'); ?></span>
            </button>

            <div class="shop-filter__panel" id="aaapos-shop-filter-panel" hidden>

                <form method="GET" action="<?php echo $current_archive_url; ?>" id="aaapos-shop-filter-form">

                    <?php if (!empty($raw_search_query)) : ?>
                        <!-- Preserve the search term/post type across sort/price changes -->
                        <input type="hidden" name="s" value="<?php echo esc_attr($raw_search_query); ?>">
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>

                    <!-- Price range -->
                    <div class="shop-filter__group">
                        <label class="shop-filter__label"><?php esc_html_e('Price Range', 'aaapos-prime'); ?></label>
                        <div class="shop-filter__price-display">
                            $<span id="aaapos-price-min-display"><?php echo esc_html($current_min_price); ?></span>
                            &ndash;
                            $<span id="aaapos-price-max-display"><?php echo esc_html($current_max_price); ?></span>
                        </div>
                        <div class="shop-filter__range-wrap">
                            <div class="shop-filter__range-track">
                                <div class="shop-filter__range-fill" id="aaapos-range-fill"></div>
                            </div>
                            <input type="range" class="shop-filter__range" id="aaapos-range-min"
                                name="min_price"
                                min="0" max="<?php echo esc_attr($max_product_price); ?>"
                                value="<?php echo esc_attr($current_min_price); ?>"
                                step="5" />
                            <input type="range" class="shop-filter__range" id="aaapos-range-max"
                                name="max_price"
                                min="0" max="<?php echo esc_attr($max_product_price); ?>"
                                value="<?php echo esc_attr($current_max_price); ?>"
                                step="5" />
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="shop-filter__group">
                        <label class="shop-filter__label" for="aaapos-filter-orderby"><?php esc_html_e('Sort By', 'aaapos-prime'); ?></label>
                        <div class="shop-filter__select-wrap">
                            <select name="orderby" id="aaapos-filter-orderby" class="shop-filter__select">
                                <option value=""           <?php selected($current_orderby, ''); ?>          ><?php esc_html_e('Default', 'aaapos-prime'); ?></option>
                                <option value="date"       <?php selected($current_orderby, 'date'); ?>       ><?php esc_html_e('Newest', 'aaapos-prime'); ?></option>
                                <option value="popularity" <?php selected($current_orderby, 'popularity'); ?> ><?php esc_html_e('Popularity', 'aaapos-prime'); ?></option>
                                <option value="rating"     <?php selected($current_orderby, 'rating'); ?>     ><?php esc_html_e('Rating', 'aaapos-prime'); ?></option>
                                <option value="price"      <?php selected($current_orderby, 'price'); ?>      ><?php esc_html_e('Price: Low–High', 'aaapos-prime'); ?></option>
                                <option value="price-desc" <?php selected($current_orderby, 'price-desc'); ?> ><?php esc_html_e('Price: High–Low', 'aaapos-prime'); ?></option>
                            </select>
                            <svg class="shop-filter__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>

                    <!-- Panel actions -->
                    <div class="shop-filter__actions">
                        <button type="submit" class="shop-filter__btn"><?php esc_html_e('Apply Filters', 'aaapos-prime'); ?></button>
                        <?php if ($has_active_filters) : ?>
                            <a href="<?php echo $clear_filters_url; ?>" class="shop-filter__clear"><?php esc_html_e('Clear All', 'aaapos-prime'); ?></a>
                        <?php endif; ?>
                    </div>

                </form>

            </div><!-- .shop-filter__panel -->
        </div><!-- .shop-filter__dropdown-wrap -->

        <!-- Column Toggle (2, 3, 4 columns) -->
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

    </div><!-- .shop-toolbar__right -->

</div><!-- .shop-toolbar -->

<script>
(function () {
    var toggle = document.getElementById('aaapos-shop-filter-toggle');
    var panel  = document.getElementById('aaapos-shop-filter-panel');
    var wrap   = document.getElementById('aaapos-shop-filter-wrap');

    function openPanel() {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closePanel() {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    }

    if (toggle && panel) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (panel.hidden) {
                openPanel();
            } else {
                closePanel();
            }
        });

        document.addEventListener('click', function (e) {
            if (wrap && !wrap.contains(e.target) && !panel.hidden) {
                closePanel();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) {
                closePanel();
            }
        });
    }

    var rangeMin   = document.getElementById('aaapos-range-min');
    var rangeMax   = document.getElementById('aaapos-range-max');
    var minDisplay = document.getElementById('aaapos-price-min-display');
    var maxDisplay = document.getElementById('aaapos-price-max-display');
    var fill       = document.getElementById('aaapos-range-fill');

    if (rangeMin && rangeMax) {
        // Some browsers restore the last interacted slider position on
        // reload, independent of the server-rendered value attribute -
        // force both back to their defaults so the display always
        // matches the actual current filter state.
        rangeMin.value = rangeMin.defaultValue;
        rangeMax.value = rangeMax.defaultValue;

        function updateSlider(moved) {
            var min    = parseInt(rangeMin.value);
            var max    = parseInt(rangeMax.value);
            var absMax = parseInt(rangeMin.getAttribute('max'));

            if (min >= max) {
                if (moved === 'min') { rangeMin.value = min = max - 5; }
                else                 { rangeMax.value = max = min + 5; }
            }

            if (minDisplay) minDisplay.textContent = min;
            if (maxDisplay) maxDisplay.textContent = max;

            if (fill) {
                fill.style.left  = (min / absMax * 100) + '%';
                fill.style.right = (100 - max / absMax * 100) + '%';
            }
        }

        rangeMin.addEventListener('input', function () { updateSlider('min'); });
        rangeMax.addEventListener('input', function () { updateSlider('max'); });
        updateSlider('max');
    }
})();
</script>