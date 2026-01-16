<?php
/**
 * Header Dropdown Customizer Settings
 * Add to inc/customizer/ folder and include in functions.php
 * 
 * @package Macedon_Ranges
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Header Dropdown customizer settings
 */
function mr_header_dropdown_customizer($wp_customize) {
    
    // Add Header Dropdowns Section
    $wp_customize->add_section('mr_header_dropdowns', array(
        'title'    => __('Header Dropdowns', 'macedon-ranges'),
        'priority' => 35,
        'panel'    => 'mr_header_settings', // If you have a header panel, otherwise remove this line
    ));
    
    // ===============================================
    // CART DROPDOWN SETTINGS
    // ===============================================
    
    // Show Cart Dropdown
    $wp_customize->add_setting('show_cart_dropdown', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_cart_dropdown', array(
        'label'       => __('Show Cart Dropdown on Hover', 'macedon-ranges'),
        'description' => __('Display cart items when hovering over the cart icon', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'checkbox',
    ));
    
    // Cart Dropdown Max Items
    $wp_customize->add_setting('cart_dropdown_max_items', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_max_items', array(
        'label'       => __('Maximum Cart Items in Dropdown', 'macedon-ranges'),
        'description' => __('Maximum number of items to show before scrolling', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 10,
            'step' => 1,
        ),
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // Show Product Thumbnails
    $wp_customize->add_setting('cart_dropdown_show_thumbnails', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_show_thumbnails', array(
        'label'   => __('Show Product Thumbnails', 'macedon-ranges'),
        'section' => 'mr_header_dropdowns',
        'type'    => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // Show Product Prices
    $wp_customize->add_setting('cart_dropdown_show_prices', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_show_prices', array(
        'label'   => __('Show Product Prices', 'macedon-ranges'),
        'section' => 'mr_header_dropdowns',
        'type'    => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // Show Quantity
    $wp_customize->add_setting('cart_dropdown_show_quantity', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_show_quantity', array(
        'label'   => __('Show Product Quantity', 'macedon-ranges'),
        'section' => 'mr_header_dropdowns',
        'type'    => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // Show Remove Button
    $wp_customize->add_setting('cart_dropdown_show_remove', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_show_remove', array(
        'label'       => __('Show Remove Item Button', 'macedon-ranges'),
        'description' => __('Allow removing items directly from dropdown', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // Show Cart Buttons
    $wp_customize->add_setting('cart_dropdown_show_buttons', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('cart_dropdown_show_buttons', array(
        'label'       => __('Show View Cart & Checkout Buttons', 'macedon-ranges'),
        'description' => __('Display action buttons at the bottom of dropdown', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_cart_dropdown', true);
        },
    ));
    
    // ===============================================
    // ACCOUNT DROPDOWN SETTINGS
    // ===============================================
    
    // Show Account Dropdown
    $wp_customize->add_setting('show_account_dropdown', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_account_dropdown', array(
        'label'       => __('Show Account Dropdown on Hover', 'macedon-ranges'),
        'description' => __('Display account menu when hovering over account icon (logged in users only)', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'checkbox',
    ));
    
    // Show Username Next to Icon
    $wp_customize->add_setting('show_username_in_header', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_username_in_header', array(
        'label'       => __('Show Username Next to Icon', 'macedon-ranges'),
        'description' => __('Display username next to account icon for logged in users (desktop only)', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'checkbox',
    ));
    
    // Username Display Format
    $wp_customize->add_setting('username_display_format', array(
        'default'           => 'first_name',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('username_display_format', array(
        'label'   => __('Username Display Format', 'macedon-ranges'),
        'section' => 'mr_header_dropdowns',
        'type'    => 'select',
        'choices' => array(
            'first_name'   => __('First Name', 'macedon-ranges'),
            'display_name' => __('Display Name', 'macedon-ranges'),
            'username'     => __('Username', 'macedon-ranges'),
        ),
        'active_callback' => function() {
            return get_theme_mod('show_username_in_header', true);
        },
    ));
    
    // Show Email in Dropdown Header
    $wp_customize->add_setting('show_email_in_dropdown', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('show_email_in_dropdown', array(
        'label'   => __('Show Email in Dropdown Header', 'macedon-ranges'),
        'section' => 'mr_header_dropdowns',
        'type'    => 'checkbox',
        'active_callback' => function() {
            return get_theme_mod('show_account_dropdown', true);
        },
    ));
    
    // ===============================================
    // DROPDOWN ANIMATION SETTINGS
    // ===============================================
    
    // Dropdown Animation Speed
    $wp_customize->add_setting('dropdown_animation_speed', array(
        'default'           => '300',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('dropdown_animation_speed', array(
        'label'       => __('Dropdown Animation Speed (ms)', 'macedon-ranges'),
        'description' => __('Speed of dropdown show/hide animation in milliseconds', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 100,
            'max'  => 1000,
            'step' => 50,
        ),
    ));
    
    // Dropdown Hover Delay
    $wp_customize->add_setting('dropdown_hover_delay', array(
        'default'           => '200',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('dropdown_hover_delay', array(
        'label'       => __('Dropdown Hover Delay (ms)', 'macedon-ranges'),
        'description' => __('Delay before dropdown appears on hover', 'macedon-ranges'),
        'section'     => 'mr_header_dropdowns',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 1000,
            'step' => 50,
        ),
    ));
}
add_action('customize_register', 'mr_header_dropdown_customizer');

/**
 * Add customizer CSS for dropdown settings
 */
function mr_header_dropdown_customizer_css() {
    $animation_speed = get_theme_mod('dropdown_animation_speed', 300);
    ?>
    <style type="text/css">
        .cart-dropdown,
        .account-dropdown {
            transition: all <?php echo esc_attr($animation_speed); ?>ms ease !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'mr_header_dropdown_customizer_css');