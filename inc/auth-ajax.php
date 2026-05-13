<?php
/**
 * Authentication AJAX Handlers
 * Handle login, registration, and password reset via AJAX
 *
 * @package Macedon_Ranges
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validate password strength.
 * Requires: 8+ chars, uppercase, lowercase, number, special character.
 * Returns an error string on failure, or null on success.
 */
if (!function_exists('mr_validate_strong_password')) {
    function mr_validate_strong_password($password) {
        if (strlen($password) < 8) {
            return __('Password must be at least 8 characters long.', 'aaapos');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return __('Password must contain at least one uppercase letter.', 'aaapos');
        }
        if (!preg_match('/[a-z]/', $password)) {
            return __('Password must contain at least one lowercase letter.', 'aaapos');
        }
        if (!preg_match('/[0-9]/', $password)) {
            return __('Password must contain at least one number.', 'aaapos');
        }
        if (!preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>\/?\\\\|`~]/', $password)) {
            return __('Password must contain at least one special character (e.g. !@#$%^&*).', 'aaapos');
        }
        return null;
    }
}

/**
 * Helper function to get auth modal image URL from attachment ID
 */
if (!function_exists('mr_get_auth_modal_image_url')) {
    function mr_get_auth_modal_image_url() {
        $image_id = get_theme_mod('auth_modal_login_image');
        
        if (empty($image_id)) {
            return 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=1200&fit=crop';
        }
        
        $image_url = wp_get_attachment_image_url($image_id, 'full');
        return $image_url ? $image_url : 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=1200&fit=crop';
    }
}

/**
 * Enqueue authentication scripts and styles
 */
function mr_enqueue_auth_scripts() {
    // Load for logged-out users, OR when arriving via a password reset link
    $is_reset_link = isset($_GET['show_reset']) && $_GET['show_reset'] === '1';

    if (!is_user_logged_in() || $is_reset_link) {
        wp_enqueue_style(
            'mr-auth-modal',
            get_template_directory_uri() . '/assets/css/auth-modal.css',
            array(),
            '1.0.4'
        );

        wp_enqueue_script(
            'mr-auth-modal',
            get_template_directory_uri() . '/assets/js/auth-modal.js',
            array(),
            '1.0.4',
            true
        );

        $login_image     = mr_get_auth_modal_image_url();
        $raw_image_value = get_theme_mod('auth_modal_login_image');

        wp_localize_script('mr-auth-modal', 'mr_auth', array(
            'ajax_url'          => admin_url('admin-ajax.php'),
            'nonce'             => wp_create_nonce('mr_auth_nonce'),
            'login_image'       => esc_url($login_image),
            'login_subtitle'    => get_theme_mod('auth_modal_login_subtitle', __('Welcome back! Please enter your details', 'aaapos')),
            'register_subtitle' => get_theme_mod('auth_modal_register_subtitle', __('Create your account to get started', 'aaapos')),
            'has_custom_image'  => (!empty($raw_image_value)) ? 'yes' : 'no',
        ));
    }
}
add_action('wp_enqueue_scripts', 'mr_enqueue_auth_scripts');

/**
 * AJAX Login Handler
 */
function mr_ajax_login() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberme']) && $_POST['rememberme'] === 'forever';

    if (empty($username) || empty($password)) {
        wp_send_json_error(array(
            'message' => __('Please fill in all required fields.', 'aaapos')
        ));
    }

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

    wp_send_json_success(array(
        'message'  => __('Login successful! Redirecting...', 'aaapos'),
        'redirect' => get_permalink(get_option('woocommerce_myaccount_page_id'))
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_login', 'mr_ajax_login');

/**
 * AJAX Registration Handler
 */
function mr_ajax_register() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    if (!get_option('users_can_register')) {
        wp_send_json_error(array(
            'message' => __('User registration is currently disabled.', 'aaapos')
        ));
    }

    $username = sanitize_user($_POST['username']);
    $email    = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        wp_send_json_error(array(
            'message' => __('Please fill in all required fields.', 'aaapos')
        ));
    }

    if (!validate_username($username)) {
        wp_send_json_error(array(
            'message' => __('Invalid username. Only lowercase letters, numbers, and underscores are allowed.', 'aaapos')
        ));
    }

    if (username_exists($username)) {
        wp_send_json_error(array(
            'message' => __('This username is already taken. Please choose another one.', 'aaapos')
        ));
    }

    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'aaapos')
        ));
    }

    if (email_exists($email)) {
        wp_send_json_error(array(
            'message' => __('This email is already registered. Please use another email or login.', 'aaapos')
        ));
    }

    $password_error = mr_validate_strong_password($password);
    if ($password_error) {
        wp_send_json_error(array('message' => $password_error));
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array(
            'message' => $user_id->get_error_message()
        ));
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true, is_ssl());
    wp_new_user_notification($user_id, null, 'user');

    wp_send_json_success(array(
        'message'  => __('Registration successful! Welcome aboard!', 'aaapos'),
        'redirect' => get_permalink(get_option('woocommerce_myaccount_page_id'))
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_register', 'mr_ajax_register');

/**
 * AJAX Forgot Password Handler
 * Validates that the email/username exists before sending a reset link.
 */
function mr_ajax_forgot_password() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    $email_or_username = sanitize_text_field($_POST['email_or_username'] ?? '');

    if (empty($email_or_username)) {
        wp_send_json_error(array(
            'message' => __('Please enter your email address or username.', 'aaapos')
        ));
    }

    // Validate email format if the input looks like an email
    if (strpos($email_or_username, '@') !== false && !is_email($email_or_username)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'aaapos')
        ));
    }

    // Try to find the user by email first, then by login
    $user = is_email($email_or_username)
        ? get_user_by('email', $email_or_username)
        : get_user_by('login', $email_or_username);

    // No account found — tell the user clearly
    if (!$user) {
        wp_send_json_error(array(
            'message' => __('No account found with that email or username. Please check and try again.', 'aaapos')
        ));
    }

    // Generate password reset key
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error(array(
            'message' => __('Unable to generate reset link. Please try again later.', 'aaapos')
        ));
    }

    // Build reset URL — use WooCommerce my-account URL if available, otherwise wp-login
    $reset_url         = '';
    $myaccount_page_id = get_option('woocommerce_myaccount_page_id');

    if ($myaccount_page_id && class_exists('WooCommerce')) {
        $reset_url = add_query_arg(
            array(
                'key'   => $reset_key,
                'login' => rawurlencode($user->user_login),
            ),
            wc_get_endpoint_url('lost-password', '', get_permalink($myaccount_page_id))
        );
    } else {
        $reset_url = network_site_url(
            "wp-login.php?action=rp&key={$reset_key}&login=" . rawurlencode($user->user_login),
            'login'
        );
    }

    // Build custom email
    $site_name = get_bloginfo('name');
    $user_name = $user->display_name ?: $user->user_login;
    $to        = $user->user_email;
    $subject   = sprintf(__('[%s] Password Reset Request', 'aaapos'), $site_name);

    $message  = sprintf(__('Hi %s,', 'aaapos'), $user_name) . "\n\n";
    $message .= __('Someone requested a password reset for your account. If this was you, click the link below to reset your password:', 'aaapos') . "\n\n";
    $message .= $reset_url . "\n\n";
    $message .= __('This link will expire in 24 hours.', 'aaapos') . "\n\n";
    $message .= __("If you didn't request this, you can safely ignore this email. Your password won't change.", 'aaapos') . "\n\n";
    $message .= sprintf(__('— The %s Team', 'aaapos'), $site_name);

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    $sent = wp_mail($to, $subject, $message, $headers);

    if (!$sent) {
        wp_send_json_error(array(
            'message' => __('Failed to send email. Please try again or contact support.', 'aaapos')
        ));
    }

    wp_send_json_success(array(
        'message' => __('Password reset email sent! Please check your inbox.', 'aaapos')
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_forgot_password', 'mr_ajax_forgot_password');
add_action('wp_ajax_mr_ajax_forgot_password', 'mr_ajax_forgot_password');

/**
 * AJAX Reset Password Handler
 * Validates the reset key and sets the new password
 */
function mr_ajax_reset_password() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mr_auth_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'aaapos')
        ));
    }

    $login     = sanitize_text_field($_POST['login']     ?? '');
    $key       = sanitize_text_field($_POST['key']       ?? '');
    $password  = $_POST['password']                       ?? '';
    $password2 = $_POST['password2']                      ?? '';

    if (empty($login) || empty($key)) {
        wp_send_json_error(array(
            'message' => __('Invalid reset link. Please request a new one.', 'aaapos')
        ));
    }

    if (empty($password) || empty($password2)) {
        wp_send_json_error(array(
            'message' => __('Please fill in both password fields.', 'aaapos')
        ));
    }

    if ($password !== $password2) {
        wp_send_json_error(array(
            'message' => __('Passwords do not match. Please try again.', 'aaapos')
        ));
    }

    $password_error = mr_validate_strong_password($password);
    if ($password_error) {
        wp_send_json_error(array('message' => $password_error));
    }

    // Validate the reset key
    $user = check_password_reset_key($key, $login);

    if (is_wp_error($user)) {
        wp_send_json_error(array(
            'message' => __('This reset link has expired or is invalid. Please request a new one.', 'aaapos')
        ));
    }

    // Reset the password
    reset_password($user, $password);

    wp_send_json_success(array(
        'message' => __('Password reset successfully! You can now log in with your new password.', 'aaapos')
    ));
}
add_action('wp_ajax_nopriv_mr_ajax_reset_password', 'mr_ajax_reset_password');
add_action('wp_ajax_mr_ajax_reset_password', 'mr_ajax_reset_password');

/**
 * Add body class when user is logged in
 */
function mr_add_logged_in_body_class($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
    }
    return $classes;
}
add_filter('body_class', 'mr_add_logged_in_body_class');

/**
 * Intercept the WooCommerce lost-password endpoint (the reset link from email)
 * and redirect to homepage with the key/login as URL params so our modal can handle it.
 *
 * Only intercepts the "rp" (reset password) step — not the "lostpassword" request form step.
 */
function mr_intercept_reset_password_page() {
    if (!is_wc_endpoint_url('lost-password')) {
        return;
    }

    // WooCommerce puts key & login in $_GET when the user arrives via the email link
    $key   = isset($_GET['key'])   ? sanitize_text_field($_GET['key'])   : '';
    $login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

    // Only intercept when both are present (i.e. coming from reset email link)
    if (empty($key) || empty($login)) {
        return;
    }

    // Redirect to homepage and open modal in reset-password view
    $redirect = add_query_arg(array(
        'show_reset' => '1',
        'key'        => $key,
        'login'      => rawurlencode($login),
    ), home_url('/'));

    wp_redirect($redirect);
    exit;
}
add_action('template_redirect', 'mr_intercept_reset_password_page', 5);

/**
 * Redirect My Account page to homepage with modal trigger when user is logged out.
 * Does NOT intercept the lost-password endpoint (handled above separately).
 */
function mr_redirect_myaccount_to_modal() {
    if (!is_user_logged_in() && is_account_page() && !is_wc_endpoint_url('lost-password')) {
        wp_redirect(home_url('/?show_login=1'));
        exit;
    }
}
add_action('template_redirect', 'mr_redirect_myaccount_to_modal');