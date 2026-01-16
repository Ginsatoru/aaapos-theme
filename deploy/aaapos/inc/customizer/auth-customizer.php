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
        'title'       => __('Authentication Modal', 'aaapos'),
        'description' => __('Customize the login and registration modal appearance.', 'aaapos'),
        'priority'    => 140,
    ));

    // Login Image Setting - Stores attachment ID
    $wp_customize->add_setting('auth_modal_login_image', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'auth_modal_login_image', array(
        'label'       => __('Login Modal Image', 'aaapos'),
        'description' => __('Upload an image for the right side of the login modal. Recommended size: 800x1200px. Leave empty for gradient background.', 'aaapos'),
        'section'     => 'mr_auth_modal_section',
        'mime_type'   => 'image',
        'button_labels' => array(
            'select'       => __('Select Image', 'aaapos'),
            'change'       => __('Change Image', 'aaapos'),
            'remove'       => __('Remove', 'aaapos'),
            'placeholder'  => __('No image selected', 'aaapos'),
            'frame_title'  => __('Select Image', 'aaapos'),
            'frame_button' => __('Choose Image', 'aaapos'),
        ),
    )));

    // Login Modal Welcome Text
    $wp_customize->add_setting('auth_modal_login_subtitle', array(
        'default'           => __('Welcome back! Please enter your details', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('auth_modal_login_subtitle', array(
        'label'       => __('Login Subtitle', 'aaapos'),
        'description' => __('Subtitle text shown in the login form.', 'aaapos'),
        'section'     => 'mr_auth_modal_section',
        'type'        => 'text',
        'priority'    => 40,
    ));

    // Register Modal Welcome Text
    $wp_customize->add_setting('auth_modal_register_subtitle', array(
        'default'           => __('Create your account to get started', 'aaapos'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('auth_modal_register_subtitle', array(
        'label'       => __('Register Subtitle', 'aaapos'),
        'description' => __('Subtitle text shown in the registration form.', 'aaapos'),
        'section'     => 'mr_auth_modal_section',
        'type'        => 'text',
        'priority'    => 50,
    ));
}
add_action('customize_register', 'mr_auth_modal_customizer');

/**
 * Get auth modal login image URL
 * 
 * @return string|false Image URL or false if not set
 */
function mr_get_auth_modal_image() {
    $image_id = get_theme_mod('auth_modal_login_image', '');
    
    if (empty($image_id)) {
        return false;
    }
    
    $image_url = wp_get_attachment_image_url($image_id, 'full');
    return $image_url ? $image_url : false;
}

/**
 * Output custom CSS for modal customization
 */
function mr_auth_modal_custom_css() {
    $primary_color = get_theme_mod('auth_modal_primary_color', get_theme_mod('brand_color', '#6366f1'));
    
    // Convert hex to RGB for use in rgba()
    $rgb = sscanf($primary_color, "#%02x%02x%02x");
    
    if ($rgb && count($rgb) === 3) {
        ?>
        <style type="text/css">
            :root {
                --auth-primary-color: <?php echo esc_attr($primary_color); ?>;
                --auth-primary-rgb: <?php echo esc_attr(implode(', ', $rgb)); ?>;
            }
            
            .auth-submit-btn {
                background: var(--auth-primary-color) !important;
            }
            
            .auth-forgot-link,
            .auth-footer-text a {
                color: var(--auth-primary-color) !important;
            }
            
            .auth-form-input:focus {
                border-color: var(--auth-primary-color) !important;
            }
            
            .auth-checkbox {
                accent-color: var(--auth-primary-color) !important;
            }
            
            .auth-modal-title::before {
                background: var(--auth-primary-color) !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'mr_auth_modal_custom_css');

/**
 * Customizer live preview for subtitles
 */
function mr_auth_modal_customizer_preview() {
    if (is_customize_preview()) {
        ?>
        <script type="text/javascript">
        (function($) {
            'use strict';
            
            // Live preview for login subtitle
            wp.customize('auth_modal_login_subtitle', function(value) {
                value.bind(function(newval) {
                    const loginSubtitle = document.querySelector('.auth-tab-content[data-content="login"] .auth-modal-subtitle');
                    if (loginSubtitle) {
                        loginSubtitle.textContent = newval;
                        loginSubtitle.setAttribute('data-login-text', newval);
                    }
                });
            });
            
            // Live preview for register subtitle
            wp.customize('auth_modal_register_subtitle', function(value) {
                value.bind(function(newval) {
                    const registerSubtitle = document.querySelector('.auth-tab-content[data-content="register"] .auth-modal-subtitle');
                    if (registerSubtitle) {
                        registerSubtitle.textContent = newval;
                        registerSubtitle.setAttribute('data-register-text', newval);
                    }
                });
            });
            
            // Live preview for login image
            wp.customize('auth_modal_login_image', function(value) {
                value.bind(function(attachment) {
                    const rightPanel = document.querySelector('.auth-modal-right');
                    if (rightPanel) {
                        if (attachment) {
                            const imageUrl = typeof attachment === 'object' ? attachment.url : attachment;
                            rightPanel.style.backgroundImage = `url('${imageUrl}')`;
                            rightPanel.classList.remove('no-image');
                        } else {
                            rightPanel.style.backgroundImage = 'none';
                            rightPanel.classList.add('no-image');
                        }
                    }
                });
            });
            
        })(jQuery);
        </script>
        <?php
    }
}
add_action('customize_preview_init', 'mr_auth_modal_customizer_preview');