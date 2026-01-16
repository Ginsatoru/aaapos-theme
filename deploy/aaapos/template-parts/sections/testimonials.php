<?php
/**
 * Testimonials Section with Scroll Animations
 * 
 * @package Macedon_Ranges
 * 
 * FIXED ISSUES:
 * - Added wp_reset_postdata() to prevent ghost slides
 * - Added proper slide count validation
 * - Improved HTML structure for better slider initialization
 */

// Get customizer settings
$title = get_theme_mod('testimonials_title', 'What Our Customers Say');
$subtitle = get_theme_mod('testimonials_subtitle', 'Don\'t just take our word for it - hear from our satisfied customers across Macedon Ranges');

// Get statistics
$stat_customers = get_theme_mod('testimonials_stat_customers', '500+');
$stat_customers_label = get_theme_mod('testimonials_stat_customers_label', 'Happy Customers');
$stat_local = get_theme_mod('testimonials_stat_local', '100%');
$stat_local_label = get_theme_mod('testimonials_stat_local_label', 'Local Produce');
$stat_support = get_theme_mod('testimonials_stat_support', '24/7');
$stat_support_label = get_theme_mod('testimonials_stat_support_label', 'Support');
$stat_rating = get_theme_mod('testimonials_stat_rating', '4.9★');
$stat_rating_label = get_theme_mod('testimonials_stat_rating_label', 'Average Rating');

// Query testimonials from Custom Post Type
$testimonials_query = new WP_Query(array(
    'post_type'      => 'testimonial',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
));

// Only display if we have testimonials
if (!$testimonials_query->have_posts()) {
    wp_reset_postdata(); // CRITICAL: Reset even if no posts
    return;
}
?>

<section class="testimonials section" id="testimonials" aria-labelledby="testimonials-heading">
    <div class="container">
        <!-- Section Header with Animation -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            <h2 id="testimonials-heading" class="section-title">
                <?php echo esc_html($title); ?>
            </h2>
            <?php if (!empty($subtitle)) : ?>
                <p class="section-subtitle">
                    <?php echo esc_html($subtitle); ?>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Testimonial Slider with Animation -->
        <div class="testimonial-slider" 
             role="region" 
             aria-label="Customer testimonials"
             data-animate="fade-up" 
             data-animate-delay="200"
             data-total-slides="<?php echo $testimonials_query->post_count; ?>">
            <div class="testimonial-slides">
                <?php 
                $slide_count = 0;
                while ($testimonials_query->have_posts()) : 
                    $testimonials_query->the_post(); 
                    $slide_count++;
                    
                    // Get custom fields
                    $customer_name = get_post_meta(get_the_ID(), '_testimonial_customer_name', true);
                    $customer_role = get_post_meta(get_the_ID(), '_testimonial_customer_role', true);
                    $star_rating = get_post_meta(get_the_ID(), '_testimonial_star_rating', true);
                    
                    // Set default rating if empty
                    $star_rating = !empty($star_rating) ? absint($star_rating) : 5;
                    $star_rating = max(1, min(5, $star_rating)); // Ensure between 1-5
                    
                    // Get featured image
                    $avatar_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                    
                    // Set default name if empty
                    $customer_name = !empty($customer_name) ? $customer_name : 'Anonymous';
                ?>
                
                <div class="testimonial-slide" 
                     role="group" 
                     aria-label="Testimonial <?php echo $slide_count; ?> of <?php echo $testimonials_query->post_count; ?>" 
                     aria-roledescription="slide"
                     data-slide-index="<?php echo $slide_count - 1; ?>">
                    <div class="testimonial-card">
                        <!-- Avatar Icon (Decorative) -->
                        <?php if ($avatar_url) : ?>
                            <div class="testimonial-card__avatar-wrapper">
                                <img 
                                    src="<?php echo esc_url($avatar_url); ?>" 
                                    alt="" 
                                    class="testimonial-card__avatar-icon"
                                    loading="lazy"
                                    width="80"
                                    height="80"
                                    aria-hidden="true"
                                >
                            </div>
                        <?php else : ?>
                            <div class="testimonial-card__avatar-wrapper">
                                <div class="testimonial-card__avatar-icon testimonial-card__avatar-icon--placeholder" aria-hidden="true">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Star Rating -->
                        <?php if ($star_rating > 0) : ?>
                        <div class="testimonial-card__rating" role="img" aria-label="<?php echo esc_attr($star_rating); ?> out of 5 stars">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <span class="star <?php echo $i <= $star_rating ? 'star--filled' : 'star--empty'; ?>" aria-hidden="true">★</span>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Testimonial Content -->
                        <div class="testimonial-card__content">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Author Information -->
                        <div class="testimonial-card__author">
                            <div class="testimonial-card__name">
                                <?php echo esc_html($customer_name); ?>
                            </div>
                            <?php if (!empty($customer_role)) : ?>
                                <div class="testimonial-card__role">
                                    <?php echo esc_html($customer_role); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php endwhile; ?>
            </div>
            
            <!-- Navigation Dots -->
            <?php if ($testimonials_query->post_count > 1) : ?>
                <div class="slider-dots" role="tablist" aria-label="Testimonial navigation"></div>
            <?php endif; ?>
        </div>
        
        <!-- Statistics Section with Staggered Animation -->
        <div class="testimonials-stats">
            <div class="stat-item" 
                 data-animate="zoom-in" 
                 data-animate-delay="300">
                <div class="stat-item__value"><?php echo esc_html($stat_customers); ?></div>
                <div class="stat-item__label"><?php echo esc_html($stat_customers_label); ?></div>
            </div>
            
            <div class="stat-item" 
                 data-animate="zoom-in" 
                 data-animate-delay="400">
                <div class="stat-item__value"><?php echo esc_html($stat_local); ?></div>
                <div class="stat-item__label"><?php echo esc_html($stat_local_label); ?></div>
            </div>
            
            <div class="stat-item" 
                 data-animate="zoom-in" 
                 data-animate-delay="500">
                <div class="stat-item__value"><?php echo esc_html($stat_support); ?></div>
                <div class="stat-item__label"><?php echo esc_html($stat_support_label); ?></div>
            </div>
            
            <div class="stat-item" 
                 data-animate="zoom-in" 
                 data-animate-delay="600">
                <div class="stat-item__value"><?php echo esc_html($stat_rating); ?></div>
                <div class="stat-item__label"><?php echo esc_html($stat_rating_label); ?></div>
            </div>
        </div>
    </div>
</section>

<?php 
// CRITICAL FIX: Always reset post data to prevent ghost slides
wp_reset_postdata(); 
?>