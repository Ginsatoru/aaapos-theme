<?php
/**
 * Site Footer Template
 * template-parts/footer/site-footer.php
 * 
 * @package AAAPOS
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<footer class="site-footer" role="contentinfo">
    
    <?php 
    // Include footer widgets
    get_template_part('template-parts/footer/footer-widgets'); 
    ?>
    
    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                
                <!-- Copyright Section -->
                <div class="footer-copyright">
                    <?php
                    $copyright_text = get_theme_mod('footer_copyright_text', '© {year} {sitename}. All rights reserved.');
                    $copyright_text = str_replace('{year}', date('Y'), $copyright_text);
                    $copyright_text = str_replace('{sitename}', get_bloginfo('name'), $copyright_text);
                    echo wp_kses_post($copyright_text);
                    ?>
                </div>
                
                <!-- Footer Menu -->
                <?php if (get_theme_mod('footer_show_menu', true) && has_nav_menu('footer')) : ?>
                    <nav class="footer-bottom-menu" aria-label="<?php esc_attr_e('Footer Navigation', 'aaapos-prime'); ?>">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu-list',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
                
                <!-- Payment Icons -->
                <?php if (get_theme_mod('footer_show_payment_icons', true)) : 
                    
                    // Get theme assets URI
                    $assets_uri = get_template_directory_uri() . '/images/payments/';
                    
                    // Define payment methods with their default images
                    $payment_methods = array(
                        'visa' => array(
                            'name' => __('Visa', 'aaapos-prime'),
                            'default_image' => $assets_uri . 'payment-visa.png',
                        ),
                        'mastercard' => array(
                            'name' => __('Mastercard', 'aaapos-prime'),
                            'default_image' => $assets_uri . 'payment-mastercard.png',
                        ),
                        'amex' => array(
                            'name' => __('American Express', 'aaapos-prime'),
                            'default_image' => $assets_uri . 'payment-amex.png',
                        ),
                        'paypal' => array(
                            'name' => __('PayPal', 'aaapos-prime'),
                            'default_image' => $assets_uri . 'payment-paypal.png',
                        ),
                        'discover' => array(
                            'name' => __('Discover', 'aaapos-prime'),
                            'default_image' => $assets_uri . 'payment-discover.png',
                        ),
                    );
                    
                    // Check if any payment method is enabled
                    $has_payment_methods = false;
                    foreach ($payment_methods as $key => $method) {
                        if (get_theme_mod("payment_show_{$key}", true)) {
                            $has_payment_methods = true;
                            break;
                        }
                    }
                    
                    // Display payment icons if at least one is enabled
                    if ($has_payment_methods) :
                ?>
                    <div class="footer-payment-icons">
                        <span class="payment-label"><?php esc_html_e('We Accept:', 'aaapos-prime'); ?></span>
                        <div class="payment-icons-list">
                            <?php foreach ($payment_methods as $key => $method) : 
                                // Check if this specific payment method is enabled
                                if (get_theme_mod("payment_show_{$key}", true)) :
                                    
                                    // Get custom uploaded icon or use default
                                    $icon_id = get_theme_mod("payment_icon_{$key}");
                                    if ($icon_id) {
                                        $icon_url = wp_get_attachment_image_url($icon_id, 'full');
                                    } else {
                                        $icon_url = $method['default_image'];
                                    }
                                    
                                    // Only display if we have an icon URL
                                    if ($icon_url) :
                            ?>
                                <img 
                                    src="<?php echo esc_url($icon_url); ?>" 
                                    alt="<?php echo esc_attr($method['name']); ?>" 
                                    class="payment-icon payment-icon-<?php echo esc_attr($key); ?>"
                                    loading="lazy"
                                    width="60"
                                    height="38"
                                >
                            <?php 
                                    endif;
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php 
                    endif;
                endif; 
                ?>
                
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <?php if (get_theme_mod('footer_show_back_to_top', true)) : 
        $position = get_theme_mod('footer_back_to_top_position', 'right');
    ?>
        <button 
            id="back-to-top" 
            class="back-to-top back-to-top--<?php echo esc_attr($position); ?>" 
            aria-label="<?php esc_attr_e('Back to top', 'aaapos-prime'); ?>"
            style="display: none;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </button>
    <?php endif; ?>
    
</footer>