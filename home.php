<?php
/**
 * The template for displaying the blog posts index
 * (used when Settings > Reading > Posts page is set to a static page)
 *
 * UPDATED: Blog page redesigned as a magazine layout - featured post
 * (large image, category pill, title, meta) beside a "Latest post" list,
 * a scrollable "More posts" card row with arrows, and numbered circle
 * pagination with prev/next arrows. Styles live in
 * assets/css/components/archive-blog.css (.blog-mag-* classes).
 *
 * @package AAAPOS
 */

get_header();
?>

<main id="primary" class="site-main archive-page">
    <div class="container">
        
        <?php if (have_posts()) : ?>

            <?php
            // Collect this page's posts so they can be split across the sections
            $blog_posts = array();
            while (have_posts()) {
                the_post();
                $blog_posts[] = get_post();
            }
            wp_reset_postdata();

            $blog_hero   = array_shift($blog_posts);          // 1 featured post
            $blog_latest = array_splice($blog_posts, 0, 4);   // next 4 for the "Latest post" list
            $blog_more   = $blog_posts;                       // everything else -> "More posts" row

            // Meta line: "Aug 10 • 10 min read"
            $blog_meta = function ($p) {
                $words = str_word_count(wp_strip_all_tags(strip_shortcodes($p->post_content)));
                $mins  = max(1, (int) ceil($words / 200));
                return '<span class="blog-mag-meta">' . esc_html(get_the_date('M j', $p)) .
                    '<i class="blog-mag-meta__dot"></i>' .
                    esc_html(sprintf(__('%d min read', 'aaapos-prime'), $mins)) . '</span>';
            };

            // Category pill (plain span - the whole card is already a link)
            $blog_pill = function ($p) {
                $cats = get_the_category($p->ID);
                if (empty($cats) || $cats[0]->slug === 'uncategorized') {
                    return '';
                }
                return '<span class="blog-mag-pill"><i class="blog-mag-pill__dot"></i>' . esc_html($cats[0]->name) . '</span>';
            };
            ?>

            <!-- Page title (kept for SEO / screen readers) -->
            <h1 class="screen-reader-text">
                <?php
                echo get_option('page_for_posts')
                    ? esc_html(get_the_title(get_option('page_for_posts')))
                    : esc_html__('Blog', 'aaapos-prime');
                ?>
            </h1>

            <div class="blog-mag">

                <!-- Top: featured post + latest list -->
                <section class="blog-mag-top">

                    <?php if ($blog_hero) : ?>
                        <?php $hero_img = get_the_post_thumbnail_url($blog_hero->ID, 'large'); ?>
                        <a id="post-<?php echo (int) $blog_hero->ID; ?>" href="<?php echo esc_url(get_permalink($blog_hero)); ?>" class="blog-mag-hero">
                            <?php if ($hero_img) : ?>
                                <img src="<?php echo esc_url($hero_img); ?>" alt="<?php echo esc_attr(get_the_title($blog_hero)); ?>" class="blog-mag-hero__image">
                            <?php endif; ?>
                            <div class="blog-mag-hero__overlay">
                                <?php echo $blog_pill($blog_hero); ?>
                                <h2 class="blog-mag-hero__title"><?php echo esc_html(get_the_title($blog_hero)); ?></h2>
                                <?php echo $blog_meta($blog_hero); ?>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($blog_latest)) : ?>
                        <aside class="blog-mag-latest">
                            <h2 class="blog-mag-heading"><?php esc_html_e('Latest post', 'aaapos-prime'); ?></h2>
                            <div class="blog-mag-latest__list">
                                <?php foreach ($blog_latest as $p) : $img = get_the_post_thumbnail_url($p->ID, 'medium'); ?>
                                    <a id="post-<?php echo (int) $p->ID; ?>" href="<?php echo esc_url(get_permalink($p)); ?>" class="blog-mag-latest__item">
                                        <span class="blog-mag-latest__thumb">
                                            <?php if ($img) : ?>
                                                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>" loading="lazy">
                                            <?php endif; ?>
                                        </span>
                                        <span class="blog-mag-latest__body">
                                            <span class="blog-mag-latest__title"><?php echo esc_html(get_the_title($p)); ?></span>
                                            <?php echo $blog_meta($p); ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </aside>
                    <?php endif; ?>

                </section>

                <!-- Bottom: more posts row with arrows -->
                <?php if (!empty($blog_more)) : ?>
                    <section class="blog-mag-more">
                        <div class="blog-mag-more__head">
                            <h2 class="blog-mag-heading"><?php esc_html_e('More posts', 'aaapos-prime'); ?></h2>
                            <div class="blog-mag-more__nav">
                                <button type="button" class="blog-mag-arrow" data-blog-scroll="prev" aria-label="<?php esc_attr_e('Previous posts', 'aaapos-prime'); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
                                </button>
                                <button type="button" class="blog-mag-arrow" data-blog-scroll="next" aria-label="<?php esc_attr_e('Next posts', 'aaapos-prime'); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="blog-mag-more__track">
                            <?php foreach ($blog_more as $p) : $img = get_the_post_thumbnail_url($p->ID, 'medium_large'); ?>
                                <a id="post-<?php echo (int) $p->ID; ?>" href="<?php echo esc_url(get_permalink($p)); ?>" class="blog-mag-card">
                                    <span class="blog-mag-card__image">
                                        <?php if ($img) : ?>
                                            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title($p)); ?>" loading="lazy">
                                        <?php endif; ?>
                                    </span>
                                    <?php echo $blog_pill($p); ?>
                                    <h3 class="blog-mag-card__title"><?php echo esc_html(get_the_title($p)); ?></h3>
                                    <p class="blog-mag-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($p), 22, '...')); ?></p>
                                    <?php echo $blog_meta($p); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Pagination -->
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg><span class="screen-reader-text">' . esc_html__('Previous', 'aaapos-prime') . '</span>',
                    'next_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg><span class="screen-reader-text">' . esc_html__('Next', 'aaapos-prime') . '</span>',
                    'class'     => 'pagination',
                ));
                ?>

            </div>

            <script>
            (function () {
                var track = document.querySelector('.blog-mag-more__track');
                if (!track) { return; }
                var nav = document.querySelector('.blog-mag-more__nav');
                var buttons = document.querySelectorAll('[data-blog-scroll]');
                buttons.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var card = track.querySelector('.blog-mag-card');
                        var step = card ? card.getBoundingClientRect().width + 24 : track.clientWidth;
                        track.scrollBy({ left: btn.getAttribute('data-blog-scroll') === 'next' ? step : -step, behavior: 'smooth' });
                    });
                });
                function toggleNav() {
                    if (nav) { nav.style.display = track.scrollWidth > track.clientWidth + 2 ? '' : 'none'; }
                }
                toggleNav();
                window.addEventListener('resize', toggleNav);
            })();
            </script>

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