<?php
/**
 * Footer Customizer Settings - Complete Version with Payment Icons
 * UPDATED: Payment icon controls now show/hide based on master toggle
 *
 * @package aaapos-prime
 * @since 1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Register Footer Customizer Settings
 */
function aaapos_footer_customizer($wp_customize)
{
    // ===================================================================
    // FOOTER SECTION
    // ===================================================================

    $wp_customize->add_section("aaapos_footer_settings", [
        "title" => __("Footer Settings", "aaapos-prime"),
        "priority" => 120,
        "description" => __(
            "Customize your footer appearance and content. Social media links set here will also appear on the Contact page.",
            "aaapos-prime",
        ),
    ]);

    // -------------------------------------------------------------------
    // LAYOUT SETTINGS
    // -------------------------------------------------------------------

    // Footer Layout
    $wp_customize->add_setting("footer_layout", [
        "default" => "4-columns",
        "sanitize_callback" => "aaapos_sanitize_select",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_layout", [
        "label" => __("Footer Layout", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "select",
        "priority" => 10,
        "choices" => [
            "1-column" => __("1 Column", "aaapos-prime"),
            "2-columns" => __("2 Columns", "aaapos-prime"),
            "3-columns" => __("3 Columns", "aaapos-prime"),
            "4-columns" => __("4 Columns (Default)", "aaapos-prime"),
            "5-columns" => __("5 Columns", "aaapos-prime"),
        ],
    ]);

    // Footer Width
    $wp_customize->add_setting("footer_width", [
        "default" => "boxed",
        "sanitize_callback" => "aaapos_sanitize_select",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_width", [
        "label" => __("Footer Width", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "select",
        "priority" => 15,
        "choices" => [
            "boxed" => __("Boxed", "aaapos-prime"),
            "full-width" => __("Full Width", "aaapos-prime"),
        ],
    ]);

    // -------------------------------------------------------------------
    // BRAND COLUMN SETTINGS
    // -------------------------------------------------------------------

    // Show Logo in Footer
    $wp_customize->add_setting("footer_show_logo", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_show_logo", [
        "label" => __("Show Logo in Footer", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "checkbox",
        "priority" => 20,
    ]);

    // Footer Logo (Alternative)
    $wp_customize->add_setting("footer_logo", [
        "default" => "",
        "sanitize_callback" => "absint",
    ]);

    $wp_customize->add_control(
        new WP_Customize_Media_Control($wp_customize, "footer_logo", [
            "label" => __("Footer Logo (Optional)", "aaapos-prime"),
            "description" => __(
                "Upload a different logo for the footer. Leave empty to use the main site logo.",
                "aaapos-prime",
            ),
            "section" => "aaapos_footer_settings",
            "mime_type" => "image",
            "priority" => 25,
        ]),
    );

    // Footer Description
    $wp_customize->add_setting("footer_description", [
        "default" => __(
            "Your trusted source for fresh, locally sourced produce. Supporting local farmers and delivering quality to your doorstep.",
            "aaapos-prime",
        ),
        "sanitize_callback" => "wp_kses_post",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_description", [
        "label" => __("Footer Description", "aaapos-prime"),
        "description" => __(
            "Text that appears below the logo.",
            "aaapos-prime",
        ),
        "section" => "aaapos_footer_settings",
        "type" => "textarea",
        "priority" => 30,
    ]);

    // -------------------------------------------------------------------
    // CONTACT INFORMATION
    // -------------------------------------------------------------------

    // Section Heading
    $wp_customize->add_setting("footer_contact_heading", [
        "sanitize_callback" => "__return_false",
    ]);

    $wp_customize->add_control(
        new Aaapos_Heading_Control($wp_customize, "footer_contact_heading", [
            "label" => __("Contact Information", "aaapos-prime"),
            "section" => "aaapos_footer_settings",
            "priority" => 35,
        ]),
    );

    // Footer Address
    $wp_customize->add_setting("footer_address", [
        "default" => __(
            "123 Farm Road, Macedon Ranges VIC 3440",
            "aaapos-prime",
        ),
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_address", [
        "label" => __("Address", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "text",
        "priority" => 40,
    ]);

    // Footer Phone
    $wp_customize->add_setting("footer_phone", [
        "default" => "03 5427 3552",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_phone", [
        "label" => __("Phone Number", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "text",
        "priority" => 45,
    ]);

    // Footer Email
    $wp_customize->add_setting("footer_email", [
        "default" => "sales@macedonrangesproducestore.com.au",
        "sanitize_callback" => "sanitize_email",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_email", [
        "label" => __("Email Address", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "email",
        "priority" => 50,
    ]);

    // -------------------------------------------------------------------
    // SOCIAL MEDIA LINKS
    // -------------------------------------------------------------------

    // Section Heading
    $wp_customize->add_setting("footer_social_heading", [
        "sanitize_callback" => "__return_false",
    ]);

    $wp_customize->add_control(
        new Aaapos_Heading_Control($wp_customize, "footer_social_heading", [
            "label" => __("Social Media Links", "aaapos-prime"),
            "description" => __(
                "These links will appear in both the footer and on the Contact page.",
                "aaapos-prime",
            ),
            "section" => "aaapos_footer_settings",
            "priority" => 55,
        ]),
    );

    // Show Social Links
    $wp_customize->add_setting("footer_show_social", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_show_social", [
        "label" => __("Show Social Media Icons", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "checkbox",
        "priority" => 60,
    ]);

    // Social Media Platforms with default URLs
    $social_platforms = [
        "facebook" => [
            "label" => __("Facebook", "aaapos-prime"),
            "default" => "https://www.facebook.com/aaapos.retailmanager",
        ],
        "twitter" => [
            "label" => __("Twitter/X", "aaapos-prime"),
            "default" => "https://x.com/",
        ],
        "instagram" => [
            "label" => __("Instagram", "aaapos-prime"),
            "default" => "",
        ],
        "youtube" => [
            "label" => __("YouTube", "aaapos-prime"),
            "default" => "https://www.youtube.com/@aaapos",
        ],
        "linkedin" => [
            "label" => __("LinkedIn", "aaapos-prime"),
            "default" => "",
        ],
        "pinterest" => [
            "label" => __("Pinterest", "aaapos-prime"),
            "default" => "",
        ],
        "tiktok" => [
            "label" => __("TikTok", "aaapos-prime"),
            "default" => "",
        ],
        "whatsapp" => [
            "label" => __("WhatsApp", "aaapos-prime"),
            "default" => "",
        ],
    ];

    $priority = 65;
    foreach ($social_platforms as $platform => $data) {
        $wp_customize->add_setting("footer_social_" . $platform, [
            "default" => $data["default"],
            "sanitize_callback" => "esc_url_raw",
            "transport" => "postMessage",
        ]);

        $wp_customize->add_control("footer_social_" . $platform, [
            "label" => $data["label"] . " " . __("URL", "aaapos-prime"),
            "section" => "aaapos_footer_settings",
            "type" => "url",
            "priority" => $priority,
        ]);

        $priority += 5;
    }

    // Social Icon Style
    $wp_customize->add_setting("social_icon_style", [
        "default" => "rounded",
        "sanitize_callback" => "aaapos_sanitize_select",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("social_icon_style", [
        "label" => __("Social Icon Style", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "select",
        "priority" => 130,
        "choices" => [
            "circle" => __("Circle", "aaapos-prime"),
            "rounded" => __("Rounded Square", "aaapos-prime"),
            "square" => __("Square", "aaapos-prime"),
            "minimal" => __("Minimal (No Background)", "aaapos-prime"),
        ],
    ]);

    // -------------------------------------------------------------------
    // COPYRIGHT SETTINGS
    // -------------------------------------------------------------------

    // Section Heading
    $wp_customize->add_setting("footer_copyright_heading", [
        "sanitize_callback" => "__return_false",
    ]);

    $wp_customize->add_control(
        new Aaapos_Heading_Control($wp_customize, "footer_copyright_heading", [
            "label" => __("Copyright & Bottom Bar", "aaapos-prime"),
            "section" => "aaapos_footer_settings",
            "priority" => 135,
        ]),
    );

    // Copyright Text
    $wp_customize->add_setting("footer_copyright_text", [
        "default" => sprintf(
            __("© %s {sitename}. All rights reserved.", "aaapos-prime"),
            "{year}",
        ),
        "sanitize_callback" => "wp_kses_post",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_copyright_text", [
        "label" => __("Copyright Text", "aaapos-prime"),
        "description" => __(
            "Use {year} for current year and {sitename} for site name.",
            "aaapos-prime",
        ),
        "section" => "aaapos_footer_settings",
        "type" => "textarea",
        "priority" => 140,
    ]);

    // Show Footer Menu
    $wp_customize->add_setting("footer_show_menu", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_show_menu", [
        "label" => __("Show Footer Bottom Menu", "aaapos-prime"),
        "description" => __("Privacy Policy, Terms, etc.", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "checkbox",
        "priority" => 145,
    ]);

    // ============================================
    // PAYMENT ICONS SETTINGS
    // ============================================

    // Section Heading
    $wp_customize->add_setting("footer_payment_heading", [
        "sanitize_callback" => "__return_false",
    ]);

    $wp_customize->add_control(
        new Aaapos_Heading_Control($wp_customize, "footer_payment_heading", [
            "label" => __("Payment Method Icons", "aaapos-prime"),
            "description" => __(
                "Configure payment icons displayed in the footer bottom.",
                "aaapos-prime",
            ),
            "section" => "aaapos_footer_settings",
            "priority" => 146,
        ]),
    );

    // MASTER TOGGLE: Show/Hide Payment Icons Section
    $wp_customize->add_setting("footer_show_payment_icons", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_show_payment_icons", [
        "label" => __("Show Payment Icons", "aaapos-prime"),
        "description" => __(
            "Enable or disable the entire payment icons section in footer.",
            "aaapos-prime",
        ),
        "section" => "aaapos_footer_settings",
        "type" => "checkbox",
        "priority" => 147,
    ]);

    // Individual Payment Card Toggles (only visible when master toggle is ON)
    $payment_cards = [
        'visa' => __('Visa', 'aaapos-prime'),
        'mastercard' => __('Mastercard', 'aaapos-prime'),
        'amex' => __('American Express', 'aaapos-prime'),
        'paypal' => __('PayPal', 'aaapos-prime'),
        'discover' => __('Discover', 'aaapos-prime'),
    ];

    $priority = 148;
    foreach ($payment_cards as $card => $label) {
        $wp_customize->add_setting("payment_show_{$card}", [
            "default" => true,
            "sanitize_callback" => "wp_validate_boolean",
            "transport" => "refresh",
        ]);

        $wp_customize->add_control("payment_show_{$card}", [
            "label" => sprintf(__('Show %s', 'aaapos-prime'), $label),
            "section" => "aaapos_footer_settings",
            "type" => "checkbox",
            "priority" => $priority,
            "active_callback" => "aaapos_is_payment_icons_enabled",
        ]);

        $priority++;
    }

    // Payment Icon Upload Controls (only visible when master toggle is ON)
    $payment_icon_uploads = [
        'visa' => [
            'label' => __('Visa Icon', 'aaapos-prime'),
            'desc' => __('Upload Visa icon (PNG recommended, 100x60px).', 'aaapos-prime'),
            'priority' => 153,
        ],
        'mastercard' => [
            'label' => __('Mastercard Icon', 'aaapos-prime'),
            'desc' => __('Upload Mastercard icon (PNG recommended, 100x60px).', 'aaapos-prime'),
            'priority' => 154,
        ],
        'amex' => [
            'label' => __('American Express Icon', 'aaapos-prime'),
            'desc' => __('Upload Amex icon (PNG recommended, 100x60px).', 'aaapos-prime'),
            'priority' => 155,
        ],
        'paypal' => [
            'label' => __('PayPal Icon', 'aaapos-prime'),
            'desc' => __('Upload PayPal icon (PNG recommended, 100x60px).', 'aaapos-prime'),
            'priority' => 156,
        ],
        'discover' => [
            'label' => __('Discover Icon', 'aaapos-prime'),
            'desc' => __('Upload Discover icon (PNG recommended, 100x60px).', 'aaapos-prime'),
            'priority' => 157,
        ],
    ];

    foreach ($payment_icon_uploads as $card => $data) {
        $wp_customize->add_setting("payment_icon_{$card}", [
            "default" => "",
            "sanitize_callback" => "absint",
            "transport" => "refresh",
        ]);

        $wp_customize->add_control(
            new WP_Customize_Media_Control($wp_customize, "payment_icon_{$card}", [
                "label" => $data['label'],
                "description" => $data['desc'],
                "section" => "aaapos_footer_settings",
                "mime_type" => "image",
                "priority" => $data['priority'],
                "active_callback" => "aaapos_is_payment_icons_enabled",
            ]),
        );
    }

    // -------------------------------------------------------------------
    // BACK TO TOP BUTTON
    // -------------------------------------------------------------------

    // Section Heading
    $wp_customize->add_setting("footer_back_to_top_heading", [
        "sanitize_callback" => "__return_false",
    ]);

    $wp_customize->add_control(
        new Aaapos_Heading_Control(
            $wp_customize,
            "footer_back_to_top_heading",
            [
                "label" => __("Back to Top Button", "aaapos-prime"),
                "section" => "aaapos_footer_settings",
                "priority" => 160,
            ],
        ),
    );

    // Show Back to Top
    $wp_customize->add_setting("footer_show_back_to_top", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("footer_show_back_to_top", [
        "label" => __("Show Back to Top Button", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "checkbox",
        "priority" => 165,
    ]);

    // Back to Top Position
    $wp_customize->add_setting("footer_back_to_top_position", [
        "default" => "right",
        "sanitize_callback" => "aaapos_sanitize_select",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("footer_back_to_top_position", [
        "label" => __("Button Position", "aaapos-prime"),
        "section" => "aaapos_footer_settings",
        "type" => "select",
        "priority" => 170,
        "choices" => [
            "left" => __("Bottom Left", "aaapos-prime"),
            "right" => __("Bottom Right", "aaapos-prime"),
        ],
    ]);
}
add_action("customize_register", "aaapos_footer_customizer");

/**
 * Active Callback: Check if payment icons are enabled
 * Used to show/hide individual payment icon controls
 */
function aaapos_is_payment_icons_enabled() {
    return get_theme_mod('footer_show_payment_icons', true);
}

/**
 * Sanitize Select Fields
 */
function aaapos_sanitize_select($input, $setting)
{
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return array_key_exists($input, $choices) ? $input : $setting->default;
}

/**
 * Custom Heading Control
 */
if (class_exists("WP_Customize_Control")) {
    class Aaapos_Heading_Control extends WP_Customize_Control
    {
        public $type = "heading";
        public $description = "";

        public function render_content()
        {
            ?>
            <label>
                <span class="customize-control-title" style="font-size: 14px; font-weight: 600; color: #0073aa; border-bottom: 2px solid #0073aa; padding-bottom: 5px; display: block; margin-top: 20px;">
                    <?php echo esc_html($this->label); ?>
                </span>
                <?php if (!empty($this->description)): ?>
                    <span class="description customize-control-description" style="display: block; margin-top: 8px; font-style: italic; color: #666;">
                        <?php echo esc_html($this->description); ?>
                    </span>
                <?php endif; ?>
            </label>
            <?php
        }
    }
}