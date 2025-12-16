<?php
/**
 * The template for displaying search results
 * FIXED: Quick View and Cart Notifications now work properly
 * 
 * @package aaapos-prime
 */

get_header();
?>

<div class="search-results-wrapper shop-page-wrapper">
    <div class="container-wide">
        
        <!-- Search Header -->
        <header class="search-header woocommerce-products-header">
            <h1 class="search-title woocommerce-products-header__title">
                <?php
                printf(
                    esc_html__('Search Results for: "%s"', 'aaapos-prime'),
                    '<span class="search-query">' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            
            <?php if (have_posts()) : ?>
                <p class="search-count">
                    <?php
                    global $wp_query;
                    $total = $wp_query->found_posts;
                    printf(
                        _n(
                            'Found %s result',
                            'Found %s results',
                            $total,
                            'aaapos-prime'
                        ),
                        '<strong>' . number_format_i18n($total) . '</strong>'
                    );
                    ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if (have_posts()) : ?>
            
            <!-- Search Refinement Bar -->
            <div class="search-refinement shop-toolbar">
                <div class="woocommerce-result-count">
                    <?php
                    printf(
                        esc_html__('Showing all %d results', 'aaapos-prime'),
                        $wp_query->found_posts
                    );
                    ?>
                </div>
                <form role="search" method="get" class="search-refinement-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-group">
                        <input 
                            type="search" 
                            class="search-input-field" 
                            placeholder="<?php esc_attr_e('Refine your search...', 'aaapos-prime'); ?>" 
                            value="<?php echo get_search_query(); ?>" 
                            name="s"
                        />
                        <button type="submit" class="search-submit-btn">
                            <?php esc_html_e('Search', 'aaapos-prime'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Results Grid - MATCHES SHOP STRUCTURE -->
            <ul class="products search-results-grid">
                <?php
                while (have_posts()) : the_post();
                    
                    // Check if it's a product
                    if (class_exists('WooCommerce') && 'product' === get_post_type()) {
                        // Display product card using WooCommerce template
                        wc_get_template_part('content', 'product');
                    } else {
                        // Display blog/post card
                        ?>
                        <li class="search-card-wrapper">
                            <div class="search-card">
                                
                                <!-- Card Image -->
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="search-card__image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium_large'); ?>
                                        </a>
                                        <span class="search-card__badge">
                                            <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?>
                                        </span>
                                    </div>
                                <?php else : ?>
                                    <div class="search-card__image search-card__image--placeholder">
                                        <a href="<?php the_permalink(); ?>">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                        </a>
                                        <span class="search-card__badge">
                                            <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Card Content -->
                                <div class="search-card__content">
                                    
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) :
                                    ?>
                                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="search-card__category">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <h2 class="search-card__title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                    
                                    <div class="search-card__meta">
                                        <span class="search-card__date">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                        <span class="search-card__author">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                            <?php the_author(); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (has_excerpt()) : ?>
                                        <div class="search-card__excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="search-card__link">
                                        <?php esc_html_e('Read More', 'aaapos-prime'); ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </a>
                                    
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    
                endwhile;
                ?>
            </ul>

            <!-- Pagination -->
            <div class="woocommerce-pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
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
            
            <!-- No Results -->
            <div class="search-no-results woocommerce-info">
                <div class="no-results-content">
                    <div class="no-results-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    
                    <h2 class="no-results-title">
                        <?php esc_html_e('No results found', 'aaapos-prime'); ?>
                    </h2>
                    
                    <p class="no-results-text">
                        <?php esc_html_e('We couldn\'t find anything matching your search. Try adjusting your keywords or explore our suggestions below.', 'aaapos-prime'); ?>
                    </p>
                    
                    <!-- Try Again Search -->
                    <form role="search" method="get" class="no-results-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-group">
                            <input 
                                type="search" 
                                class="search-input-field" 
                                placeholder="<?php esc_attr_e('Try another search...', 'aaapos-prime'); ?>" 
                                value="" 
                                name="s"
                                autofocus
                            />
                            <button type="submit" class="search-submit-btn">
                                <?php esc_html_e('Search', 'aaapos-prime'); ?>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Helpful Suggestions -->
                <div class="no-results-suggestions">
                    <h3 class="suggestions-title"><?php esc_html_e('Explore these instead', 'aaapos-prime'); ?></h3>
                    <div class="suggestion-grid">
                        <?php if (class_exists('WooCommerce')) : ?>
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="suggestion-card">
                                <div class="suggestion-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div class="suggestion-content">
                                    <h4><?php esc_html_e('Browse Products', 'aaapos-prime'); ?></h4>
                                    <p><?php esc_html_e('Explore our full catalog', 'aaapos-prime'); ?></p>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(home_url('/blog')); ?>" class="suggestion-card">
                            <div class="suggestion-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            </div>
                            <div class="suggestion-content">
                                <h4><?php esc_html_e('Visit Blog', 'aaapos-prime'); ?></h4>
                                <p><?php esc_html_e('Read our latest articles', 'aaapos-prime'); ?></p>
                            </div>
                        </a>
                        
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="suggestion-card">
                            <div class="suggestion-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <div class="suggestion-content">
                                <h4><?php esc_html_e('Get Help', 'aaapos-prime'); ?></h4>
                                <p><?php esc_html_e('Contact our team', 'aaapos-prime'); ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div><!-- .container-wide -->
</div><!-- .search-results-wrapper -->

<style>
/* =========================================
   SEARCH PAGE SPECIFIC FIXES
   ========================================= */

/* Hide "View cart" button on product cards in search page */
.search-results-wrapper .products li.product .added_to_cart {
    display: none !important;
}

/* Remove border and background from Quick View button */
.search-results-wrapper .products li.product .quick-view-button {
    background: transparent !important;
    border: none !important;
    color: var(--color-text) !important;
    padding: 0.75rem 1rem !important;
}

.search-results-wrapper .products li.product .quick-view-button:hover {
    background: transparent !important;
    border: none !important;
    color: var(--color-primary) !important;
    transform: translateY(-2px) !important;
}

/* Bigger search title with more padding */
.search-header.woocommerce-products-header {
    padding: 3rem 0 2.5rem 0 !important;
    margin-bottom: 2.5rem !important;
}

.search-title.woocommerce-products-header__title {
    font-size: clamp(2rem, 3.5vw, 2.75rem) !important;
    margin-bottom: 1rem !important;
}
</style>

<?php
get_footer();