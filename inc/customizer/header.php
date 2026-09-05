<?php
/**
 * Header and Topbar customizer settings
 */

// ===================================
// DEFAULT ANNOUNCEMENTS
// ===================================
if (!function_exists("mr_default_topbar_announcements")) {
    function mr_default_topbar_announcements()
    {
        return [
            [
                "text" => "RetailManager v14 Update is here!",
                "url" => "https://www.aaapos.com/aaapos-retailmanager-v14/",
            ],
            [
                "text" => "RM Mobile is now released!",
                "url" => "",
            ],
            [
                "text" => "Webstore Manager Update available now",
                "url" => "https://www.aaapos.com/aaapos-webstore-manager/",
            ],
        ];
    }
}

if (!function_exists("mr_sanitize_topbar_announcements")) {
    function mr_sanitize_topbar_announcements($input)
    {
        $items = json_decode($input, true);

        if (!is_array($items)) {
            return wp_json_encode([]);
        }

        $sanitized = [];

        foreach ($items as $item) {
            $text = isset($item["text"]) ? sanitize_text_field($item["text"]) : "";

            if ($text === "") {
                continue;
            }

            $url = isset($item["url"]) && $item["url"] !== "" ? esc_url_raw($item["url"]) : "#";

            $sanitized[] = [
                "text" => $text,
                "url" => $url,
            ];
        }

        return wp_json_encode($sanitized);
    }
}

// ===================================
// CUSTOM CONTROL: DRAG & DROP ANNOUNCEMENTS
// ===================================
if (class_exists("WP_Customize_Control") && !class_exists("MR_Announcements_Control")) {
    class MR_Announcements_Control extends WP_Customize_Control
    {
        public $type = "mr_announcements";

        public function enqueue()
        {
            wp_enqueue_script("jquery-ui-sortable");
        }

        public function render_content()
        {
            $value = $this->value();
            $items = json_decode($value, true);

            if (!is_array($items)) {
                $items = [];
            }
            ?>
            <label class="customize-control-title-wrap">
                <?php if ($this->label) : ?>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <?php endif; ?>
                <?php if ($this->description) : ?>
                    <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
                <?php endif; ?>
            </label>

            <div class="mr-announcements-control">
                <ul class="mr-announcements-list">
                    <?php foreach ($items as $item) : ?>
                        <li class="mr-announcement-item">
                            <span class="mr-announcement-handle dashicons dashicons-menu" title="<?php esc_attr_e("Drag to reorder", "macedon-ranges"); ?>"></span>
                            <div class="mr-announcement-fields">
                                <input type="text" class="mr-announcement-text" placeholder="<?php esc_attr_e("Announcement text", "macedon-ranges"); ?>" value="<?php echo esc_attr($item["text"] ?? ""); ?>">
                                <input type="text" class="mr-announcement-url" placeholder="<?php esc_attr_e("URL (leave blank for #)", "macedon-ranges"); ?>" value="<?php echo esc_attr($item["url"] ?? ""); ?>">
                            </div>
                            <button type="button" class="mr-announcement-remove" aria-label="<?php esc_attr_e("Remove", "macedon-ranges"); ?>">&times;</button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <button type="button" class="button mr-announcement-add"><?php esc_html_e("+ Add Announcement", "macedon-ranges"); ?></button>

                <input type="hidden" class="mr-announcements-input" <?php $this->link(); ?> value="<?php echo esc_attr($value); ?>">
            </div>
            <?php
        }
    }
}

// ===================================
// CUSTOMIZER ASSETS FOR THE ANNOUNCEMENTS CONTROL
// ===================================
function mr_announcements_control_assets()
{
    wp_enqueue_script("jquery-ui-sortable");

    $css = "
        .mr-announcements-control { margin-top: 10px; }
        .mr-announcements-list { margin: 0 0 10px; padding: 0; list-style: none; }
        .mr-announcement-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 3px;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        .mr-announcement-handle {
            cursor: grab;
            color: #8c8f94;
            flex: 0 0 auto;
        }
        .mr-announcement-item.ui-sortable-helper { box-shadow: 0 2px 6px rgba(0,0,0,.15); }
        .mr-announcement-fields {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        .mr-announcement-fields input {
            width: 100%;
        }
        .mr-announcement-remove {
            flex: 0 0 auto;
            background: none;
            border: none;
            color: #b32d2e;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            padding: 4px 6px;
        }
        .mr-announcement-remove:hover { color: #dc3232; }
        .mr-announcement-add { width: 100%; }
    ";

    $js = "
        (function ($) {
            function serialize(\$control) {
                var items = [];
                \$control.find('.mr-announcement-item').each(function () {
                    var \$item = $(this);
                    var text = \$item.find('.mr-announcement-text').val();
                    var url = \$item.find('.mr-announcement-url').val();
                    if (text) {
                        items.push({ text: text, url: url || '#' });
                    }
                });
                \$control.find('.mr-announcements-input').val(JSON.stringify(items)).trigger('change');
            }

            function newItem() {
                return $(
                    '<li class=\"mr-announcement-item\">' +
                        '<span class=\"mr-announcement-handle dashicons dashicons-menu\"></span>' +
                        '<div class=\"mr-announcement-fields\">' +
                            '<input type=\"text\" class=\"mr-announcement-text\" placeholder=\"Announcement text\" value=\"\">' +
                            '<input type=\"text\" class=\"mr-announcement-url\" placeholder=\"URL (leave blank for #)\" value=\"\">' +
                        '</div>' +
                        '<button type=\"button\" class=\"mr-announcement-remove\">&times;</button>' +
                    '</li>'
                );
            }

            function bind(\$control) {
                \$control.find('.mr-announcements-list').sortable({
                    handle: '.mr-announcement-handle',
                    axis: 'y',
                    update: function () { serialize(\$control); }
                });

                \$control.on('input', '.mr-announcement-text, .mr-announcement-url', function () {
                    serialize(\$control);
                });

                \$control.on('click', '.mr-announcement-remove', function (e) {
                    e.preventDefault();
                    $(this).closest('.mr-announcement-item').remove();
                    serialize(\$control);
                });

                \$control.on('click', '.mr-announcement-add', function (e) {
                    e.preventDefault();
                    var \$li = newItem();
                    \$control.find('.mr-announcements-list').append(\$li);
                    \$li.find('.mr-announcement-text').trigger('focus');
                    serialize(\$control);
                });
            }

            wp.customize.control('topbar_announcements', function (control) {
                control.container.ready(function () {
                    bind(control.container.find('.mr-announcements-control'));
                });
            });
        })(jQuery);
    ";

    wp_add_inline_style("customize-controls", $css);
    wp_add_inline_script("customize-controls", $js);
}
add_action("customize_controls_enqueue_scripts", "mr_announcements_control_assets");

function mr_header_customizer($wp_customize)
{
    // Header Section
    $wp_customize->add_section("mr_header", [
        "title" => __("Header & Topbar Settings", "macedon-ranges"),
        "priority" => 30,
    ]);

    // ===================================
    // TOP BAR SETTINGS
    // ===================================

    $wp_customize->add_setting("show_top_bar", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh", // Changed to refresh
    ]);

    $wp_customize->add_control("show_top_bar", [
        "label" => __("Show Top Bar", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 10,
    ]);

    $wp_customize->add_setting("topbar_phone", [
        "default" => "+61 3 5420 0000",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("topbar_phone", [
        "label" => __("Top Bar Phone", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "text",
        "priority" => 20,
    ]);

    $wp_customize->add_setting("topbar_email", [
        "default" => "info@macedonrangesproduce.com",
        "sanitize_callback" => "sanitize_email",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control("topbar_email", [
        "label" => __("Top Bar Email", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "email",
        "priority" => 30,
    ]);

    $wp_customize->add_setting("topbar_promo_text", [
    "default" => "New Home of MYOB RetailManager",
    "sanitize_callback" => "sanitize_text_field",
    "transport" => "postMessage",
]);

$wp_customize->add_control("topbar_promo_text", [
    "label" => __("Top Bar Promo Text", "macedon-ranges"),
    "description" => __("Leave blank to hide this text entirely.", "macedon-ranges"),
    "section" => "mr_header",
    "type" => "text",
    "priority" => 40,
]);

    // ===================================
    // TOP BAR ANNOUNCEMENTS (DRAG & DROP)
    // ===================================

    $wp_customize->add_setting("topbar_announcements", [
        "default" => wp_json_encode(mr_default_topbar_announcements()),
        "sanitize_callback" => "mr_sanitize_topbar_announcements",
        "transport" => "postMessage",
    ]);

    $wp_customize->add_control(
        new MR_Announcements_Control($wp_customize, "topbar_announcements", [
            "label" => __("Top Bar Live Update Slider", "macedon-ranges"),
            "description" => __("Add, remove, and drag to reorder the rotating announcements. Leave URL blank to use #.", "macedon-ranges"),
            "section" => "mr_header",
            "priority" => 45,
        ])
    );

    // ===================================
    // HEADER SETTINGS
    // ===================================

    $wp_customize->add_setting("sticky_header", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh", // Changed to refresh so body class updates
    ]);

    $wp_customize->add_control("sticky_header", [
        "label" => __("Sticky Header", "macedon-ranges"),
        "description" => __("Keep header visible when scrolling down", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 50,
    ]);

    // ===================================
    // HEADER ELEMENTS VISIBILITY
    // ===================================

    $wp_customize->add_setting("show_search_bar", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_search_bar", [
        "label" => __("Show Search Bar", "macedon-ranges"),
        "description" => __("Display search bar in header", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 55,
    ]);

    // Show/Hide Account Icon
    $wp_customize->add_setting("show_account_icon", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_account_icon", [
        "label" => __("Show Account/Profile Icon", "macedon-ranges"),
        "description" => __("Display account login/profile link in header", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 56,
    ]);

    // Show/Hide Cart Icon
    $wp_customize->add_setting("show_cart_icon", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("show_cart_icon", [
        "label" => __("Show Shopping Cart Icon", "macedon-ranges"),
        "description" => __("Display shopping cart in header (WooCommerce required)", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "checkbox",
        "priority" => 57,
    ]);

    // ===================================
    // CART ICON STYLE (only if cart is shown)
    // ===================================

    $wp_customize->add_setting("cart_icon_style", [
        "default" => "icon-count",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);

    $wp_customize->add_control("cart_icon_style", [
        "label" => __("Cart Icon Style", "macedon-ranges"),
        "description" => __("Choose how to display the cart icon", "macedon-ranges"),
        "section" => "mr_header",
        "type" => "select",
        "choices" => [
            "icon-only" => __("Icon Only", "macedon-ranges"),
            "icon-count" => __("Icon + Item Count", "macedon-ranges"),
            "icon-total" => __("Icon + Cart Total", "macedon-ranges"),
        ],
        "priority" => 58,
        "active_callback" => function() {
            return get_theme_mod("show_cart_icon", true);
        },
    ]);
}
add_action("customize_register", "mr_header_customizer");