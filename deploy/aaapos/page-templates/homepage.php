<?php
/**
 * homepage.php
 * Template Name: Homepage Template
 * Template Post Type: page
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<?php
// Use front-page.php content or custom page content
if (have_posts()) :
    while (have_posts()) : the_post();
        if (get_the_content()) :
            ?>
            <div class="container">
                <div class="homepage-content">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php
        endif;
    endwhile;
endif;

// Include homepage sections
get_template_part('template-parts/hero/hero-section');

if (get_theme_mod('show_featured_products', true)) {
    get_template_part('template-parts/sections/featured-products');
}

if (get_theme_mod('show_categories', true)) {
    get_template_part('template-parts/sections/product-categories');
}

if (get_theme_mod('show_deals', true)) {
    get_template_part('template-parts/sections/deals-offers');
}

if (get_theme_mod('show_testimonials', true)) {
    get_template_part('template-parts/sections/testimonials');
}

if (get_theme_mod('show_blog', true)) {
    get_template_part('template-parts/sections/blog-preview');
}

if (get_theme_mod('show_newsletter', true)) {
    get_template_part('template-parts/sections/newsletter');
}
?>

<?php
get_footer();