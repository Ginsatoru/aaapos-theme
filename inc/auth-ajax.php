<?php
/**
 * Authentication AJAX Handlers
 * Handle login and registration via AJAX
 * auth-ajax.php
 * @package Macedon_Ranges
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue authentication scripts and styles
 */
function mr_enqueue_auth_scripts() {
    // Only enqueue if user is not logged in
    if (!is_user_logged_in()) {
        wp_enqueue_style(
            'mr-auth-modal',
            get_template_directory_uri() . '/assets/css/auth-modal.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'mr-auth-modal',
            get_template_directory_uri() . '/assets/js/auth-modal.js',
            array(),
            '1.0.0',
            true
        );

        // Get login image from customizer
        $customizer_image = get_theme_mod('auth_modal_login_image');
        
        // Fallback to default image if customizer image is not set or empty
        $fallback_image = get_template_directory_uri() . '/assets/images/login.png';
        $login_image = (!empty($customizer_image)) ? $customizer_image : $fallback_image;

        // Localize script with AJAX URL and nonce
        wp_localize_script('mr-auth-modal', 'mr_auth', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mr_auth_nonce'),
            'lost_password_url' => wp_lostpassword_url(),
            'login_image' => esc_url($login_image), // Uses customizer image OR fallback
            'login_subtitle' => get_theme_mod('auth_modal_login_subtitle', __('Welcome back! Please enter your details', 'aaapos')),
            'register_subtitle' => get_theme_mod('auth_modal_register_subtitle', __('Create your account to get started', 'aaapos')),
        ));
    }
}
add_action('wp_enqueue_scripts', 'mr_enqueue_auth_scripts');

/**
 * AJAX Login Handler
 */
function mr_ajax_login() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    // Sanitize input
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberme']) && $_POST['rememberme'] === 'forever';

    // Validate
    if (empty($username) || empty($password)) {
        wp_send_json_error(array(
            'message' => __('Please fill in all required fields.', 'aaapos')
        ));
    }

    // Attempt login
    $credentials = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    );

    $user = wp_signon($credentials, is_ssl());

    if (is_wp_error($user)) {
        wp_send_json_error(array(
            'message' => __('Invalid username or password. Please try again.', 'aaapos')
        ));
    }

    // Success
    wp_send_json_success(array(
        'message' => __('Login successful! Redirecting...', 'aaapos'),
        'redirect' => get_permalink(get_option('woocommerce_myaccount_page_id'))
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_login', 'mr_ajax_login');

/**
 * AJAX Registration Handler
 */
function mr_ajax_register() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    // Check if registration is enabled
    if (!get_option('users_can_register')) {
        wp_send_json_error(array(
            'message' => __('User registration is currently disabled.', 'aaapos')
        ));
    }

    // Sanitize input
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        wp_send_json_error(array(
            'message' => __('Please fill in all required fields.', 'aaapos')
        ));
    }

    // Validate username
    if (!validate_username($username)) {
        wp_send_json_error(array(
            'message' => __('Invalid username. Only lowercase letters, numbers, and underscores are allowed.', 'aaapos')
        ));
    }

    // Check if username exists
    if (username_exists($username)) {
        wp_send_json_error(array(
            'message' => __('This username is already taken. Please choose another one.', 'aaapos')
        ));
    }

    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'aaapos')
        ));
    }

    // Check if email exists
    if (email_exists($email)) {
        wp_send_json_error(array(
            'message' => __('This email is already registered. Please use another email or login.', 'aaapos')
        ));
    }

    // Validate password strength (minimum 6 characters)
    if (strlen($password) < 6) {
        wp_send_json_error(array(
            'message' => __('Password must be at least 6 characters long.', 'aaapos')
        ));
    }

    // Create user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array(
            'message' => $user_id->get_error_message()
        ));
    }

    // Auto-login after registration
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true, is_ssl());

    // Send new user notification (optional)
    wp_new_user_notification($user_id, null, 'user');

    // Success
    wp_send_json_success(array(
        'message' => __('Registration successful! Welcome aboard!', 'aaapos'),
        'redirect' => get_permalink(get_option('woocommerce_myaccount_page_id'))
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_register', 'mr_ajax_register');

/**
 * Add body class when user is logged in
 * This helps the JavaScript know whether to show the modal
 */
function mr_add_logged_in_body_class($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
    }
    return $classes;
}
add_filter('body_class', 'mr_add_logged_in_body_class');

/**
 * Redirect My Account page to homepage with modal trigger when user is logged out
 */
function mr_redirect_myaccount_to_modal() {
    // Only redirect if user is not logged in and on my-account page
    if (!is_user_logged_in() && is_account_page()) {
        wp_redirect(home_url('/?show_login=1'));
        exit;
    }
}
add_action('template_redirect', 'mr_redirect_myaccount_to_modal');