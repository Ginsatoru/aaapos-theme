<?php
/**
 * Template part for displaying search results (blog posts/pages)
 * Clean card design matching the product cards
 * 
 * @package aaapos-prime
 */

$post_id = get_the_ID();
$thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
$categories = get_the_category();
$post_type = get_post_type();
$post_date = get_the_date('M j, Y');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-card'); ?>>
    
    <!-- Post Thumbnail -->
    <?php if ($thumbnail_url) : ?>
        <div class="search-card__image">
            <a href="<?php the_permalink(); ?>">
                <img 
                    src="<?php echo esc_url($thumbnail_url); ?>" 
                    alt="<?php the_title_attribute(); ?>"
                    loading="lazy"
                >
            </a>
            
            <!-- Post Type Badge -->
            <span class="search-card__badge">
                <?php
                if ($post_type === 'post') {
                    esc_html_e('Blog', 'aaapos-prime');
                } elseif ($post_type === 'page') {
                    esc_html_e('Page', 'aaapos-prime');
                } else {
                    echo esc_html(ucfirst($post_type));
                }
                ?>
            </span>
        </div>
    <?php else : ?>
        <!-- Placeholder if no image -->
        <div class="search-card__image search-card__image--placeholder">
            <a href="<?php the_permalink(); ?>">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </a>
            <span class="search-card__badge">
                <?php
                if ($post_type === 'post') {
                    esc_html_e('Blog', 'aaapos-prime');
                } elseif ($post_type === 'page') {
                    esc_html_e('Page', 'aaapos-prime');
                } else {
                    echo esc_html(ucfirst($post_type));
                }
                ?>
            </span>
        </div>
    <?php endif; ?>
    
    <!-- Post Content -->
    <div class="search-card__content">
        
        <!-- Category (for blog posts) -->
        <?php if (!empty($categories) && $post_type === 'post') : ?>
            <div class="search-card__category">
                <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                    <?php echo esc_html($categories[0]->name); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Post Title -->
        <h2 class="search-card__title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>
        
        <!-- Post Meta -->
        <div class="search-card__meta">
            <span class="search-card__date">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <?php echo esc_html($post_date); ?>
            </span>
            
            <span class="search-card__author">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <?php the_author(); ?>
            </span>
        </div>
        
        <!-- Post Excerpt -->
        <div class="search-card__excerpt">
            <?php
            $excerpt = get_the_excerpt();
            echo wp_trim_words($excerpt, 20, '...');
            ?>
        </div>
        
        <!-- Read More Link -->
        <a href="<?php the_permalink(); ?>" class="search-card__link">
            <?php esc_html_e('Read More', 'aaapos-prime'); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>
    
</article>