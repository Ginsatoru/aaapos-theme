<?php
/**
 * The template for displaying comments
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $mr_comment_count = get_comments_number();
            if ('1' === $mr_comment_count) {
                printf(
                    esc_html__('One thought on &ldquo;%s&rdquo;', 'macedon-ranges'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    esc_html__('%1$s thoughts on &ldquo;%2$s&rdquo;', 'macedon-ranges'),
                    number_format_i18n($mr_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size' => 60,
                )
            );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'macedon-ranges'); ?></p>
            <?php
        endif;

    endif;

    comment_form();
    ?>
</div>