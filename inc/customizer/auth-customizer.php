<?php
/**
 * Authentication Modal Customizer Settings
 *
 * @package Macedon_Ranges
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Authentication Modal Settings
 */
function mr_auth_modal_customizer($wp_customize) {
    
    // Add Authentication Section
    $wp_customize->add_section('mr_auth_modal_section', array(
        'title'       => __('Authentication Modal', 'macedon-ranges'),
        'description' => __('Customize the login and registration modal appearance.', 'macedon-ranges'),
        'priority'    => 140,
    ));

    // Login Image Setting
    $wp_customize->add_setting('auth_modal_login_image', array(
        'default'           => get_template_directory_uri() . '/assets/images/login.png',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'auth_modal_login_image', array(
        'label'       => __('Login Modal Image', 'macedon-ranges'),
        'description' => __('Upload an image for the right side of the login modal. Recommended size: 800x1200px', 'macedon-ranges'),
        'section'     => 'mr_auth_modal_section',
        'settings'    => 'auth_modal_login_image',
        'priority'    => 10,
    )));

    // Enable/Disable Social Login Buttons
    $wp_customize->add_setting('auth_modal_show_social', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('auth_modal_show_social', array(
        'label'       => __('Show Social Login Buttons', 'macedon-ranges'),
        'description' => __('Display Google and Facebook login options in the modal.', 'macedon-ranges'),
        'section'     => 'mr_auth_modal_section',
        'type'        => 'checkbox',
        'priority'    => 30,
    ));

    // Login Modal Welcome Text
    $wp_customize->add_setting('auth_modal_login_subtitle', array(
        'default'           => __('Welcome back! Please enter your details', 'macedon-ranges'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('auth_modal_login_subtitle', array(
        'label'       => __('Login Subtitle', 'macedon-ranges'),
        'description' => __('Subtitle text shown in the login form.', 'macedon-ranges'),
        'section'     => 'mr_auth_modal_section',
        'type'        => 'text',
        'priority'    => 40,
    ));

    // Register Modal Welcome Text
    $wp_customize->add_setting('auth_modal_register_subtitle', array(
        'default'           => __('Create your account to get started', 'macedon-ranges'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('auth_modal_register_subtitle', array(
        'label'       => __('Register Subtitle', 'macedon-ranges'),
        'description' => __('Subtitle text shown in the registration form.', 'macedon-ranges'),
        'section'     => 'mr_auth_modal_section',
        'type'        => 'text',
        'priority'    => 50,
    ));
}
add_action('customize_register', 'mr_auth_modal_customizer');

/**
 * Output custom CSS for modal customization
 */
function mr_auth_modal_custom_css() {
    $primary_color = get_theme_mod('auth_modal_primary_color', get_theme_mod('brand_color', '#6366f1'));
    
    // Convert hex to RGB for use in rgba()
    $rgb = sscanf($primary_color, "#%02x%02x%02x");
    
    ?>
    <style type="text/css">
        :root {
            --auth-primary-color: <?php echo esc_attr($primary_color); ?>;
            --auth-primary-rgb: <?php echo esc_attr(implode(', ', $rgb)); ?>;
        }
        
        .auth-submit-btn {
            background: var(--auth-primary-color) !important;
        }
        
        .auth-submit-btn:hover {
            box-shadow: 0 8px 20px rgba(var(--auth-primary-rgb), 0.3) !important;
        }
        
        .auth-forgot-link,
        .auth-footer-text a {
            color: var(--auth-primary-color) !important;
        }
        
        .auth-form-input:focus {
            border-color: var(--auth-primary-color) !important;
            box-shadow: 0 0 0 4px rgba(var(--auth-primary-rgb), 0.1) !important;
        }
        
        .auth-checkbox {
            accent-color: var(--auth-primary-color) !important;
        }
        
        .auth-social-btn:hover {
            border-color: var(--auth-primary-color) !important;
            background: rgba(var(--auth-primary-rgb), 0.05) !important;
        }
        
        .auth-modal-title::before {
            background: var(--auth-primary-color) !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'mr_auth_modal_custom_css');