<?php
/**
 * The template for displaying 404 pages (not found)
 * Modern minimalist design matching the reference
 * 
 * @package Macedon_Ranges
 */
get_header(); ?>

<div class="error-404-wrapper">
    <div class="container">
        <div class="error-404 not-found">
            <div class="error-content">
                
                <!-- Large 404 Number with Gradient -->
                <h1 class="error-title" data-animate="zoom-in">
                    <span class="error-number">4</span>
                    <span class="error-number">0</span>
                    <span class="error-number">4</span>
                </h1>
                
                <!-- Error Messages -->
                <div class="error-text" data-animate="fade-up">
                    <h2 class="error-subtitle">
                        <?php esc_html_e("How did you get here?!", "aaapos"); ?>
                    </h2>
                    <p class="error-description">
                        <?php esc_html_e("It's cool. We'll help you out.", "aaapos"); ?>
                    </p>
                </div>
                
                <!-- Back to Homepage Button -->
                <div class="error-actions" data-animate="fade-up">
                    <a href="<?php echo esc_url(home_url("/")); ?>" class="btn-404-home">
                        <?php esc_html_e("Back to Homepage", "aaapos"); ?>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>