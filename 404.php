<?php
/**
 * The template for displaying 404 pages (not found)
 */
get_header(); ?>

<div class="container">
    <div class="error-404 not-found">
        <div class="error-content">
            <h1 class="error-title"><?php esc_html_e(
                "404",
                "aaapos",
            ); ?></h1>
            <h2 class="error-subtitle"><?php esc_html_e(
                "Page Not Found",
                "aaapos",
            ); ?></h2>
            <p class="error-description">
                <?php esc_html_e(
                    'How did you get here?! It’s cool. We’ll help you out.',
                    "aaapos",
                ); ?>
            </p>
            <div class="error-actions">
                <a href="<?php echo esc_url(
                    home_url("/"),
                ); ?>" class="btn btn--primary">
                    <?php esc_html_e("Go Back Home", "aaapos"); ?>
                </a>
                <a href="<?php echo esc_url(
                    get_permalink(wc_get_page_id("shop")),
                ); ?>" class="btn btn--outline">
                    <?php esc_html_e("Continue Shopping", "aaapos"); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php get_footer();
