<?php
/**
 * Request a Demo popup form
 *
 * - Prints the popup form in the footer when the header custom button is
 *   set to "Open Request a Demo Form" (Customizer > Header & Topbar Settings)
 * - Enqueues the form's CSS/JS only when needed
 * - Handles the AJAX submit and emails the request to the support address
 *
 * @package aaapos-prime
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Is the header button set up to open the demo form?
 */
function aaapos_demo_form_enabled() {
    return get_theme_mod('header_cta_enable', false)
        && get_theme_mod('header_cta_demo_form', true)
        && trim((string) get_theme_mod('header_cta_text', '')) !== '';
}

/**
 * Is reCAPTCHA switched on for the demo form (Customizer > reCAPTCHA)?
 * Needs both keys to be filled in; shares them with the other forms.
 */
function aaapos_demo_recaptcha_enabled() {
    return get_theme_mod('recaptcha_enable_demo', false)
        && get_theme_mod('recaptcha_site_key', '') !== ''
        && get_theme_mod('recaptcha_secret_key', '') !== '';
}

/**
 * Product options.
 */
function aaapos_demo_products() {
    return [
        'RetailManager',
        'RM Mobile',
        'Webstore Manager',
        'Multi-Store',
        'Not sure yet',
        'Other',
    ];
}

/**
 * "How did you hear about us?" options (grouped).
 */
function aaapos_demo_sources() {
    return [
        __('Search Engines & AI', 'aaapos') => [
            'google'              => 'Google Search',
            'bing'                => 'Bing Search',
            'chatgpt'             => 'ChatGPT',
            'google_ai_overviews' => 'Google AI Overviews',
            'microsoft_copilot'   => 'Microsoft Copilot',
            'perplexity'          => 'Perplexity AI',
            'other_ai'            => 'Other AI tool',
        ],
        __('Advertising & Social Media', 'aaapos') => [
            'google_ads' => 'Google Ads',
            'facebook'   => 'Facebook',
            'instagram'  => 'Instagram',
            'linkedin'   => 'LinkedIn',
            'youtube'    => 'YouTube',
        ],
        __('Other Sources', 'aaapos') => [
            'referral'          => 'Word of Mouth / Referral',
            'existing_customer' => 'Existing AAAPOS Customer',
            'industry_event'    => 'Industry Event',
            'email'             => 'Email / Newsletter',
            'other'             => 'Other',
        ],
    ];
}

/**
 * Image for the right-hand side of the popup.
 * Uses the "Demo Form Image" from the Customizer if set, otherwise
 * reuses the Authentication Modal's login image. Empty string = no image.
 */
function aaapos_demo_image_url() {
    foreach (['demo_request_image', 'auth_modal_login_image'] as $mod) {
        $id = absint(get_theme_mod($mod, 0));
        if ($id) {
            $url = wp_get_attachment_image_url($id, 'full');
            if ($url) {
                return $url;
            }
        }
    }
    return '';
}

/**
 * Enqueue CSS + JS (only when the demo form is in use).
 */
function aaapos_demo_enqueue_assets() {
    if (!aaapos_demo_form_enabled()) {
        return;
    }

    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    $css_path = $dir . '/assets/css/components/demo-request.css';
    $js_path  = $dir . '/assets/js/demo-request.js';

    wp_enqueue_style(
        'aaapos-demo-request',
        $uri . '/assets/css/components/demo-request.css',
        [],
        file_exists($css_path) ? filemtime($css_path) : null
    );

    wp_enqueue_script(
        'aaapos-demo-request',
        $uri . '/assets/js/demo-request.js',
        [],
        file_exists($js_path) ? filemtime($js_path) : null,
        true
    );

    wp_localize_script('aaapos-demo-request', 'aaaposDemo', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'sending'  => __('Sending…', 'aaapos'),
        'error'    => __('Sorry, something went wrong. Please try again.', 'aaapos'),
        'recaptcha_site_key'  => aaapos_demo_recaptcha_enabled() ? get_theme_mod('recaptcha_site_key', '') : '',
        'recaptcha_required'  => __('Please tick the "I\'m not a robot" box.', 'aaapos'),
    ]);
}
add_action('wp_enqueue_scripts', 'aaapos_demo_enqueue_assets');

/**
 * Print the popup markup in the footer.
 */
function aaapos_demo_render_modal() {
    if (!aaapos_demo_form_enabled()) {
        return;
    }

    $arrow = '<svg class="demo-modal-btn-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $chevron = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $image_url = aaapos_demo_image_url();
    ?>
    <div class="demo-modal-overlay" id="demo-modal-overlay">
        <section class="demo-modal<?php echo $image_url ? '' : ' demo-modal--no-image'; ?>" id="demo-modal" role="dialog" aria-modal="true" aria-labelledby="demo-modal-title" aria-describedby="demo-modal-desc">
            <div class="demo-modal-split">
            <div class="demo-modal-left">

            <button class="demo-modal-close" id="demo-modal-close" type="button" aria-label="<?php esc_attr_e('Close demo request form', 'aaapos'); ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>

            <header class="demo-modal-header">
                <h2 id="demo-modal-title"><?php esc_html_e('Request a Demo', 'aaapos'); ?></h2>
                <p id="demo-modal-desc"><?php esc_html_e('Share a few details about your business. Our team will contact you to discuss the right solution.', 'aaapos'); ?></p>
            </header>

            <form class="demo-modal-form" id="demo-modal-form" novalidate>
                <div class="demo-modal-fields">

                    <div class="demo-modal-field">
                        <label for="demo-full-name"><?php esc_html_e('Full name', 'aaapos'); ?></label>
                        <input id="demo-full-name" name="fullName" type="text" autocomplete="name" maxlength="100" placeholder="<?php esc_attr_e('Your full name', 'aaapos'); ?>" required>
                    </div>

                    <div class="demo-modal-field">
                        <label for="demo-business-name"><?php esc_html_e('Business name', 'aaapos'); ?></label>
                        <input id="demo-business-name" name="businessName" type="text" autocomplete="organization" maxlength="120" placeholder="<?php esc_attr_e('Your business', 'aaapos'); ?>" required>
                    </div>

                    <div class="demo-modal-field demo-modal-full">
                        <label for="demo-email"><?php esc_html_e('Business email', 'aaapos'); ?></label>
                        <input id="demo-email" name="email" type="email" autocomplete="email" maxlength="120" placeholder="you@business.com" required>
                    </div>

                    <div class="demo-modal-field">
                        <label for="demo-phone"><?php esc_html_e('Phone number', 'aaapos'); ?> <span class="demo-optional"><?php esc_html_e('(optional)', 'aaapos'); ?></span></label>
                        <input id="demo-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" placeholder="<?php esc_attr_e('Your phone number', 'aaapos'); ?>">
                    </div>

                    <div class="demo-modal-field">
                        <label for="demo-product"><?php esc_html_e('Product of interest', 'aaapos'); ?></label>
                        <div class="demo-modal-select">
                            <select id="demo-product" name="product" required>
                                <option value="" selected disabled><?php esc_html_e('Select a product', 'aaapos'); ?></option>
                                <?php foreach (aaapos_demo_products() as $product) : ?>
                                    <option value="<?php echo esc_attr($product); ?>"><?php echo esc_html($product); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                    </div>

                    <div class="demo-modal-field demo-modal-full">
                        <label for="demo-source"><?php esc_html_e('How did you hear about us?', 'aaapos'); ?></label>
                        <div class="demo-modal-select">
                            <select id="demo-source" name="source" required>
                                <option value="" selected disabled><?php esc_html_e('Select an option', 'aaapos'); ?></option>
                                <?php foreach (aaapos_demo_sources() as $group => $options) : ?>
                                    <optgroup label="<?php echo esc_attr($group); ?>">
                                        <?php foreach ($options as $value => $label) : ?>
                                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                    </div>

                    <div class="demo-modal-field demo-modal-full">
                        <label for="demo-message"><?php esc_html_e("Anything else you'd like us to know?", 'aaapos'); ?> <span class="demo-optional"><?php esc_html_e('(optional)', 'aaapos'); ?></span></label>
                        <textarea id="demo-message" name="message" maxlength="2000" placeholder="<?php esc_attr_e("Tell us a little about your business or what you'd like to see...", 'aaapos'); ?>"></textarea>
                    </div>

                    <?php // Honeypot - hidden from people, bots tend to fill it in ?>
                    <div class="demo-modal-hp" aria-hidden="true">
                        <label for="demo-website"><?php esc_html_e('Website', 'aaapos'); ?></label>
                        <input id="demo-website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                </div>

                <div class="demo-modal-footer">
                    <?php if (aaapos_demo_recaptcha_enabled()) : ?>
                        <div class="demo-modal-recaptcha" id="demo-recaptcha"></div>
                    <?php endif; ?>
                    <button class="demo-modal-submit" type="submit">
                        <span class="demo-modal-submit-icon"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                        <span class="demo-modal-submit-label"><?php esc_html_e('Submit Demo Request', 'aaapos'); ?></span>
                    </button>
                    <div class="demo-modal-status" id="demo-modal-status" role="status" aria-live="polite"></div>
                </div>
            </form>

            <div class="demo-modal-success" id="demo-modal-success" hidden>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><path d="m8 12.5 2.8 2.8L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3><?php esc_html_e('Thanks, your request is in!', 'aaapos'); ?></h3>
                <p><?php esc_html_e('Our team will be in touch shortly to arrange your demo.', 'aaapos'); ?></p>
            </div>

            </div><!-- /.demo-modal-left -->

            <?php if ($image_url) : ?>
                <div class="demo-modal-right" style="background-image: url('<?php echo esc_url($image_url); ?>');" aria-hidden="true"></div>
            <?php endif; ?>

            </div><!-- /.demo-modal-split -->
        </section>
    </div>
    <?php
}
add_action('wp_footer', 'aaapos_demo_render_modal');

/**
 * AJAX: validate the form and email it to support.
 */
function aaapos_demo_request_handler() {
    if (!aaapos_demo_form_enabled()) {
        wp_send_json_error(['message' => __('This form is not available right now.', 'aaapos')]);
    }

    $success_message = __('Thanks, your request has been sent.', 'aaapos');

    // Honeypot: pretend it worked, send nothing.
    if (!empty($_POST['website'])) {
        wp_send_json_success(['message' => $success_message]);
    }

    $field = function ($key) {
        return isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
    };

    $name     = mb_substr($field('fullName'), 0, 100);
    $business = mb_substr($field('businessName'), 0, 120);
    $email    = sanitize_email($field('email'));
    $phone    = preg_replace('/[^0-9+()\-\s.]/', '', $field('phone'));
    $phone    = mb_substr(trim($phone), 0, 40);
    $product  = $field('product');
    $source   = $field('source');
    $message  = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $message  = mb_substr($message, 0, 2000);

    // Validate required fields and dropdown values.
    $source_labels = [];
    foreach (aaapos_demo_sources() as $options) {
        $source_labels += $options;
    }

    if ($name === '' || $business === '' || !is_email($email)
        || !in_array($product, aaapos_demo_products(), true)
        || !isset($source_labels[$source])) {
        wp_send_json_error(['message' => __('Please fill in all required fields with valid details.', 'aaapos')]);
    }

    // reCAPTCHA (when enabled in the Customizer)
    if (aaapos_demo_recaptcha_enabled()) {
        $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '';

        if (!function_exists('aaapos_verify_recaptcha_response') || !aaapos_verify_recaptcha_response($token)) {
            wp_send_json_error(['message' => __('reCAPTCHA verification failed. Please try again.', 'aaapos')]);
        }
    }

    // Basic rate limit: 5 requests per hour per IP.
    $ip    = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    $key   = 'aaapos_demo_' . md5($ip);
    $count = (int) get_transient($key);

    if ($count >= 5) {
        wp_send_json_error(['message' => __('Too many requests. Please try again later.', 'aaapos')]);
    }
    set_transient($key, $count + 1, HOUR_IN_SECONDS);

    // Recipient: Customizer setting, falling back to the site admin email.
    $to = sanitize_email((string) get_theme_mod('demo_request_email', ''));
    if (!is_email($to)) {
        $to = get_option('admin_email');
    }

    $subject = sprintf('New demo request: %s (%s)', $business, $product);

    $body  = "A new demo request was submitted on " . wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES) . ".\n\n";
    $body .= "Name: {$name}\n";
    $body .= "Business: {$business}\n";
    $body .= "Email: {$email}\n";
    $body .= 'Phone: ' . ($phone !== '' ? $phone : 'Not provided') . "\n";
    $body .= "Product of interest: {$product}\n";
    $body .= 'Heard about us via: ' . $source_labels[$source] . "\n\n";
    $body .= "Message:\n" . ($message !== '' ? $message : 'None') . "\n";

    // Reply goes straight to the visitor. Strip characters that could break the header.
    $reply_name = str_replace(['<', '>', '"', ',', "\r", "\n"], '', $name);
    $headers    = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $reply_name . ' <' . $email . '>',
    ];

    if (!wp_mail($to, $subject, $body, $headers)) {
        wp_send_json_error(['message' => __('Sorry, we could not send your request. Please try again.', 'aaapos')]);
    }

    wp_send_json_success(['message' => $success_message]);
}
add_action('wp_ajax_aaapos_demo_request', 'aaapos_demo_request_handler');
add_action('wp_ajax_nopriv_aaapos_demo_request', 'aaapos_demo_request_handler');