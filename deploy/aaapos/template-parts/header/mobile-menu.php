<?php
/**
 * Mobile menu component
 * 
 * @package AAAPOS
 */
?>
<div id="mobile-menu" class="mobile-menu" aria-hidden="true">
    <nav class="mobile-navigation" role="navigation" aria-label="<?php esc_attr_e('Mobile Navigation', 'AAAPOS'); ?>">
        <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 3,
            ));
        } else {
            // Fallback menu
            echo '<ul class="nav-menu">';
            echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'AAAPOS') . '</a></li>';
            if (class_exists('WooCommerce')) {
                echo '<li><a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">' . esc_html__('Shop', 'AAAPOS') . '</a></li>';
            }
            echo '<li><a href="' . esc_url(home_url('/about')) . '">' . esc_html__('About', 'AAAPOS') . '</a></li>';
            echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . esc_html__('Contact', 'AAAPOS') . '</a></li>';
            echo '</ul>';
        }
        ?>
    </nav>

    <?php if (class_exists('WooCommerce')) : ?>
        <div class="mobile-menu-actions">
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="mobile-menu-action">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <span><?php esc_html_e('My Account', 'AAAPOS'); ?></span>
            </a>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="mobile-menu-action">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                </svg>
                <span><?php esc_html_e('Cart', 'AAAPOS'); ?></span>
                <?php 
                $cart_count = WC()->cart->get_cart_contents_count();
                if ($cart_count > 0) : 
                ?>
                    <span class="cart-count"><?php echo esc_html($cart_count); ?></span>
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>
</div><!-- .mobile-menu -->