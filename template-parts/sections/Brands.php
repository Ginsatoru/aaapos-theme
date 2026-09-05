<?php
/**
 * Brands Section
 * 
 * Displays WooCommerce product brands automatically.
 * Auto-detects whichever brand taxonomy is registered:
 * - product_brand (WooCommerce core Brands, WC 8.3+)
 * - pwb-brand (Perfect Brands for WooCommerce)
 * - yith_product_brand (YITH WooCommerce Brands)
 * 
 * Displays as a slider: 6 visible at a time on desktop, with prev/next
 * controls in the section header. Remaining brands scroll into view.
 * 
 * @package aaapos-prime
 */

// Detect which brand taxonomy is registered on this site
$brand_taxonomy = '';
foreach (['product_brand', 'pwb-brand', 'yith_product_brand'] as $tax) {
    if (taxonomy_exists($tax)) {
        $brand_taxonomy = $tax;
        break;
    }
}

// Bail if no brand taxonomy is available
if (empty($brand_taxonomy)) {
    if (current_user_can('manage_options')) {
        echo '<!-- AAAPOS Brands: no brand taxonomy found (checked product_brand, pwb-brand, yith_product_brand). Install/activate a WooCommerce Brands feature or plugin. -->';
    }
    return;
}

$title = get_theme_mod('brands_title', 'Shop by Brand');
$subtitle = get_theme_mod('brands_subtitle', '');

// Pull all brands with at least one product
$brands = get_terms(array(
    'taxonomy'   => $brand_taxonomy,
    'hide_empty' => true,
));

if (empty($brands) || is_wp_error($brands)) {
    if (current_user_can('manage_options')) {
        echo '<!-- AAAPOS Brands: taxonomy "' . esc_html($brand_taxonomy) . '" found, but get_terms() returned no brands with published products attached. Assign this brand taxonomy to at least one published product. -->';
    }
    return;
}

// Only show prev/next nav buttons if there are more brands than fit in one view (6)
$show_nav = count($brands) > 6;
?>

<section class="brands section" id="brands" aria-labelledby="brands-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">

            <div class="section-header-text">
                <h2 id="brands-heading" class="section-title">
                    <?php echo esc_html($title); ?>
                </h2>
                <?php if (!empty($subtitle)) : ?>
                    <p class="section-subtitle">
                        <?php echo esc_html($subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="section-header-actions">
                <?php if ($show_nav) : ?>
                    <button type="button" class="brands-nav-btn brands-nav-prev" aria-label="<?php esc_attr_e('Previous brands', 'aaapos-prime'); ?>" disabled>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button type="button" class="brands-nav-btn brands-nav-next" aria-label="<?php esc_attr_e('Next brands', 'aaapos-prime'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="section-header-link btn-outline">
                    <?php esc_html_e('View All Brands', 'aaapos-prime'); ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Brands Slider -->
        <div class="brands-slider-wrap">
            <div class="brands-slider">
                <div class="brands-track" data-animate="zoom-in" data-animate-delay="200">
                    <?php
                    foreach ($brands as $brand) :
                        // Get brand thumbnail (works for WC core Brands + most brand plugins
                        // that store it under the 'thumbnail_id' term meta key)
                        $thumbnail_id = get_term_meta($brand->term_id, 'thumbnail_id', true);
                        $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';
                    ?>
                        <a href="<?php echo esc_url(get_term_link($brand)); ?>" class="brand-card">
                            <span class="brand-card__image-wrap">
                                <?php if ($image) : ?>
                                    <img 
                                        src="<?php echo esc_url($image); ?>" 
                                        alt="<?php echo esc_attr($brand->name); ?>" 
                                        class="brand-card__image"
                                        loading="lazy"
                                    >
                                <?php else : ?>
                                    <span class="brand-card__name-fallback"><?php echo esc_html($brand->name); ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if ($image) : ?>
                                <span class="brand-card__name">
                                    <?php echo esc_html($brand->name); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($show_nav) : ?>
<script>
(function () {
    document.querySelectorAll('.brands-slider-wrap').forEach(function (wrap) {
        var slider = wrap.querySelector('.brands-slider');
        var brandsSection = wrap.closest('.brands');
        var prevBtn = brandsSection ? brandsSection.querySelector('.brands-nav-prev') : null;
        var nextBtn = brandsSection ? brandsSection.querySelector('.brands-nav-next') : null;

        if (!slider || !prevBtn || !nextBtn) {
            return;
        }

        function updateButtons() {
            var maxScroll = slider.scrollWidth - slider.clientWidth;
            prevBtn.disabled = slider.scrollLeft <= 4;
            nextBtn.disabled = slider.scrollLeft >= maxScroll - 4;
        }

        function scrollByPage(direction) {
            slider.scrollBy({
                left: direction * slider.clientWidth,
                behavior: 'smooth'
            });
        }

        prevBtn.addEventListener('click', function () { scrollByPage(-1); });
        nextBtn.addEventListener('click', function () { scrollByPage(1); });
        slider.addEventListener('scroll', updateButtons, { passive: true });
        window.addEventListener('resize', updateButtons);
        updateButtons();
    });
})();
</script>
<?php endif; ?>