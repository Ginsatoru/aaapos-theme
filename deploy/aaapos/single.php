<?php
/**
 * The template for displaying single posts
 * 
 * @package Macedon_Ranges
 */

get_header();
?>

<main id="main" class="site-main single-post-page">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            
            // Load the styled single post template
            get_template_part('template-parts/content/content', 'single');
            
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();