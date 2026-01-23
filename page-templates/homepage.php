<?php
/**
 * Template Name: Homepage Template
 * Template Post Type: page
 * 
 * Description: This template enables the homepage sections (hero, products, categories, etc.)
 * When assigned to a page and that page is set as the homepage in Reading Settings.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="homepage-wrapper">
    <?php
    // Display custom page content if it exists (editor content)
    if (have_posts()) :
        while (have_posts()) : the_post();
            if (trim(get_the_content())) :
                ?>
                <div class="container">
                    <div class="homepage-custom-content">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php
            endif;
        endwhile;
    endif;

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
</div>

<?php
get_footer();