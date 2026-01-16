<?php
/**
 * The main template file
 */
get_header();
?>

<main id="primary" class="site-main blog-page">
    <div class="container">
        
        <?php if (have_posts()) : ?>
            
            <!-- Page Header -->
            <header class="page-header">
                <?php
                if (is_home() && !is_front_page()) :
                    ?>
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                <?php else : ?>
                    <h1 class="page-title"><?php esc_html_e('Latest Posts', 'macedon-ranges'); ?></h1>
                <?php endif;
                
                // Optional: Add blog description
                $blog_description = get_bloginfo('description');
                if ($blog_description) :
                    ?>
                    <div class="archive-description">
                        <p><?php echo esc_html($blog_description); ?></p>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Blog Grid (changed from posts-grid to blog-grid) -->
            <div class="blog-grid">
                <?php while (have_posts()) : the_post(); 
                    // Get post data
                    $post_id = get_the_ID();
                    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
                    $categories = get_the_category();
                    $author_id = get_the_author_meta('ID');
                    $author_name = get_the_author();
                    $post_date = get_the_date('F j, Y');
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                        
                        <!-- Post Thumbnail -->
                        <?php if ($thumbnail_url) : ?>
                            <div class="blog-card__thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <img 
                                        src="<?php echo esc_url($thumbnail_url); ?>" 
                                        alt="<?php the_title_attribute(); ?>"
                                        loading="lazy"
                                    >
                                </a>
                                
                                <!-- Category Badge -->
                                <?php if (!empty($categories)) : ?>
                                    <div class="blog-card__meta-overlay">
                                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="blog-card__category">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Post Content -->
                        <div class="blog-card__content">
                            
                            <!-- Post Title -->
                            <h2 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <!-- Post Meta -->
                            <div class="blog-card__meta">
                                <div class="blog-card__meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="blog-card__author">
                                        <?php echo esc_html($author_name); ?>
                                    </a>
                                </div>
                                
                                <div class="blog-card__meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="blog-card__date"><?php echo esc_html($post_date); ?></span>
                                </div>
                            </div>
                            
                            <!-- Post Excerpt -->
                            <div class="blog-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <!-- Read More Link -->
                            <a href="<?php the_permalink(); ?>" class="blog-card__read-more">
                                <?php esc_html_e('Read More', 'macedon-ranges'); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                        
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&laquo; Previous', 'macedon-ranges'),
                'next_text' => __('Next &raquo;', 'macedon-ranges'),
                'class'     => 'pagination',
            ));
            ?>

        <?php else : ?>
            
            <!-- No Posts Found -->
            <div class="no-posts-found">
                <h1 class="no-posts-found__title"><?php esc_html_e('Nothing Found', 'macedon-ranges'); ?></h1>
                <p class="no-posts-found__message">
                    <?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'macedon-ranges'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="no-posts-found__button">
                    <?php esc_html_e('Back to Home', 'macedon-ranges'); ?>
                </a>
            </div>

        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
?>