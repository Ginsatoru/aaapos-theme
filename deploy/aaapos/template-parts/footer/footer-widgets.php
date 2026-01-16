<?php
/**
 * Footer widget areas
 * 
 * @package AAAPOS
 */

// Check if we should display footer widgets
$has_widgets = false;
for ($i = 1; $i <= 4; $i++) {
    if (is_active_sidebar('footer-' . $i)) {
        $has_widgets = true;
        break;
    }
}
?>

<?php if ($has_widgets || !is_active_sidebar('footer-1')) : ?>
<div class="footer-widgets">
    <div class="container">
        <div class="footer-widgets-grid">
            <!-- Footer Column 1: About/Brand -->
            <div class="footer-widget footer-brand">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <div class="widget">
                        <?php if (has_custom_logo()) : ?>
                            <div class="footer-logo">
                                <?php the_custom_logo(); ?>
                            </div>
                        <?php else : ?>
                            <h4 class="widget-title"><?php bloginfo('name'); ?></h4>
                        <?php endif; ?>
                        
                        <p class="footer-description">
                            <?php 
                            $description = get_bloginfo('description');
                            if ($description) {
                                echo esc_html($description);
                            } else {
                                esc_html_e('Your trusted source for quality products and exceptional service.', 'AAAPOS');
                            }
                            ?>
                        </p>
                        
                        <!-- Social Links -->
                        <?php
                        $social_platforms = array(
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'twitter' => 'Twitter',
                            'youtube' => 'YouTube',
                            'linkedin' => 'LinkedIn'
                        );
                        
                        $has_social = false;
                        foreach ($social_platforms as $platform => $label) {
                            if (get_theme_mod($platform . '_url')) {
                                $has_social = true;
                                break;
                            }
                        }
                        
                        if ($has_social) :
                        ?>
                            <div class="footer-social-links">
                                <h5 class="social-title"><?php esc_html_e('Follow Us', 'AAAPOS'); ?></h5>
                                <div class="social-links">
                                    <?php foreach ($social_platforms as $platform => $label) :
                                        $url = get_theme_mod($platform . '_url');
                                        if ($url) :
                                    ?>
                                        <a href="<?php echo esc_url($url); ?>" 
                                           class="social-link" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           title="<?php echo esc_attr($label); ?>">
                                            <span class="sr-only"><?php echo esc_html($label); ?></span>
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                                <?php if ($platform === 'facebook') : ?>
                                                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                                <?php elseif ($platform === 'instagram') : ?>
                                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                                <?php elseif ($platform === 'twitter') : ?>
                                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                                <?php elseif ($platform === 'youtube') : ?>
                                                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                                                <?php elseif ($platform === 'linkedin') : ?>
                                                    <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                                                <?php endif; ?>
                                            </svg>
                                        </a>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer Column 2: Quick Links -->
            <div class="footer-widget">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <div class="widget">
                        <h4 class="widget-title"><?php esc_html_e('Quick Links', 'AAAPOS'); ?></h4>
                        <ul class="footer-menu">
                            <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About Us', 'AAAPOS'); ?></a></li>
                            <?php if (class_exists('WooCommerce')) : ?>
                                <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Shop', 'AAAPOS'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Blog', 'AAAPOS'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php esc_html_e('Contact', 'AAAPOS'); ?></a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer Column 3: Customer Service -->
            <div class="footer-widget">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <div class="widget">
                        <h4 class="widget-title"><?php esc_html_e('Customer Service', 'AAAPOS'); ?></h4>
                        <ul class="footer-menu">
                            <?php if (class_exists('WooCommerce')) : ?>
                                <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('My Account', 'AAAPOS'); ?></a></li>
                                <li><a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>"><?php esc_html_e('Shopping Cart', 'AAAPOS'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo esc_url(home_url('/shipping')); ?>"><?php esc_html_e('Shipping Info', 'AAAPOS'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/returns')); ?>"><?php esc_html_e('Returns', 'AAAPOS'); ?></a></li>
                            <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy Policy', 'AAAPOS'); ?></a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer Column 4: Newsletter -->
            <div class="footer-widget">
                <?php if (is_active_sidebar('footer-4')) : ?>
                    <?php dynamic_sidebar('footer-4'); ?>
                <?php else : ?>
                    <div class="widget widget-newsletter">
                        <h4 class="widget-title"><?php esc_html_e('Newsletter', 'AAAPOS'); ?></h4>
                        <p><?php esc_html_e('Subscribe to get special offers and updates.', 'AAAPOS'); ?></p>
                        <form class="newsletter-form" method="post">
                            <div class="form-group">
                                <input 
                                    type="email" 
                                    name="subscriber_email" 
                                    placeholder="<?php esc_attr_e('Your email', 'AAAPOS'); ?>" 
                                    required
                                >
                                <button type="submit" class="btn-subscribe">
                                    <?php esc_html_e('Subscribe', 'AAAPOS'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>