<?php
/**
 * Single post content template - Styled version
 * content-single.php
 * @package Macedon_Ranges
 */

// Get post data
$post_id = get_the_ID();
$categories = get_the_category();
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_avatar = get_avatar($author_id, 48);
$post_date = get_the_date('F j, Y');
$reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200); // Estimate reading time
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
    
    <!-- Post Header -->
    <header class="single-post__header">
        
        <!-- Categories -->
        <?php if (!empty($categories)) : ?>
            <div class="single-post__categories">
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="single-post__category">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Title -->
        <h1 class="single-post__title"><?php the_title(); ?></h1>
        
        <!-- Meta Information -->
        <div class="single-post__meta">
            <div class="single-post__author-info">
                <div class="single-post__avatar">
                    <?php echo $author_avatar; ?>
                </div>
                <div class="single-post__author-details">
                    <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="single-post__author-name">
                        <?php echo esc_html($author_name); ?>
                    </a>
                    <div class="single-post__meta-secondary">
                        <time datetime="<?php echo get_the_date('c'); ?>" class="single-post__date">
                            <?php echo esc_html($post_date); ?>
                        </time>
                        <span class="single-post__separator">·</span>
                        <span class="single-post__reading-time"><?php echo esc_html($reading_time); ?> min read</span>
                    </div>
                </div>
            </div>
        </div>
        
    </header>

    <!-- Featured Image -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="single-post__featured-image">
            <?php the_post_thumbnail('large', array('class' => 'single-post__image')); ?>
        </div>
    <?php endif; ?>

    <!-- Post Content -->
    <div class="single-post__content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links"><span class="page-links__title">' . esc_html__('Pages:', 'macedon-ranges') . '</span>',
                'after'  => '</div>',
            )
        );
        ?>
    </div>

    <!-- Post Footer -->
    <footer class="single-post__footer">
        
        <!-- Tags -->
        <?php
        $tags = get_the_tags();
        if ($tags) :
        ?>
            <div class="single-post__tags">
                <span class="single-post__tags-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <?php esc_html_e('Tags:', 'macedon-ranges'); ?>
                </span>
                <div class="single-post__tags-list">
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="single-post__tag">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Share Buttons (Optional - you can add social sharing here) -->
        <div class="single-post__share">
            <span class="single-post__share-label"><?php esc_html_e('Share this post:', 'macedon-ranges'); ?></span>
            <!-- Add your social sharing buttons here if needed -->
        </div>
        
    </footer>

    <!-- Author Bio -->
    <?php
    $author_description = get_the_author_meta('description', $author_id);
    if ($author_description) :
    ?>
        <div class="single-post__author-bio">
            <div class="author-bio">
                <div class="author-bio__avatar">
                    <?php echo get_avatar($author_id, 80); ?>
                </div>
                <div class="author-bio__content">
                    <h3 class="author-bio__name">
                        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>">
                            <?php echo esc_html($author_name); ?>
                        </a>
                    </h3>
                    <p class="author-bio__description"><?php echo wp_kses_post($author_description); ?></p>
                    <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="author-bio__link">
                        <?php esc_html_e('View all posts', 'macedon-ranges'); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

</article>