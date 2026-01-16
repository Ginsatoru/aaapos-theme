<?php
/**
 * Hero section component with single WebM video support
 */
if (!get_theme_mod("show_hero", true)) {
    return;
}

// Get media type
$media_type = get_theme_mod("hero_media_type", "image");

// Get content settings
$badge_text = get_theme_mod(
    "hero_badge_text",
    "🐾 Quality Pet & Animal Supplies",
);
$hero_title = get_theme_mod("hero_title", "Premium Feed & Supplies");
$hero_title_highlight = get_theme_mod(
    "hero_title_highlight",
    "For Your Beloved Pets & Livestock",
);
$hero_subtitle = get_theme_mod(
    "hero_subtitle",
    "Your trusted local supplier for premium pet food, animal feed, farm supplies, and everything your animals need. From dogs and cats to horses, poultry, and livestock.",
);
$primary_btn_text = get_theme_mod(
    "hero_primary_button_text",
    "Shop All Products",
);
$primary_btn_link = get_theme_mod("hero_primary_button_link", "/shop");
$secondary_btn_text = get_theme_mod(
    "hero_secondary_button_text",
    "About Our Store",
);
$secondary_btn_link = get_theme_mod("hero_secondary_button_link", "/about");

// Default fallback images
$default_images = [
    "https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=1920&q=80",
    "https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?w=1920&q=80",
    "https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=1920&q=80",
    "https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=1920&q=80",
];

// Add CSS class
$hero_class = "hero-section";
if ($media_type === "video") {
    $hero_class .= " hero-video-mode";
} else {
    $hero_class .= " hero-image-mode";
}

// Get video settings if video mode
if ($media_type === "video") {
    $video_loop = get_theme_mod("hero_video_loop", true);
    $video_mute = get_theme_mod("hero_video_mute", true);
    $video_mobile_fallback = get_theme_mod("hero_video_mobile_fallback", true);

    $video_id = get_theme_mod("hero_video_webm", "");
    $video_url = $video_id ? wp_get_attachment_url($video_id) : "";

    $fallback_image_id = get_theme_mod("hero_video_fallback", "");
    $fallback_image_url = $fallback_image_id
        ? wp_get_attachment_image_url($fallback_image_id, "full")
        : "";

    // Add video data attributes
    $video_data_attrs = sprintf(
        'data-video-url="%s" data-fallback-image="%s" data-video-loop="%s" data-video-mute="%s" data-mobile-fallback="%s"',
        esc_url($video_url),
        esc_url($fallback_image_url),
        $video_loop ? "true" : "false",
        $video_mute ? "true" : "false",
        $video_mobile_fallback ? "true" : "false",
    );
} else {
    $video_data_attrs = "";

    // Get image slideshow settings
    $enable_slideshow = get_theme_mod("hero_enable_slideshow", true);

    // Get slide images
    $hero_slide_1 = get_theme_mod("hero_slide_1");
    $hero_slide_2 = get_theme_mod("hero_slide_2");
    $hero_slide_3 = get_theme_mod("hero_slide_3");
    $hero_slide_4 = get_theme_mod("hero_slide_4");

    $slideshow_speed = get_theme_mod("hero_slideshow_speed", 5000);

    // Build slides array
    $slides = [$hero_slide_1, $hero_slide_2, $hero_slide_3, $hero_slide_4];

    if (!$enable_slideshow) {
        $hero_class .= " hero-static";
    }
}
?>

<section class="<?php echo esc_attr(
    $hero_class,
); ?>" <?php echo $video_data_attrs; ?>>
    
    <?php if ($media_type === "video"): ?>
        <!-- Single Video Background -->
        <div class="hero-video-background">
            <?php if ($video_url && $video_url): ?>
                <div class="hero-video-container">
                    <?php if ($fallback_image_url): ?>
                        <img src="<?php echo esc_url($fallback_image_url); ?>" 
                             alt="<?php echo esc_attr($hero_title); ?>"
                             class="video-fallback-image"
                             loading="lazy">
                    <?php else: ?>
                        <img src="<?php echo esc_url($default_images[0]); ?>" 
                             alt="<?php echo esc_attr($hero_title); ?>"
                             class="video-fallback-image"
                             loading="lazy">
                    <?php endif; ?>
                    
                    <div class="video-controls">
                        <button class="video-play-btn" aria-label="Play video">
                            <svg class="play-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                        <button class="video-pause-btn" aria-label="Pause video" style="display: none;">
                            <svg class="pause-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                        </button>
                        <button class="video-mute-btn" aria-label="<?php echo $video_mute
                            ? "Unmute video"
                            : "Mute video"; ?>">
                            <?php if ($video_mute): ?>
                                <svg class="mute-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                </svg>
                            <?php else: ?>
                                <svg class="unmute-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                </svg>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- No video uploaded, show fallback image -->
                <div class="hero-static-background">
                    <?php if ($fallback_image_url): ?>
                        <img src="<?php echo esc_url($fallback_image_url); ?>" 
                             alt="<?php echo esc_attr($hero_title); ?>"
                             class="hero-static-image"
                             loading="lazy">
                    <?php else: ?>
                        <img src="<?php echo esc_url($default_images[0]); ?>" 
                             alt="<?php echo esc_attr($hero_title); ?>"
                             class="hero-static-image"
                             loading="lazy">
                    <?php endif; ?>
                    <div class="slide-overlay"></div>
                </div>
            <?php endif; ?>
            <div class="slide-overlay"></div>
        </div>
        
    <?php // Added class for consistent styling
        // Added class for consistent styling
        else: ?>
        <!-- In the image slideshow section, update the img tags: -->
<?php if ($enable_slideshow): ?>
    <!-- Hero Slider -->
    <div class="hero-slider" 
         data-autoplay="true" 
         data-delay="<?php echo esc_attr($slideshow_speed); ?>"
         data-pause-hover="true"
         data-keyboard="true"
         data-dots="false"
         data-arrows="false"
         data-loop="true">
        <div class="hero-slides">
            <?php foreach ($slides as $index => $slide_id): ?>
                <div class="hero-slide">
                    <?php if ($slide_id): ?>
                        <?php echo wp_get_attachment_image(
                            $slide_id,
                            "full",
                            false,
                            [
                                "alt" => esc_attr($hero_title),
                                "loading" => "lazy",
                                "class" => "hero-slide-image",
                            ],
                        ); ?>
                    <?php else: ?>
                        <img src="<?php echo esc_url(
                            $default_images[$index],
                        ); ?>" 
                             alt="<?php echo esc_attr($hero_title); ?>"
                             class="hero-slide-image" // Added class
                             loading="lazy">
                    <?php endif; ?>
                    <div class="slide-overlay"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Static Background -->
    <div class="hero-static-background">
        <?php if ($hero_slide_1): ?>
            <?php echo wp_get_attachment_image($hero_slide_1, "full", false, [
                "alt" => esc_attr($hero_title),
                "class" => "hero-static-image",
                "loading" => "lazy",
            ]); ?>
        <?php else: ?>
            <img src="<?php echo esc_url($default_images[0]); ?>" 
                 alt="<?php echo esc_attr($hero_title); ?>" 
                 class="hero-static-image"
                 loading="lazy">
        <?php endif; ?>
        <div class="slide-overlay"></div>
    </div>
<?php endif; ?>
    <?php endif; ?>

    <!-- Hero Content with Scroll Animations -->
    <div class="hero-content-container">
        <div class="hero-content">
            <?php if ($badge_text): ?>
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
                <?php if ($hero_title_highlight): ?>
                <span class="hero-title-highlight"><?php echo esc_html(
                    $hero_title_highlight,
                ); ?></span>
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
                <?php if ($primary_btn_text): ?>
                    <a href="<?php echo esc_url(
                        $primary_btn_link,
                    ); ?>" class="hero-btn hero-btn-primary">
                        <?php echo esc_html($primary_btn_text); ?>
                        <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($secondary_btn_text): ?>
                    <a href="<?php echo esc_url(
                        $secondary_btn_link,
                    ); ?>" class="hero-btn hero-btn-secondary">
                        <?php echo esc_html($secondary_btn_text); ?>
                        <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Loading indicator for videos -->
    <?php if ($media_type === "video" && $video_url): ?>
        <div class="video-loading" style="display: none;">
            <div class="loading-spinner"></div>
            <span class="loading-text">Loading video...</span>
        </div>
    <?php endif; ?>
</section>