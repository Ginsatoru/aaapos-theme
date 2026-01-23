<?php
/**
 * The front page template file
 * 
 * This template intelligently handles different homepage scenarios:
 * 1. If a specific page is selected as homepage with "Homepage Template" -> Show homepage sections
 * 2. If a different page is set as homepage -> Show that page's normal content
 * 3. If "Latest Posts" is selected -> Show blog posts
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get the current page ID if a static page is set
$page_id = get_option('page_on_front');

// Check if this page uses the Homepage Template
$is_homepage_template = false;
if ($page_id) {
    $template = get_page_template_slug($page_id);
    $is_homepage_template = ($template === 'page-templates/homepage.php');
}

// Scenario 1: Homepage Template is assigned OR no static page is set (show sections)
if ($is_homepage_template || !$page_id) {
    ?>
    <?php
    // Hero Section
    get_template_part('template-parts/hero/hero-section');

    // Featured Products
    if (get_theme_mod('show_featured_products', true)) {
        get_template_part('template-parts/sections/featured-products');
    }

    // Product Categories
    if (get_theme_mod('show_categories', true)) {
        get_template_part('template-parts/sections/product-categories');
    }

    // Deals & Offers
    if (get_theme_mod('show_deals', true)) {
        get_template_part('template-parts/sections/deals-offers');
    }

    // Testimonials
    if (get_theme_mod('show_testimonials', true)) {
        get_template_part('template-parts/sections/testimonials');
    }

    // Blog Preview
    if (get_theme_mod('show_blog', true)) {
        get_template_part('template-parts/sections/blog-preview');
    }

    // Newsletter
    if (get_theme_mod('show_newsletter', true)) {
        get_template_part('template-parts/sections/newsletter');
    }
    ?>
    <?php
} 
// Scenario 2: A different page template is set as homepage (show regular page content)
else {
    ?>
    <div class="site-content">
        <div class="container">
            <main id="primary" class="site-main">
                <?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content/content', 'page');
                    
                    // If comments are open or there's at least one comment, load the comment template
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                endwhile;
                ?>
            </main>
        </div>
    </div>
    <?php
}

get_footer();