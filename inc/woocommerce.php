<?php
/**
 * WooCommerce Integration - COMPLETE WITH SINGLE PRODUCT PAGE
 * 
 * Fully updated with:
 * - Shop grid layout (71% width)
 * - Single product page (71% width, modern design)
 * - Variable product support
 * - Enhanced quantity selectors
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add WooCommerce Support
 */
function aaapos_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'aaapos_woocommerce_setup');

/**
 * Enqueue WooCommerce Styles - COMPLETE SYSTEM
 */
function aaapos_woocommerce_nuclear_styles() {
    // Only on WooCommerce pages
    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        return;
    }
    
    // Main WooCommerce styles (base styles, buttons, forms, etc.)
    wp_enqueue_style(
        'aaapos-woocommerce-base', 
        get_template_directory_uri() . '/assets/css/woocommerce.css', 
        array(), 
        AAAPOS_VERSION . '.' . time(),
        'all'
    );
    
    // Shop grid layout - for shop/archive pages only
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_style(
            'aaapos-shop-grid', 
            get_template_directory_uri() . '/assets/css/shop-grid.css', 
            array('aaapos-woocommerce-base'), 
            AAAPOS_VERSION . '.' . time(),
            'all'
        );
    }
    
    // My Account page styles
    if (is_account_page()) {
        wp_enqueue_style(
            'aaapos-woocommerce-myaccount', 
            get_template_directory_uri() . '/assets/css/woocommerce-myaccount.css', 
            array('aaapos-woocommerce-base'), 
            AAAPOS_VERSION . '.' . time()
        );
    }
    
    // Cart notifications CSS (toast notifications)
    wp_enqueue_style(
        'aaapos-cart-notifications',
        get_template_directory_uri() . '/assets/css/cart-notifications.css',
        array('aaapos-woocommerce-base'),
        AAAPOS_VERSION,
        'all'
    );
    
    // WooCommerce cart functionality
    wp_enqueue_script(
        'aaapos-woocommerce-js',
        get_template_directory_uri() . '/assets/js/woocommerce.js',
        array('jquery', 'wc-add-to-cart'),
        AAAPOS_VERSION,
        true
    );
    
    // Cart notifications JS (handles toast notifications)
    wp_enqueue_script(
        'aaapos-cart-notifications-js',
        get_template_directory_uri() . '/assets/js/cart-notifications.js',
        array('jquery', 'aaapos-woocommerce-js'),
        AAAPOS_VERSION,
        true
    );
    
    // Quantity selector enhancement (for single product page)
    if (is_product()) {
        wp_enqueue_script(
            'aaapos-quantity-selector',
            get_template_directory_uri() . '/assets/js/quantity-selector.js',
            array('jquery'),
            AAAPOS_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'aaapos_woocommerce_nuclear_styles', 999);

/**
 * Add Critical Inline CSS - Loads IMMEDIATELY in HEAD
 */
function aaapos_woocommerce_inline_critical_css() {
    if (!is_woocommerce() && !is_cart() && !is_checkout()) {
        return;
    }
    ?>
    <style id="woocommerce-critical-fix">
        /* CRITICAL IMMEDIATE FIXES - Applied instantly */
        .woocommerce ul.products,
        .woocommerce-page ul.products,
        ul.products,
        ul.products.columns-3 {
            display: grid !important;
            grid-template-columns: repeat(1, 1fr) !important;
            gap: 1.5rem !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        @media (min-width: 640px) {
            .woocommerce ul.products,
            .woocommerce-page ul.products,
            ul.products,
            ul.products.columns-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (min-width: 1024px) {
            .woocommerce ul.products,
            .woocommerce-page ul.products,
            ul.products,
            ul.products.columns-3 {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
        
        .woocommerce ul.products li.product,
        .woocommerce-page ul.products li.product,
        ul.products li.product {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            float: none !important;
            clear: none !important;
        }
        
        /* Single Product - Critical fix for quantity */
        .single-product .quantity {
            display: inline-flex !important;
            align-items: center !important;
        }
        
        .single-product .quantity input.qty {
            width: 70px !important;
            text-align: center !important;
            border: none !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'aaapos_woocommerce_inline_critical_css', 999);

/**
 * Disable WooCommerce's Default Conflicting Styles
 */
add_filter('woocommerce_enqueue_styles', function($styles) {
    // Remove default WooCommerce general styles that add grid conflicts
    if (isset($styles['woocommerce-general'])) {
        unset($styles['woocommerce-general']);
    }
    return $styles;
});

/**
 * Force Remove WooCommerce's Inline Grid CSS
 */
function aaapos_remove_woo_inline_css() {
    if (is_woocommerce()) {
        wp_add_inline_style('woocommerce-inline', '
            .woocommerce ul.products[class*="columns-"] li.product {
                width: auto !important;
                float: none !important;
                margin-right: 0 !important;
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'aaapos_remove_woo_inline_css', 9999);

/**
 * Remove Default WooCommerce Wrappers
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

/**
 * Remove Default Sidebar
 */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/**
 * Products Per Page
 */
function aaapos_products_per_page() {
    return get_theme_mod('products_per_page', 12);
}
add_filter('loop_shop_per_page', 'aaapos_products_per_page', 20);

/**
 * Products Per Row - Force 3 Columns on Desktop
 */
function aaapos_products_per_row() {
    return 3;
}
add_filter('loop_shop_columns', 'aaapos_products_per_row');

/**
 * Related Products Configuration
 */
function aaapos_related_products_args($args) {
    $args['posts_per_page'] = get_theme_mod('related_products_count', 4);
    $args['columns'] = 4;
    return $args;
}
add_filter('woocommerce_output_related_products_args', 'aaapos_related_products_args');

/**
 * Custom Image Sizes for WooCommerce
 */
function aaapos_woocommerce_image_sizes() {
    add_image_size('woocommerce_thumbnail', 400, 400, true);
    add_image_size('woocommerce_single', 800, 800, true);
    add_image_size('woocommerce_gallery_thumbnail', 150, 150, true);
}
add_action('after_setup_theme', 'aaapos_woocommerce_image_sizes', 11);

/**
 * Set WooCommerce Default Image Dimensions
 */
function aaapos_woocommerce_theme_image_dimensions() {
    $catalog = array(
        'width'  => '400',
        'height' => '400',
        'crop'   => 1,
    );
    
    $single = array(
        'width'  => '800',
        'height' => '800',
        'crop'   => 1,
    );
    
    $thumbnail = array(
        'width'  => '150',
        'height' => '150',
        'crop'   => 1,
    );
    
    update_option('shop_catalog_image_size', $catalog);
    update_option('shop_single_image_size', $single);
    update_option('shop_thumbnail_image_size', $thumbnail);
}
add_action('after_switch_theme', 'aaapos_woocommerce_theme_image_dimensions');

/**
 * Modify Product Gallery Classes
 */
function aaapos_product_gallery_classes($classes) {
    $classes[] = 'woocommerce-product-gallery--custom';
    return $classes;
}
add_filter('woocommerce_single_product_image_gallery_classes', 'aaapos_product_gallery_classes');

/**
 * Add Body Classes for WooCommerce Pages
 */
function aaapos_woo_body_classes($classes) {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = 'aaapos-shop-page';
        $classes[] = 'woocommerce-shop';
    }
    
    if (is_product()) {
        $classes[] = 'single-product';
        $classes[] = 'woocommerce-product-page';
    }
    
    if (is_account_page()) {
        $classes[] = 'woocommerce-account';
    }
    
    return $classes;
}
add_filter('body_class', 'aaapos_woo_body_classes');

/**
 * Customize My Account Menu Order
 */
function aaapos_custom_my_account_menu_order() {
    return array(
        'dashboard'       => __('Dashboard', 'woocommerce'),
        'orders'          => __('Orders', 'woocommerce'),
        'downloads'       => __('Downloads', 'woocommerce'),
        'edit-address'    => __('Addresses', 'woocommerce'),
        'payment-methods' => __('Payment methods', 'woocommerce'),
        'edit-account'    => __('Account details', 'woocommerce'),
        'customer-logout' => __('Logout', 'woocommerce'),
    );
}
add_filter('woocommerce_account_menu_items', 'aaapos_custom_my_account_menu_order');

/**
 * Custom Dashboard Content with Grid Cards
 */
function aaapos_custom_dashboard_content() {
    $current_user = wp_get_current_user();
    $display_name = !empty($current_user->first_name) 
        ? $current_user->first_name 
        : $current_user->display_name;
    
    ?>
    <div class="woocommerce-MyAccount-dashboard-intro">
        <h2 class="dashboard-greeting">
            <?php if (file_exists(get_template_directory() . '/assets/images/icons/profile-icon.png')) : ?>
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/icons/profile-icon.png'); ?>" 
                     alt="Profile" class="greeting-icon">
            <?php endif; ?>
            Hello <span class="greeting-name"><?php echo esc_html($display_name); ?></span>
        </h2>
    </div>

    <div class="woocommerce-MyAccount-dashboard-grid">
        
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e('Orders', 'aaapos-prime'); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e('View your order history', 'aaapos-prime'); ?></p>
        </a>

        <a href="<?php echo esc_url(wc_get_account_endpoint_url('downloads')); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e('Downloads', 'aaapos-prime'); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e('Access your downloads', 'aaapos-prime'); ?></p>
        </a>

        <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e('Addresses', 'aaapos-prime'); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e('Manage billing & shipping', 'aaapos-prime'); ?></p>
        </a>

        <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e('Account Details', 'aaapos-prime'); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e('Update your information', 'aaapos-prime'); ?></p>
        </a>

        <a href="<?php echo esc_url(wc_get_account_endpoint_url('payment-methods')); ?>" class="dashboard-card">
            <div class="dashboard-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h3 class="dashboard-card__title"><?php esc_html_e('Payment Methods', 'aaapos-prime'); ?></h3>
            <p class="dashboard-card__description"><?php esc_html_e('Manage saved payment cards', 'aaapos-prime'); ?></p>
        </a>

        <?php if (get_page_by_path('contact')) : ?>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="dashboard-card">
                <div class="dashboard-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="dashboard-card__title"><?php esc_html_e('Support', 'aaapos-prime'); ?></h3>
                <p class="dashboard-card__description"><?php esc_html_e('Get help & contact us', 'aaapos-prime'); ?></p>
            </a>
        <?php endif; ?>

    </div>
    <?php
}
remove_action('woocommerce_account_dashboard', 'woocommerce_account_dashboard', 10);
add_action('woocommerce_account_dashboard', 'aaapos_custom_dashboard_content', 10);

/**
 * Register Shop Sidebar Widget Area
 */
function aaapos_register_shop_sidebar() {
    register_sidebar(array(
        'name'          => __('Shop Sidebar', 'aaapos-prime'),
        'id'            => 'shop-sidebar',
        'description'   => __('Widgets in this area will be shown on the shop pages.', 'aaapos-prime'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'aaapos_register_shop_sidebar');

/**
 * Add Data Attributes to Order Table for Responsive Design
 */
function aaapos_add_data_title_to_order_table() {
    if (!is_account_page()) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.woocommerce-orders-table thead th').each(function(index) {
                var title = $(this).text();
                $('.woocommerce-orders-table tbody tr').each(function() {
                    $(this).find('td').eq(index).attr('data-title', title);
                });
            });
        });
    </script>
    <?php
}
add_action('woocommerce_account_orders_endpoint', 'aaapos_add_data_title_to_order_table');