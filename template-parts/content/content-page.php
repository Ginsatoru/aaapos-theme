<?php
/**
 * Page content template
 */

// Determine if we should show the title
$show_title = true;

// Hide title for Elementor pages on the front page
if (is_front_page() && did_action('elementor/loaded')) {
    // Check if this page is built with Elementor
    if (class_exists('\Elementor\Plugin')) {
        $document = \Elementor\Plugin::$instance->documents->get(get_the_ID());
        if ($document && $document->is_built_with_elementor()) {
            $show_title = false;
        }
    }
}

// Allow override via custom field (optional - gives you manual control)
$custom_hide_title = get_post_meta(get_the_ID(), '_hide_page_title', true);
if ($custom_hide_title === 'yes') {
    $show_title = false;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ($show_title) : ?>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header>
    <?php endif; ?>

    <?php mr_post_thumbnail(); ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'macedon-ranges'),
                'after'  => '</div>',
            )
        );
        ?>
    </div>

    <?php if (get_edit_post_link()) : ?>
        <footer class="entry-footer">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        __('Edit <span class="screen-reader-text">%s</span>', 'macedon-ranges'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer>
    <?php endif; ?>
</article>