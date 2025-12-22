<?php
/**
 * Template part for displaying a message that posts cannot be found
 * UPDATED: Now uses shop header style with fallback background image
 * 
 * @package aaapos-prime
 */

// Get shop header customizer settings with FALLBACK SUPPORT
$header_bg_image = aaapos_get_shop_header_bg_image(); // Uses fallback image
?>

<section class="no-results not-found">
    
    <!-- Header with Background Image (same style as shop/search pages) -->
    <header class="woocommerce-products-header no-results-header<?php echo !empty($header_bg_image) ? ' has-background-image' : ''; ?>"
            <?php if (!empty($header_bg_image)) : ?>
                style="--shop-header-bg-image: url('<?php echo esc_url($header_bg_image); ?>');"
            <?php endif; ?>>
        
        <div class="woocommerce-products-header__inner">
            <h1 class="woocommerce-products-header__title page-title">
                <?php esc_html_e('Nothing Found', 'aaapos-prime'); ?>
            </h1>
            
            <div class="woocommerce-archive-description">
                <p>
                    <?php
                    if (is_home() && current_user_can('publish_posts')) {
                        esc_html_e('Ready to publish your first post? Get started here.', 'aaapos-prime');
                    } elseif (is_search()) {
                        esc_html_e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'aaapos-prime');
                    } else {
                        esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'aaapos-prime');
                    }
                    ?>
                </p>
            </div>
        </div>
        
    </header>

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :
            ?>
            <div class="no-results-content">
                <div class="no-results-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                
                <h2 class="no-results-title">
                    <?php esc_html_e('Start Publishing', 'aaapos-prime'); ?>
                </h2>
                
                <p class="no-results-text">
                    <?php esc_html_e('Ready to publish your first post? Get started here.', 'aaapos-prime'); ?>
                </p>
                
                <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="button button-primary">
                    <?php esc_html_e('Create New Post', 'aaapos-prime'); ?>
                </a>
            </div>
            <?php

        elseif (is_search()) :
            ?>
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
                        <?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'aaapos-prime'); ?>
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
            <?php

        else :
            ?>
            <div class="no-results-content">
                <div class="no-results-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M16 16s-1.5-2-4-2-4 2-4 2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                </div>
                
                <h2 class="no-results-title">
                    <?php esc_html_e('Oops! Nothing here', 'aaapos-prime'); ?>
                </h2>
                
                <p class="no-results-text">
                    <?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'aaapos-prime'); ?>
                </p>
                
                <?php get_search_form(); ?>
            </div>
            <?php

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->