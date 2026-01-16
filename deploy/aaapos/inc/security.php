<?php
/**
 * Security Enhancements
 *
 * Implements WordPress security best practices:
 * - Header security
 * - Login protection
 * - File upload sanitization
 * - REST API restrictions
 * - Version hiding
 * - XSS/CSRF protection
 *
 * @package Macedon_Ranges
 * @since 1.0.0
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

/**
 * Security headers for enhanced protection
 */
function mr_security_headers($headers)
{
    // Prevent MIME type sniffing
    $headers["X-Content-Type-Options"] = "nosniff";

    // Prevent clickjacking
    $headers["X-Frame-Options"] = "SAMEORIGIN";

    // XSS protection (legacy, but good for older browsers)
    $headers["X-XSS-Protection"] = "1; mode=block";

    // Referrer policy
    $headers["Referrer-Policy"] = "strict-origin-when-cross-origin";

    // Permissions policy (formerly Feature-Policy)
    $headers["Permissions-Policy"] = "geolocation=(), microphone=(), camera=()";

    // Content Security Policy (basic - adjust based on your needs)
    // Uncomment and customize if needed
    // $headers['Content-Security-Policy'] = "default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;";

    return $headers;
}
add_filter("wp_headers", "mr_security_headers");

/**
 * Remove WordPress version from various locations
 */
function mr_remove_version()
{
    return "";
}
add_filter("the_generator", "mr_remove_version");

// Remove version from RSS feeds
add_filter("the_generator", "__return_empty_string");

/**
 * Remove version from scripts and styles
 */
function mr_remove_version_from_assets($src)
{
    // Only remove version from theme assets, not plugins
    if (strpos($src, MR_THEME_URI) !== false && strpos($src, "ver=")) {
        $src = remove_query_arg("ver", $src);
    }
    return $src;
}
add_filter("style_loader_src", "mr_remove_version_from_assets", 9999);
add_filter("script_loader_src", "mr_remove_version_from_assets", 9999);

/**
 * Remove unnecessary meta tags from <head>
 */
function mr_remove_unnecessary_headers()
{
    // Remove Windows Live Writer manifest link
    remove_action("wp_head", "wlwmanifest_link");

    // Remove RSD (Really Simple Discovery) link
    remove_action("wp_head", "rsd_link");

    // Remove WordPress shortlink
    remove_action("wp_head", "wp_shortlink_wp_head");

    // Remove REST API link tag
    remove_action("wp_head", "rest_output_link_wp_head");

    // Remove oEmbed discovery links
    remove_action("wp_head", "wp_oembed_add_discovery_links");

    // Remove generator tag
    remove_action("wp_head", "wp_generator");
}
add_action("init", "mr_remove_unnecessary_headers");

/**
 * Disable XML-RPC (unless explicitly needed)
 */
add_filter("xmlrpc_enabled", "__return_false");

/**
 * Remove XML-RPC from HTTP headers
 */
function mr_remove_xmlrpc_pingback_ping($headers)
{
    unset($headers["X-Pingback"]);
    return $headers;
}
add_filter("wp_headers", "mr_remove_xmlrpc_pingback_ping");

/**
 * Hide login errors to prevent username enumeration
 */
function mr_login_errors()
{
    return esc_html__(
        "Invalid login credentials. Please try again.",
        "macedon-ranges",
    );
}
add_filter("login_errors", "mr_login_errors");

/**
 * Login attempt rate limiting
 */
function mr_check_login_attempts($user, $username, $password)
{
    // Skip for empty username
    if (empty($username)) {
        return $user;
    }

    $ip_address = mr_get_user_ip();
    $transient_key = "mr_login_attempts_" . md5($ip_address);
    $locked_key = "mr_login_locked_" . md5($ip_address);

    // Check if IP is locked
    if (get_transient($locked_key)) {
        $remaining_time = get_transient($locked_key);
        return new WP_Error(
            "login_locked",
            sprintf(
                /* translators: %d: minutes remaining */
                esc_html__(
                    "Too many login attempts. Please try again in %d minutes.",
                    "macedon-ranges",
                ),
                ceil($remaining_time / 60),
            ),
        );
    }

    return $user;
}
add_filter("authenticate", "mr_check_login_attempts", 30, 3);

/**
 * Track failed login attempts
 */
function mr_login_failed($username)
{
    $ip_address = mr_get_user_ip();
    $transient_key = "mr_login_attempts_" . md5($ip_address);
    $locked_key = "mr_login_locked_" . md5($ip_address);

    $attempts = (int) get_transient($transient_key);
    $attempts++;

    // Lock after 5 failed attempts
    $max_attempts = apply_filters("mr_max_login_attempts", 5);
    $lockout_duration = apply_filters(
        "mr_login_lockout_duration",
        30 * MINUTE_IN_SECONDS,
    );

    if ($attempts >= $max_attempts) {
        set_transient($locked_key, $lockout_duration, $lockout_duration);
        delete_transient($transient_key);

        // Log security event (optional - implement logging if needed)
        do_action("mr_login_locked", $ip_address, $username);
    } else {
        set_transient($transient_key, $attempts, 15 * MINUTE_IN_SECONDS);
    }
}
add_action("wp_login_failed", "mr_login_failed");

/**
 * Reset login attempts on successful login
 */
function mr_login_successful($username, $user)
{
    $ip_address = mr_get_user_ip();
    $transient_key = "mr_login_attempts_" . md5($ip_address);
    $locked_key = "mr_login_locked_" . md5($ip_address);

    delete_transient($transient_key);
    delete_transient($locked_key);
}
add_action("wp_login", "mr_login_successful", 10, 2);

/**
 * Get user IP address (proxy-aware)
 */
function mr_get_user_ip()
{
    $ip = "";

    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER["HTTP_CLIENT_IP"]));
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER["HTTP_X_FORWARDED_FOR"]));
    } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER["REMOTE_ADDR"]));
    }

    // Validate IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    return "0.0.0.0"; // Fallback
}

/**
 * Sanitize uploaded file names
 */
function mr_sanitize_file_name($filename)
{
    // Remove accents
    $filename = remove_accents($filename);

    // Convert to lowercase
    $filename = strtolower($filename);

    // Remove special characters (keep alphanumeric, dash, underscore, dot)
    $filename = preg_replace("/[^a-z0-9._-]/", "", $filename);

    // Remove multiple dots (prevent directory traversal)
    $filename = preg_replace("/\.+/", ".", $filename);

    // Remove leading/trailing dots and dashes
    $filename = trim($filename, ".-");

    return $filename;
}
add_filter("sanitize_file_name", "mr_sanitize_file_name", 10);

/**
 * Restrict file upload types
 */
function mr_upload_mimes($mimes)
{
    // Remove potentially dangerous file types
    unset($mimes["exe"]);
    unset($mimes["php"]);
    unset($mimes["phtml"]);
    unset($mimes["php3"]);
    unset($mimes["php4"]);
    unset($mimes["php5"]);
    unset($mimes["pl"]);
    unset($mimes["py"]);
    unset($mimes["jsp"]);
    unset($mimes["asp"]);
    unset($mimes["htm"]);
    unset($mimes["html"]);
    unset($mimes["swf"]);

    // Add safe file types if needed
    // $mimes['svg'] = 'image/svg+xml';
    // $mimes['webp'] = 'image/webp';

    return $mimes;
}
add_filter("upload_mimes", "mr_upload_mimes");

/**
 * Disable file editing in WordPress admin
 */
if (!defined("DISALLOW_FILE_EDIT")) {
    define("DISALLOW_FILE_EDIT", true);
}

/**
 * Remove emoji scripts and styles (performance + security)
 */
function mr_disable_emojis()
{
    remove_action("wp_head", "print_emoji_detection_script", 7);
    remove_action("admin_print_scripts", "print_emoji_detection_script");
    remove_action("wp_print_styles", "print_emoji_styles");
    remove_action("admin_print_styles", "print_emoji_styles");
    remove_filter("the_content_feed", "wp_staticize_emoji");
    remove_filter("comment_text_rss", "wp_staticize_emoji");
    remove_filter("wp_mail", "wp_staticize_emoji_for_email");
    add_filter("tiny_mce_plugins", "mr_disable_emojis_tinymce");
    add_filter(
        "wp_resource_hints",
        "mr_disable_emojis_remove_dns_prefetch",
        10,
        2,
    );
}
add_action("init", "mr_disable_emojis");

function mr_disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, ["wpemoji"]);
    }
    return [];
}

function mr_disable_emojis_remove_dns_prefetch($urls, $relation_type)
{
    if ("dns-prefetch" === $relation_type) {
        $emoji_svg_url = apply_filters(
            "emoji_svg_url",
            "https://s.w.org/images/core/emoji/",
        );
        $urls = array_diff($urls, [$emoji_svg_url]);
    }
    return $urls;
}

/**
 * REST API security
 * Restrict to authenticated users only (optional - adjust based on needs)
 */
function mr_rest_api_security($result)
{
    // Allow unauthenticated access to specific routes
    $allowed_routes = apply_filters("mr_rest_api_public_routes", [
        "/wp/v2/posts",
        "/wp/v2/pages",
        "/wp/v2/categories",
        "/wp/v2/tags",
    ]);

    // Get current route
    $current_route = $GLOBALS["wp"]->query_vars["rest_route"] ?? "";

    // Check if current route is in allowed list
    foreach ($allowed_routes as $allowed_route) {
        if (strpos($current_route, $allowed_route) === 0) {
            return $result;
        }
    }

    // Require authentication for all other routes
    if (!is_user_logged_in()) {
        return new WP_Error(
            "rest_not_logged_in",
            esc_html__(
                "You must be logged in to access this endpoint.",
                "macedon-ranges",
            ),
            ["status" => 401],
        );
    }

    return $result;
}
// Uncomment to enable REST API restrictions
// add_filter('rest_authentication_errors', 'mr_rest_api_security');

/**
 * Disable REST API user endpoints (prevent user enumeration)
 */
function mr_disable_rest_api_users($endpoints)
{
    if (isset($endpoints["/wp/v2/users"])) {
        unset($endpoints["/wp/v2/users"]);
    }
    if (isset($endpoints["/wp/v2/users/(?P<id>[\d]+)"])) {
        unset($endpoints["/wp/v2/users/(?P<id>[\d]+)"]);
    }
    return $endpoints;
}
add_filter("rest_endpoints", "mr_disable_rest_api_users");

/**
 * Prevent username enumeration via /?author=N
 */
function mr_prevent_author_enumeration()
{
    if (is_author() && !is_admin()) {
        // Only block if not logged in or if user has no posts
        if (!is_user_logged_in()) {
            wp_safe_redirect(home_url(), 301);
            exit();
        }
    }
}
add_action("template_redirect", "mr_prevent_author_enumeration");

/**
 * Add nonce to comments form
 */
function mr_comment_form_nonce()
{
    wp_nonce_field("mr_comment_nonce", "mr_comment_nonce_field");
}
add_action("comment_form", "mr_comment_form_nonce");

/**
 * Verify comment form nonce
 */
function mr_verify_comment_nonce($commentdata)
{
    if (
        !isset($_POST["mr_comment_nonce_field"]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["mr_comment_nonce_field"])),
            "mr_comment_nonce",
        )
    ) {
        wp_die(
            esc_html__(
                "Security check failed. Please refresh and try again.",
                "macedon-ranges",
            ),
        );
    }
    return $commentdata;
}
add_filter("preprocess_comment", "mr_verify_comment_nonce");

/**
 * Disable pingbacks
 */
function mr_disable_pingback(&$links)
{
    foreach ($links as $l => $link) {
        if (strpos($link, get_option("home")) === 0) {
            unset($links[$l]);
        }
    }
}
add_action("pre_ping", "mr_disable_pingback");

/**
 * Log security events (optional - implement full logging system if needed)
 */
function mr_log_security_event($event_type, $details = [])
{
    // Implement logging to database or file
    // This is a placeholder for a full logging system
    do_action("mr_security_event_logged", $event_type, $details);
}

// Hook for custom logging implementations
add_action(
    "mr_login_locked",
    function ($ip, $username) {
        mr_log_security_event("login_locked", [
            "ip" => $ip,
            "username" => $username,
            "timestamp" => current_time("mysql"),
        ]);
    },
    10,
    2,
);
