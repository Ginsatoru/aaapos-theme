<?php
/**
 * Product Categories Section with Scroll Animations
 * 
 * Displays product categories with custom ordering from drag-and-drop control
 * UPDATED: Now respects drag-and-drop order from customizer
 * UPDATED: Displays as a slider - 6 visible at a time on desktop, with
 * prev/next controls in the section header and mouse drag support.
 * 
 * @package Macedon_Ranges
 */

// Get customizer settings
$title = get_theme_mod('categories_title', 'Shop by Category');
$subtitle = get_theme_mod('categories_subtitle', 'Quality feed and supplies for all your pets and livestock needs');
$selected_categories = get_theme_mod('selected_categories', '');
$categories_count = get_theme_mod('categories_count', 6);

// Get product categories
if (!empty($selected_categories)) {
    // Get categories by selected IDs in the exact order from customizer
    $category_ids = array_map('intval', explode(',', $selected_categories));
    $category_ids = array_filter($category_ids); // Remove empty values
    
    if (!empty($category_ids)) {
        // Get all categories at once
        $all_categories = get_terms(array(
            'taxonomy'   => 'product_cat',
            'include'    => $category_ids,
            'hide_empty' => false,
        ));
        
        // Create associative array for quick lookup
        $categories_by_id = array();
        foreach ($all_categories as $cat) {
            $categories_by_id[$cat->term_id] = $cat;
        }
        
        // Reorder categories to match the customizer order
        $categories = array();
        foreach ($category_ids as $cat_id) {
            if (isset($categories_by_id[$cat_id])) {
                $categories[] = $categories_by_id[$cat_id];
            }
        }
    } else {
        $categories = array();
    }
} else {
    // Fallback: Get top categories by product count
    $categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => $categories_count,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ));
}

// Only display if we have categories
if (empty($categories) || is_wp_error($categories)) {
    return;
}

// Only show prev/next nav buttons if there are more categories than fit in one view (6)
$show_nav = count($categories) > 6;
?>

<section class="product-categories section" id="categories" aria-labelledby="categories-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">

            <div class="section-header-text">
                <h2 id="categories-heading" class="section-title">
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
                    <button type="button" class="categories-nav-btn categories-nav-prev" aria-label="<?php esc_attr_e('Previous categories', 'macedon-ranges'); ?>" disabled>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button type="button" class="categories-nav-btn categories-nav-next" aria-label="<?php esc_attr_e('Next categories', 'macedon-ranges'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="section-header-link btn-outline">
                    <?php esc_html_e('View All Categories', 'macedon-ranges'); ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Categories Slider -->
        <div class="categories-slider-wrap">
            <div class="categories-slider">
                <div class="categories-track" data-animate="zoom-in" data-animate-delay="200">
                    <?php 
                    foreach ($categories as $category) : 
                        // Get category thumbnail
                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                        $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : wc_placeholder_img_src();
                    ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-card">
                            <span class="category-card__image-wrap">
                                <img 
                                    src="<?php echo esc_url($image); ?>" 
                                    alt="<?php echo esc_attr($category->name); ?>" 
                                    class="category-card__image"
                                    loading="lazy"
                                >
                            </span>
                            <span class="category-card__name">
                                <?php echo esc_html($category->name); ?>
                            </span>
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
    document.querySelectorAll('.categories-slider-wrap').forEach(function (wrap) {
        var slider = wrap.querySelector('.categories-slider');
        var section = wrap.closest('.product-categories');
        var prevBtn = section ? section.querySelector('.categories-nav-prev') : null;
        var nextBtn = section ? section.querySelector('.categories-nav-next') : null;

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