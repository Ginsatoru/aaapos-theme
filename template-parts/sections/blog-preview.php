<?php
/**
 * Blog preview section with Scroll Animations
 */
$title = get_theme_mod('blog_title', 'Latest from Our Blog');
$posts_count = get_theme_mod('blog_posts_count', 3);

$blog_posts = new WP_Query(array(
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
    'ignore_sticky_posts' => true
));
?>
<section class="blog-preview section">
    <div class="container">
        <!-- Section Header with Animation -->
        <div class="section-header" 
             data-animate="fade-up" 
             data-animate-delay="100">
            <div class="section-header-text">
                <h2 class="section-title"><?php echo esc_html($title); ?></h2>
            </div>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" 
               class="section-header-link btn-outline">
                <?php esc_html_e('View All Posts', 'AAAPOS'); ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>

        <?php if ($blog_posts->have_posts()) : ?>
            <div class="blog-grid">
                <?php 
                $delay = 200; // Starting delay for stagger
                while ($blog_posts->have_posts()) : 
                    $blog_posts->the_post(); 
                ?>
                    <!-- Blog Card with Staggered Animation -->
                    <a href="<?php the_permalink(); ?>" 
                       class="blog-card"
                       data-animate="fade-up" 
                       data-animate-delay="<?php echo esc_attr($delay); ?>">

                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('mr-blog-card', array('class' => 'blog-card__image')); ?>
                        <?php else: ?>
                            <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="" class="blog-card__image">
                        <?php endif; ?>

                        <span class="blog-card__arrow">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="7" y1="17" x2="17" y2="7"></line>
                                <polyline points="7 7 17 7 17 17"></polyline>
                            </svg>
                        </span>

                        <span class="blog-card__overlay">
                            <span class="blog-card__title"><?php the_title(); ?></span>
                            <span class="blog-card__excerpt"><?php echo esc_html(
                                wp_trim_words(get_the_excerpt(), 16, '...')
                            ); ?></span>
                        </span>
                    </a>
                <?php 
                    $delay += 150; // Increment delay for stagger
                endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="no-posts" 
               data-animate="fade-up" 
               data-animate-delay="200">
                <?php esc_html_e('No blog posts found.', 'AAAPOS'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>