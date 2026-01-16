<?php
/**
 * Template Name: About Us Page
 * Template Post Type: page
 *
 * @package AAAPOS
 */

if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<div class="about-page-redesign">
    
    <!-- Hero Section - Split Layout -->
    <?php if (get_theme_mod("about_show_hero", true)): ?>
    <section class="about-hero">
        <div class="container">
            <div class="about-hero__grid">
                <div class="about-hero__content" data-animate="fade-right">
                    <span class="about-hero__badge"><?php echo esc_html(
                        get_theme_mod("about_hero_badge", "Our Journey"),
                    ); ?></span>
                    
                    <h1 class="about-hero__title">
                        <?php
                        $hero_title = get_theme_mod(
                            "about_hero_title",
                            "Built on Trust, Driven by Quality",
                        );
                        $title_parts = explode(",", $hero_title);
                        if (count($title_parts) > 1) {
                            echo esc_html(trim($title_parts[0])) . ",<br>";
                            echo '<span class="about-hero__title-accent">' .
                                esc_html(trim($title_parts[1])) .
                                "</span>";
                        } else {
                            echo esc_html($hero_title);
                        }
                        ?>
                    </h1>
                    
                    <p class="about-hero__intro">
                        <?php echo esc_html(
                            get_theme_mod(
                                "about_hero_intro",
                                'For over a decade, we\'ve been more than just a supplier—we\'re your partner in providing the very best for your animals. From family pets to farm livestock, quality isn\'t just our promise, it\'s our foundation.',
                            ),
                        ); ?>
                    </p>
                    
                    <?php if (get_theme_mod("about_show_hero_meta", true)): ?>
                    <div class="about-hero__meta">
                        <div class="about-hero__meta-item">
                            <strong>Since <?php echo esc_html(
                                get_theme_mod("about_since_year", "2013"),
                            ); ?></strong>
                            <span>Serving the community</span>
                        </div>
                        <div class="about-hero__meta-item">
                            <strong><?php echo esc_html(
                                get_theme_mod(
                                    "about_meta1_title",
                                    "Family Owned",
                                ),
                            ); ?></strong>
                            <span><?php echo esc_html(
                                get_theme_mod(
                                    "about_meta1_text",
                                    "Local & trusted",
                                ),
                            ); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="about-hero__visual" data-animate="fade-left" data-animate-delay="200">
                    <?php
                    $hero_image = get_theme_mod("about_hero_image");
                    if ($hero_image): ?>
                        <img src="<?php echo esc_url($hero_image); ?>" 
                             alt="<?php echo esc_attr(
                                 get_theme_mod("about_hero_title", "Our Story"),
                             ); ?>" 
                             class="about-hero__image">
                    <?php elseif (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail("large", [
                            "class" => "about-hero__image",
                        ]); ?>
                    <?php else: ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about-hero-bg.jpg" 
                             alt="Our Story" 
                             class="about-hero__image">
                    <?php endif;
                    ?>
                    
                    <?php if (get_theme_mod("about_show_stat_card", true)): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Story Section -->
    <?php if (get_theme_mod("about_show_story", true)): ?>
    <section class="about-story">
        <div class="container">
            <div class="about-story__wrapper">
                
                <div class="about-story__content" data-animate="fade-up">
                    <h2 class="about-story__heading"><?php echo esc_html(
                        get_theme_mod("about_story_heading", "Our Story"),
                    ); ?></h2>
                    
                    <?php // Display the page content from WordPress editor (if it exists and is not placeholder)
                    if (have_posts()):
                        while (have_posts()):
                            the_post();
                            $content = get_the_content();
                            // Only show content if it's not empty and not the default placeholder
                            if (
                                $content &&
                                trim(strip_tags($content)) &&
                                trim(strip_tags($content)) !=
                                    "Tell your story here. Edit this page to add your content."
                            ): ?>
                        <div class="about-story__text">
                            <?php the_content(); ?>
                        </div>
                    <?php endif;
                        endwhile;
                    endif; ?>
                    
                    <?php if (get_theme_mod("about_quote")): ?>
                    <div class="about-story__highlight">
                        <svg class="about-story__quote-icon" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                        </svg>
                        <p class="about-story__quote">
                            <?php echo esc_html(
                                get_theme_mod(
                                    "about_quote",
                                    "RetailManager started as a tiny idea in a back office where things were… honestly, a mess. Stock was everywhere, sales were being tracked on random spreadsheets, and running a retail shop felt way harder than it needed to be.",
                                ),
                            ); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Values Section -->
    <?php if (get_theme_mod("about_show_values", true)): ?>
    <section class="about-values">
        <div class="container">
            
            <div class="about-values__header" data-animate="fade-up">
                <h2 class="about-values__title"><?php echo esc_html(
                    get_theme_mod("about_values_title", "What Drives Us"),
                ); ?></h2>
                <p class="about-values__subtitle"><?php echo esc_html(
                    get_theme_mod(
                        "about_values_subtitle",
                        "The principles that guide everything we do",
                    ),
                ); ?></p>
            </div>
            
            <div class="about-values__grid">
                
                <?php
                // Default values
                $value_defaults = [
                    1 => [
                        "title" => "Quality First",
                        "text" =>
                            "We source only the finest products and maintain the highest standards in everything we deliver. No compromises.",
                    ],
                    2 => [
                        "title" => "Customer Focus",
                        "text" =>
                            "Your satisfaction drives us. We listen, adapt, and continuously improve based on your needs.",
                    ],
                    3 => [
                        "title" => "Sustainability",
                        "text" =>
                            "Committed to environmental responsibility and supporting local communities for a better tomorrow.",
                    ],
                    4 => [
                        "title" => "Reliability",
                        "text" =>
                            "Count on us for consistent service, timely delivery, and dependable support every single time.",
                    ],
                ];

                for ($i = 1; $i <= 4; $i++):
                    if (get_theme_mod("about_show_value{$i}", true)): ?>
                    <!-- Value <?php echo $i; ?> -->
                    <article class="value-card" data-animate="fade-up" data-animate-delay="<?php echo $i *
                        100; ?>">
                        <div class="value-card__icon-wrapper">
                            <?php echo aaapos_get_default_value_icon($i); ?>
                        </div>
                        <h3 class="value-card__title"><?php echo esc_html(
                            get_theme_mod(
                                "about_value{$i}_title",
                                $value_defaults[$i]["title"],
                            ),
                        ); ?></h3>
                        <p class="value-card__text">
                            <?php echo esc_html(
                                get_theme_mod(
                                    "about_value{$i}_text",
                                    $value_defaults[$i]["text"],
                                ),
                            ); ?>
                        </p>
                    </article>
                <?php endif;
                endfor;
                ?>
                
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Milestone Timeline -->
    <?php if (get_theme_mod("about_show_timeline", true)): ?>
    <section class="about-timeline">
        <div class="container">
            
            <div class="about-timeline__header" data-animate="fade-up">
                <h2 class="about-timeline__title"><?php echo esc_html(
                    get_theme_mod("about_timeline_title", "Our Journey"),
                ); ?></h2>
            </div>
            
            <div class="timeline">
                
                <?php
                // Timeline defaults
                $timeline_defaults = [
                    1 => [
                        "year" => "2013",
                        "title" => "The Beginning",
                        "text" =>
                            "Started as a small family operation with a vision to provide quality animal supplies locally.",
                    ],
                    2 => [
                        "year" => "2016",
                        "title" => "Expansion",
                        "text" =>
                            "Moved to a larger facility and expanded our product range to serve more animals.",
                    ],
                    3 => [
                        "year" => "2019",
                        "title" => "1,000 Customers",
                        "text" =>
                            "Celebrated serving over 1,000 satisfied customers across the region.",
                    ],
                    4 => [
                        "year" => "2023",
                        "title" => "Going Digital",
                        "text" =>
                            "Launched our online store to serve you better, anytime, anywhere.",
                    ],
                ];

                for ($i = 1; $i <= 4; $i++):
                    if (get_theme_mod("about_show_timeline{$i}", true)): ?>
                    <div class="timeline-item" data-animate="<?php echo $i %
                        2 ==
                    1
                        ? "fade-right"
                        : "fade-left"; ?>" data-animate-delay="<?php echo $i *
    100; ?>">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-year"><?php echo esc_html(
                                get_theme_mod(
                                    "about_timeline{$i}_year",
                                    $timeline_defaults[$i]["year"],
                                ),
                            ); ?></span>
                            <h3 class="timeline-title"><?php echo esc_html(
                                get_theme_mod(
                                    "about_timeline{$i}_title",
                                    $timeline_defaults[$i]["title"],
                                ),
                            ); ?></h3>
                            <p class="timeline-text"><?php echo esc_html(
                                get_theme_mod(
                                    "about_timeline{$i}_text",
                                    $timeline_defaults[$i]["text"],
                                ),
                            ); ?></p>
                        </div>
                    </div>
                <?php endif;
                endfor;
                ?>
                
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Team Section -->
    <?php if (get_theme_mod("about_show_team", true)): ?>
    <section class="about-team">
        <div class="container">
            
            <div class="about-team__header" data-animate="fade-up">
                <h2 class="about-team__title"><?php echo esc_html(
                    get_theme_mod(
                        "about_team_title",
                        "Meet the People Behind the Promise",
                    ),
                ); ?></h2>
                <p class="about-team__subtitle"><?php echo esc_html(
                    get_theme_mod(
                        "about_team_subtitle",
                        "The passionate team dedicated to serving you and your animals",
                    ),
                ); ?></p>
            </div>
            
            <div class="about-team__grid">
                
                <?php
                $team_images = [
                    1 => "team.png",
                    2 => "team1.png",
                    3 => "team2.png",
                ];

                $team_defaults = [
                    1 => [
                        "name" => "John Smith",
                        "role" => "Founder & CEO",
                        "quote" => "Every animal deserves the very best care.",
                    ],
                    2 => [
                        "name" => "Sarah Johnson",
                        "role" => "Operations Manager",
                        "quote" => "Smooth operations, happy customers.",
                    ],
                    3 => [
                        "name" => "Michael Chen",
                        "role" => "Head of Quality",
                        "quote" =>
                            'Quality isn\'t negotiable, it\'s essential.',
                    ],
                ];

                for ($i = 1; $i <= 3; $i++):
                    if (get_theme_mod("about_show_team{$i}", true)): ?>
                    <!-- Team Member <?php echo $i; ?> -->
                    <article class="team-member" data-animate="fade-up" data-animate-delay="<?php echo $i *
                        100; ?>">
                        <div class="team-member__image-wrapper">
                            <?php
                            $team_image = get_theme_mod("about_team{$i}_image");
                            if ($team_image): ?>
                                <img src="<?php echo esc_url($team_image); ?>" 
                                     alt="<?php echo esc_attr(
                                         get_theme_mod("about_team{$i}_name"),
                                     ); ?>" 
                                     class="team-member__image">
                            <?php else: ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo $team_images[
    $i
]; ?>" 
                                     alt="<?php echo esc_attr(
                                         get_theme_mod("about_team{$i}_name"),
                                     ); ?>" 
                                     class="team-member__image">
                            <?php endif;
                            ?>
                        </div>
                        <div class="team-member__content">
                            <h3 class="team-member__name"><?php echo esc_html(
                                get_theme_mod(
                                    "about_team{$i}_name",
                                    $team_defaults[$i]["name"],
                                ),
                            ); ?></h3>
                            <span class="team-member__role"><?php echo esc_html(
                                get_theme_mod(
                                    "about_team{$i}_role",
                                    $team_defaults[$i]["role"],
                                ),
                            ); ?></span>
                            <p class="team-member__quote">"<?php echo esc_html(
                                get_theme_mod(
                                    "about_team{$i}_quote",
                                    $team_defaults[$i]["quote"],
                                ),
                            ); ?>"</p>
                        </div>
                    </article>
                <?php endif;
                endfor;
                ?>
                
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
<?php if (get_theme_mod("about_show_cta", true)): ?>
<section class="about-cta">
    <div class="container">
        <?php 
        $cta_bg_image = get_theme_mod("about_cta_bg_image");
        $cta_overlay_opacity = get_theme_mod("about_cta_overlay_opacity", "0.6");
        
        // Use custom image, or fallback to ctabg.jpg
        $background_image = $cta_bg_image ? $cta_bg_image : get_template_directory_uri() . '/assets/images/ctabg.jpg';
        
        $cta_style = 'style="background-image: url(' . esc_url($background_image) . ');"';
        $overlay_style = 'style="opacity: ' . esc_attr($cta_overlay_opacity) . ';"';
        ?>
        <div class="about-cta__card" data-animate="zoom-in" <?php echo $cta_style; ?>>
            <div class="about-cta__card-overlay" <?php echo $overlay_style; ?>></div>
            <div class="about-cta__content">
                <h2 class="about-cta__title"><?php echo esc_html(
                    get_theme_mod(
                        "about_cta_title",
                        "Ready to Experience the Difference?",
                    ),
                ); ?></h2>
                <p class="about-cta__text">
                    <?php echo esc_html(
                        get_theme_mod(
                            "about_cta_text",
                            "Join thousands of satisfied customers who trust us for quality products and exceptional service.",
                        ),
                    ); ?>
                </p>
                
                <div class="about-cta__buttons">
                    <?php if (
                        class_exists("WooCommerce") &&
                        get_theme_mod("about_show_cta_shop_btn", true)
                    ): ?>
                        <a href="<?php echo esc_url(
                            get_permalink(wc_get_page_id("shop")),
                        ); ?>" class="btn-about btn-about--primary">
                            <span><?php echo esc_html(
                                get_theme_mod(
                                    "about_cta_shop_text",
                                    "Shop Now",
                                ),
                            ); ?></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (
                        get_theme_mod("about_show_cta_contact_btn", true)
                    ): ?>
                        <a href="<?php echo esc_url(
                            get_theme_mod(
                                "about_cta_contact_url",
                                home_url("/contact"),
                            ),
                        ); ?>" class="btn-about btn-about--secondary">
                            <span><?php echo esc_html(
                                get_theme_mod(
                                    "about_cta_contact_text",
                                    "Get in Touch",
                                ),
                            ); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

</div>

<?php
get_footer();

// Helper function for default icons
function aaapos_get_default_value_icon($index)
{
    $icons = [
        1 => '<svg class="value-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
        2 => '<svg class="value-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        3 => '<svg class="value-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
        4 => '<svg class="value-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    ];
    return isset($icons[$index]) ? $icons[$index] : "";
}

?>
