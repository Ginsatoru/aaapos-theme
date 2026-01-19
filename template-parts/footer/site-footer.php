<?php
/**
 * Site Footer Template - Modernized with Image-based Payment Icons
 * UPDATED: Footer categories now sync with homepage category order
 * site-footer.php
 * @package aaapos-prime
 * @since 1.0.0
 */

// Get customizer settings
$footer_layout = get_theme_mod('footer_layout', '4-columns');
$footer_width = get_theme_mod('footer_width', 'boxed');
$show_logo = get_theme_mod('footer_show_logo', true);
$show_social = get_theme_mod('footer_show_social', true);
$show_back_to_top = get_theme_mod('footer_show_back_to_top', true);
$show_footer_menu = get_theme_mod('footer_show_menu', true);
$show_payment_icons = get_theme_mod('footer_show_payment_icons', true);
$back_to_top_position = get_theme_mod('footer_back_to_top_position', 'right');
$social_icon_style = get_theme_mod('social_icon_style', 'rounded');
?>

<footer class="site-footer footer-layout-<?php echo esc_attr($footer_layout); ?> footer-width-<?php echo esc_attr($footer_width); ?>" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
    
    <!-- Footer Main Content -->
    <div class="footer-main">
        <div class="container">
            <div class="footer-widgets-grid">
                
                <!-- Column 1: Brand & Description -->
                <div class="footer-widget footer-brand">
                    <?php if ($show_logo): ?>
                        <div class="footer-logo">
                            <?php 
                            // Check for custom footer logo first
                            $footer_logo_id = get_theme_mod('footer_logo');
                            
                            if ($footer_logo_id) {
                                // Use custom footer logo
                                $logo_url = wp_get_attachment_image_url($footer_logo_id, 'full');
                                $logo_alt = get_post_meta($footer_logo_id, '_wp_attachment_image_alt', true);
                                ?>
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php bloginfo('name'); ?>">
                                    <img src="<?php echo esc_url($logo_url); ?>" 
                                         alt="<?php echo esc_attr($logo_alt ? $logo_alt : get_bloginfo('name')); ?>"
                                         loading="lazy">
                                </a>
                                <?php
                            } elseif (has_custom_logo()) {
                                // Use main site logo
                                the_custom_logo();
                            } else {
                                // Fallback to site title
                                ?>
                                <div class="footer-logo-text">
                                    <h3>
                                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                            <?php bloginfo('name'); ?>
                                        </a>
                                    </h3>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <p class="footer-description">
                        <?php 
                        $footer_description = get_theme_mod('footer_description', 'Your trusted source for fresh, locally sourced produce. Supporting local farmers and delivering quality to your doorstep.');
                        echo wp_kses_post($footer_description); 
                        ?>
                    </p>
                    
                    <!-- Social Media Links -->
                    <?php if ($show_social): ?>
                        <div class="footer-social-links">
                            <?php aaapos_social_media_icons($social_icon_style); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Column 2: Quick Links -->
                <div class="footer-widget footer-links">
                    <h4 class="widget-title"><?php esc_html_e('Quick Links', 'aaapos-prime'); ?></h4>
                    
                    <?php
                    // Check if footer menu is set
                    if (has_nav_menu('footer')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class' => 'footer-menu',
                            'container' => 'nav',
                            'container_class' => 'footer-navigation',
                            'depth' => 1,
                            'fallback_cb' => 'aaapos_footer_quick_links_fallback',
                        ));
                    } else {
                        aaapos_footer_quick_links_fallback();
                    }
                    ?>
                </div>
                
                <!-- Column 3: Product Categories -->
                <?php if (class_exists('WooCommerce')): ?>
                    <div class="footer-widget footer-categories">
                        <h4 class="widget-title"><?php esc_html_e('Categories', 'aaapos-prime'); ?></h4>
                        
                        <?php
                        // Get the same category order as homepage
                        $selected_categories = get_theme_mod('selected_categories', '');
                        $categories_count = get_theme_mod('categories_count', 6);
                        
                        if (!empty($selected_categories)) {
                            // Use selected categories from homepage in the same order
                            $category_ids = array_map('intval', explode(',', $selected_categories));
                            
                            // Limit to 6 for footer display
                            $category_ids = array_slice($category_ids, 0, 6);
                            
                            // Get category objects maintaining order
                            $categories = array();
                            foreach ($category_ids as $cat_id) {
                                $term = get_term($cat_id, 'product_cat');
                                if ($term && !is_wp_error($term)) {
                                    $categories[] = $term;
                                }
                            }
                        } else {
                            // Fallback: Get top categories by product count
                            $categories = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => true,
                                'number' => 6,
                                'orderby' => 'count',
                                'order' => 'DESC',
                            ));
                        }
                        
                        if (!empty($categories) && !is_wp_error($categories)):
                        ?>
                            <ul class="footer-menu footer-categories-list">
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                            <?php echo esc_html($category->name); ?>
                                            <span class="category-count">(<?php echo esc_html($category->count); ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Column 4: Contact Information -->
                <div class="footer-widget footer-contact-info">
                    <h4 class="widget-title"><?php esc_html_e('Contact Us', 'aaapos-prime'); ?></h4>
                    
                    <ul class="footer-contact">
                        <li class="contact-item contact-address">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span><?php 
                                $address = get_theme_mod('footer_address', '123 Farm Road, Macedon Ranges VIC 3440');
                                echo esc_html($address); 
                            ?></span>
                        </li>
                        
                        <li class="contact-item contact-phone">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <a href="tel:<?php 
                                $phone = get_theme_mod('footer_phone', '03 5427 3552');
                                echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); 
                            ?>">
                                <?php echo esc_html($phone); ?>
                            </a>
                        </li>
                        
                        <li class="contact-item contact-email">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <a href="mailto:<?php 
                                $email = get_theme_mod('footer_email', 'sales@macedonrangesproducestore.com.au');
                                echo esc_attr(antispambot($email)); 
                            ?>">
                                <?php echo esc_html(antispambot($email)); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <!-- Copyright -->
                <div class="footer-copyright">
                    <?php aaapos_copyright_text(); ?>
                </div>
                
                <!-- Footer Bottom Navigation & Payment Icons -->
                <div class="footer-bottom-nav">
                    <!-- Footer Bottom Menu -->
                    <?php if ($show_footer_menu): ?>
                        <nav class="footer-nav-wrapper" aria-label="<?php esc_attr_e('Footer Menu', 'aaapos-prime'); ?>">
                            <?php
                            // Check if utility menu is set
                            if (has_nav_menu('utility')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'utility',
                                    'menu_class' => 'footer-nav',
                                    'container' => false,
                                    'depth' => 1,
                                    'fallback_cb' => 'aaapos_footer_bottom_menu_fallback',
                                ));
                            } else {
                                aaapos_footer_bottom_menu_fallback();
                            }
                            ?>
                        </nav>
                    <?php endif; ?>
                    
                    <!-- Payment Methods Icons -->
                    <?php if ($show_payment_icons): ?>
                        <div class="footer-payment-methods">
                            <span class="payment-methods-label"><?php esc_html_e('We Accept', 'aaapos-prime'); ?></span>
                            <div class="payment-icons">
                                <?php aaapos_payment_method_icons(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <?php if ($show_back_to_top): ?>
        <button class="back-to-top back-to-top-<?php echo esc_attr($back_to_top_position); ?>" 
                aria-label="<?php esc_attr_e('Back to top', 'aaapos-prime'); ?>"
                title="<?php esc_attr_e('Scroll to top', 'aaapos-prime'); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
    <?php endif; ?>
    
</footer>

<?php
/**
 * Helper Functions
 */

/**
 * Display Social Media Icons
 */
function aaapos_social_media_icons($style = 'rounded') {
    $social_platforms = array(
        'facebook' => array('label' => __('Facebook', 'aaapos-prime'), 'icon' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z', 'type' => 'fill'),
        'twitter' => array('label' => __('Twitter/X', 'aaapos-prime'), 'icon' => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z', 'type' => 'fill'),
        'instagram' => array('label' => __('Instagram', 'aaapos-prime'), 'icon' => 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01', 'icon2' => 'M6.5 3h11A3.5 3.5 0 0121 6.5v11a3.5 3.5 0 01-3.5 3.5h-11A3.5 3.5 0 013 17.5v-11A3.5 3.5 0 016.5 3z', 'type' => 'stroke'),
        'youtube' => array('label' => __('YouTube', 'aaapos-prime'), 'icon' => 'M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z M9.75 15.02l.01-6.27 5.77 3.14-5.78 3.13z', 'type' => 'fill'),
        'linkedin' => array('label' => __('LinkedIn', 'aaapos-prime'), 'icon' => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 110-4 2 2 0 010 4z', 'type' => 'fill'),
        'pinterest' => array('label' => __('Pinterest', 'aaapos-prime'), 'icon' => 'M8 2.1c-3.3 0-6 2.7-6 6 0 2.5 1.5 4.7 3.7 5.7.1 0 .2 0 .2-.1 0-.1.1-.4.1-.5 0-.1 0-.2-.1-.3-.4-.5-.7-1.1-.7-1.8 0-2.3 1.7-4.4 4.4-4.4 2.4 0 3.7 1.5 3.7 3.4 0 2.6-1.1 4.7-2.8 4.7-.9 0-1.6-.7-1.4-1.6.2-1.1.7-2.2.7-3 0-.7-.4-1.3-1.1-1.3-1 0-1.7 1-1.7 2.3 0 .8.3 1.4.3 1.4s-.9 3.8-1 4.5c-.3 1.3-.1 2.9 0 3.1 0 .1.1.1.2.1.1 0 1.4-1.8 1.8-3 .1-.4.6-2.3.6-2.3.3.6 1.2 1.1 2.2 1.1 2.9 0 4.9-2.7 4.9-6.2 0-2.7-2.2-5.2-5.6-5.2z', 'type' => 'fill'),
        'tiktok' => array('label' => __('TikTok', 'aaapos-prime'), 'icon' => 'M9 12a4 4 0 104 4V4a5 5 0 005 5', 'type' => 'stroke'),
        'whatsapp' => array('label' => __('WhatsApp', 'aaapos-prime'), 'icon' => 'M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z', 'type' => 'stroke'),
    );
    
    $has_social_links = false;
    foreach ($social_platforms as $platform => $data) {
        if (!empty(get_theme_mod('footer_social_' . $platform))) {
            $has_social_links = true;
            break;
        }
    }
    
    if (!$has_social_links) return;
    
    echo '<div class="social-links social-style-' . esc_attr($style) . '">';
    foreach ($social_platforms as $platform => $data) {
        $url = get_theme_mod('footer_social_' . $platform, '');
        if (!empty($url)) {
            $svg_attrs = ($data['type'] === 'stroke') ? 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"' : 'fill="currentColor" stroke="none"';
            $svg_content = '<path d="' . esc_attr($data['icon']) . '"/>';
            if ($platform === 'instagram' && isset($data['icon2'])) {
                $svg_content .= '<path d="' . esc_attr($data['icon2']) . '"/>';
            }
            printf('<a href="%s" class="social-link social-%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><svg width="20" height="20" viewBox="0 0 24 24" %s>%s</svg></a>',
                esc_url($url), esc_attr($platform), esc_attr($data['label']), $svg_attrs, $svg_content);
        }
    }
    echo '</div>';
}

/**
 * Display Payment Method Icons - Image Based with Show/Hide Control
 */
function aaapos_payment_method_icons() {
    $payment_methods = array(
        'visa' => array(
            'label' => __('Visa', 'aaapos-prime'),
            'image' => 'payment-visa.png'
        ),
        'mastercard' => array(
            'label' => __('Mastercard', 'aaapos-prime'),
            'image' => 'payment-mastercard.png'
        ),
        'amex' => array(
            'label' => __('American Express', 'aaapos-prime'),
            'image' => 'payment-amex.png'
        ),
        'paypal' => array(
            'label' => __('PayPal', 'aaapos-prime'),
            'image' => 'payment-paypal.png'
        ),
        'discover' => array(
            'label' => __('Discover', 'aaapos-prime'),
            'image' => 'payment-discover.png'
        ),
    );
    
    foreach ($payment_methods as $method => $data) {
        // Check if this payment method should be shown
        $show_payment = get_theme_mod('payment_show_' . $method, true);
        
        if (!$show_payment) {
            continue; // Skip this payment method
        }
        
        // Check if user uploaded custom image via customizer
        $custom_image_id = get_theme_mod('payment_icon_' . $method);
        
        if ($custom_image_id) {
            // Use custom uploaded image
            $image_url = wp_get_attachment_image_url($custom_image_id, 'full');
        } else {
            // Use default image from theme
            $image_url = get_template_directory_uri() . '/assets/images/payments/' . $data['image'];
        }
        
        if ($image_url) {
            printf(
                '<div class="payment-icon payment-%s"><img src="%s" alt="%s" title="%s" loading="lazy"></div>',
                esc_attr($method),
                esc_url($image_url),
                esc_attr($data['label']),
                esc_attr($data['label'])
            );
        }
    }
}

/**
 * Display Copyright Text
 */
function aaapos_copyright_text() {
    $copyright = get_theme_mod('footer_copyright_text', sprintf(__('© %s {sitename}. All rights reserved.', 'aaapos-prime'), '{year}'));
    $copyright = str_replace('{year}', date('Y'), $copyright);
    $copyright = str_replace('{sitename}', get_bloginfo('name'), $copyright);
    echo '<p>' . wp_kses_post($copyright) . '</p>';
}

/**
 * Footer Quick Links Fallback
 */
function aaapos_footer_quick_links_fallback() {
    echo '<ul class="footer-menu">';
    if (class_exists('WooCommerce')) echo '<li><a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">' . esc_html__('Shop', 'aaapos-prime') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . esc_html__('About', 'aaapos-prime') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . esc_html__('Contact', 'aaapos-prime') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/blog')) . '">' . esc_html__('Blog', 'aaapos-prime') . '</a></li>';
    echo '</ul>';
}

/**
 * Footer Bottom Menu Fallback
 */
function aaapos_footer_bottom_menu_fallback() {
    echo '<ul class="footer-nav">';
    echo '<li><a href="' . esc_url(home_url('/privacy-policy')) . '">' . esc_html__('Privacy', 'aaapos-prime') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/terms-of-service')) . '">' . esc_html__('Terms', 'aaapos-prime') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/refund-policy')) . '">' . esc_html__('Refunds', 'aaapos-prime') . '</a></li>';
    echo '</ul>';
}
?>