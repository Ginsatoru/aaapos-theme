<?php
/**
 * The front page template file front-page.php
 */
get_header();
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
get_footer();