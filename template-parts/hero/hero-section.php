<?php
/**
 * Hero section component with scroll animations
 */
if (!get_theme_mod('show_hero', true)) {
    return;
}

// Get slideshow setting
$enable_slideshow = get_theme_mod('hero_enable_slideshow', true);

// Get slide images
$hero_slide_1 = get_theme_mod('hero_slide_1');
$hero_slide_2 = get_theme_mod('hero_slide_2');
$hero_slide_3 = get_theme_mod('hero_slide_3');
$hero_slide_4 = get_theme_mod('hero_slide_4');

// Get content settings
$badge_text = get_theme_mod('hero_badge_text', '🐾 Quality Pet & Animal Supplies');
$hero_title = get_theme_mod('hero_title', 'Premium Feed & Supplies');
$hero_title_highlight = get_theme_mod('hero_title_highlight', 'For Your Beloved Pets & Livestock');
$hero_subtitle = get_theme_mod('hero_subtitle', 'Your trusted local supplier for premium pet food, animal feed, farm supplies, and everything your animals need. From dogs and cats to horses, poultry, and livestock.');
$primary_btn_text = get_theme_mod('hero_primary_button_text', 'Shop All Products');
$primary_btn_link = get_theme_mod('hero_primary_button_link', '/shop');
$secondary_btn_text = get_theme_mod('hero_secondary_button_text', 'About Our Store');
$secondary_btn_link = get_theme_mod('hero_secondary_button_link', '/about');
$slideshow_speed = get_theme_mod('hero_slideshow_speed', 5000);

// Default fallback images
$default_images = array(
    'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=1920&q=80',
    'https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?w=1920&q=80',
    'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=1920&q=80',
    'https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=1920&q=80',
);

// Build slides array
$slides = array(
    $hero_slide_1,
    $hero_slide_2,
    $hero_slide_3,
    $hero_slide_4,
);

// Add CSS class based on slideshow setting
$hero_class = 'hero-section';
if (!$enable_slideshow) {
    $hero_class .= ' hero-static';
}
?>

<section class="<?php echo esc_attr($hero_class); ?>">
    <?php if ($enable_slideshow) : ?>
        <!-- Hero Slider using Universal Slider -->
        <div class="hero-slider" 
             data-autoplay="true" 
             data-delay="<?php echo esc_attr($slideshow_speed); ?>"
             data-pause-hover="true"
             data-keyboard="true"
             data-dots="false"
             data-arrows="false"
             data-loop="true">
            <div class="hero-slides">
                <?php foreach ($slides as $index => $slide_id) : ?>
                    <div class="hero-slide">
                        <?php if ($slide_id) : ?>
                            <?php echo wp_get_attachment_image($slide_id, 'full', false, array('alt' => esc_attr($hero_title))); ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url($default_images[$index]); ?>" alt="<?php echo esc_attr($hero_title); ?>">
                        <?php endif; ?>
                        <div class="slide-overlay"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <!-- Static Background -->
        <div class="hero-static-background">
            <?php if ($hero_slide_1) : ?>
                <?php echo wp_get_attachment_image($hero_slide_1, 'full', false, array(
                    'alt' => esc_attr($hero_title),
                    'class' => 'hero-static-image'
                )); ?>
            <?php else : ?>
                <img src="<?php echo esc_url($default_images[0]); ?>" alt="<?php echo esc_attr($hero_title); ?>" class="hero-static-image">
            <?php endif; ?>
            <div class="slide-overlay"></div>
        </div>
    <?php endif; ?>

    <!-- Hero Content with Scroll Animations -->
    <div class="hero-content-container">
        <div class="hero-content">
            <?php if ($badge_text) : ?>
            <div class="hero-badge-wrapper" 
                 data-animate="fade-down" 
                 data-animate-delay="100">
                <span class="hero-badge">
                    <?php echo esc_html($badge_text); ?>
                </span>
            </div>
            <?php endif; ?>
            
            <h1 class="hero-title" 
                data-animate="fade-up" 
                data-animate-delay="200">
                <?php echo esc_html($hero_title); ?>
                <?php if ($hero_title_highlight) : ?>
                <span class="hero-title-highlight"><?php echo esc_html($hero_title_highlight); ?></span>
                <?php endif; ?>
            </h1>
            
            <p class="hero-subtitle" 
               data-animate="fade-up" 
               data-animate-delay="300">
                <?php echo esc_html($hero_subtitle); ?>
            </p>

            <div class="hero-buttons" 
                 data-animate="fade-up" 
                 data-animate-delay="400">
                <?php if ($primary_btn_text) : ?>
                    <a href="<?php echo esc_url($primary_btn_link); ?>" class="hero-btn hero-btn-primary">
                        <?php echo esc_html($primary_btn_text); ?>
                        <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($secondary_btn_text) : ?>
                    <a href="<?php echo esc_url($secondary_btn_link); ?>" class="hero-btn hero-btn-secondary">
                        <?php echo esc_html($secondary_btn_text); ?>
                        <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>