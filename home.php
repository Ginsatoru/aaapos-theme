<?php
/**
 * The template for displaying the blog posts index
 * (used when Settings > Reading > Posts page is set to a static page)
 *
 * UPDATED: Blog card markup now matches the homepage "Latest from Our
 * Blog" design exactly - full-bleed photo, gradient overlay, arrow
 * icon, overlaid title/excerpt. Previously this used a separate
 * thumbnail + content-block layout with a category badge, meta row,
 * and "Read More" link, so styling could never fully match the
 * homepage version even with matching CSS.
 *
 * @package AAAPOS
 */

get_header();
?>

<main id="primary" class="site-main archive-page">
    <div class="container">
        
        <?php if (have_posts()) : ?>
            
            <!-- Page Header -->
            <?php
            $blog_header_bg = function_exists('aaapos_get_shop_header_bg_image') ? aaapos_get_shop_header_bg_image() : '';
            ?>
            <header class="page-header<?php echo $blog_header_bg ? ' has-background-image' : ''; ?>"<?php echo $blog_header_bg ? ' style="background-image:url(' . esc_url($blog_header_bg) . ');"' : ''; ?>>
                <div class="page-header__inner">
                <?php
                if (get_option('page_for_posts')) {
                    echo '<h1 class="page-title">' . esc_html(get_the_title(get_option('page_for_posts'))) . '</h1>';
                } else {
                    echo '<h1 class="page-title">' . esc_html__('Blog', 'aaapos-prime') . '</h1>';
                }
                ?>
                </div>
            </header>

            <!-- Blog Grid -->
            <div class="blog-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    
                    // Get post data
                    $post_id = get_the_ID();
                    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
                    ?>
                    
                    <a id="post-<?php the_ID(); ?>" href="<?php the_permalink(); ?>" class="<?php echo esc_attr(implode(' ', get_post_class('blog-card', $post_id))); ?>">

                        <?php if ($thumbnail_url) : ?>
                            <img
                                src="<?php echo esc_url($thumbnail_url); ?>"
                                alt="<?php the_title_attribute(); ?>"
                                class="blog-card__image"
                                loading="lazy"
                            >
                        <?php endif; ?>

                        <span class="blog-card__arrow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 17L17 7M17 7H8M17 7V16"></path>
                            </svg>
                        </span>

                        <div class="blog-card__overlay">
                            <h3 class="blog-card__title"><?php the_title(); ?></h3>
                            <p class="blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18, '...')); ?></p>
                        </div>

                    </a>
                    
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&laquo; Previous', 'aaapos-prime'),
                'next_text' => __('Next &raquo;', 'aaapos-prime'),
                'class'     => 'pagination',
            ));
            ?>

        <?php else : ?>
            
            <!-- No Posts Found -->
            <div class="no-posts-found">
                <h1 class="no-posts-found__title"><?php esc_html_e('Nothing Found', 'aaapos-prime'); ?></h1>
                <p class="no-posts-found__message">
                    <?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'aaapos-prime'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="no-posts-found__button">
                    <?php esc_html_e('Back to Home', 'aaapos-prime'); ?>
                </a>
            </div>

        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
?>