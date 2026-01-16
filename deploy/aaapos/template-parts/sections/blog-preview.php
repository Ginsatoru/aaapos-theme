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
            <h2 class="section-title"><?php echo esc_html($title); ?></h2>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" 
               class="btn btn--outline">
                <?php esc_html_e('View All Posts', 'AAAPOS'); ?>
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
                    <article class="blog-card" 
                             data-animate="fade-up" 
                             data-animate-delay="<?php echo esc_attr($delay); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="blog-card__image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('mr-blog-card'); ?>
                                </a>
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <span class="blog-card__category">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="blog-card__content">
                            <div class="blog-card__meta">
                                <div class="blog-card__author">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                    <span><?php the_author(); ?></span>
                                </div>
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>

                            <h3 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <div class="blog-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="blog-card__link">
                                <?php esc_html_e('Read More', 'AAAPOS'); ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 01.5-.5h11.793l-3.147-3.146a.5.5 0 01.708-.708l4 4a.5.5 0 010 .708l-4 4a.5.5 0 01-.708-.708L13.293 8.5H1.5A.5.5 0 011 8z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    </article>
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