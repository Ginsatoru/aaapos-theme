<?php
/**
 * Product Categories Section with Scroll Animations
 * 
 * Displays product categories with custom ordering from drag-and-drop control
 * UPDATED: Now respects drag-and-drop order from customizer
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
?>

<section class="product-categories section" id="categories" aria-labelledby="categories-heading">
    <div class="container">
        <!-- Section Header with Animation -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            <h2 id="categories-heading" class="section-title">
                <?php echo esc_html($title); ?>
            </h2>
            <?php if (!empty($subtitle)) : ?>
                <p class="section-subtitle">
                    <?php echo esc_html($subtitle); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Categories Grid with Staggered Animations -->
        <div class="categories-grid">
            <?php 
            $delay = 200; // Starting delay for stagger effect
            foreach ($categories as $category) : 
                // Get category thumbnail
                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : wc_placeholder_img_src();
                
                // Get category description
                $description = $category->description ? $category->description : sprintf(__('Browse our selection of %s', 'macedon-ranges'), strtolower($category->name));
                
                // Trim description to reasonable length
                if (strlen($description) > 100) {
                    $description = substr($description, 0, 100) . '...';
                }
                
                // Get product count
                $product_count = $category->count;
                $count_text = sprintf(
                    _n('%s product', '%s products', $product_count, 'macedon-ranges'),
                    number_format_i18n($product_count)
                );
            ?>
                <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                   class="category-card"
                   data-animate="zoom-in" 
                   data-animate-delay="<?php echo esc_attr($delay); ?>">
                    <img 
                        src="<?php echo esc_url($image); ?>" 
                        alt="<?php echo esc_attr($category->name); ?>" 
                        class="category-card__image"
                        loading="lazy"
                    >
                    <div class="category-card__overlay">
                        <div class="category-card__content">
                            <h3 class="category-card__name">
                                <?php echo esc_html($category->name); ?>
                            </h3>
                            <p class="category-card__description">
                                <?php echo esc_html($description); ?>
                            </p>
                            <span class="category-card__count">
                                <?php echo esc_html($count_text); ?>
                            </span>
                            <span class="category-card__button">
                                <?php esc_html_e('Shop Now', 'macedon-ranges'); ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php 
                $delay += 100;
            endforeach; ?>
        </div>
    </div>
</section>