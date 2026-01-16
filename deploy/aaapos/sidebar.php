<?php
/**
 * The sidebar containing the main widget area
 */
if (!is_active_sidebar('shop-sidebar')) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
    <?php dynamic_sidebar('shop-sidebar'); ?>
</aside><!-- #secondary -->