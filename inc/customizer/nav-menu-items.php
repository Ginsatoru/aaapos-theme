<?php
/**
 * Navigation Menu Items - Icon & Description Customizer Settings
 *
 * Lets you add an icon and description to individual dropdown menu
 * items, independent of WordPress's native menu editor. Data is stored
 * per-item via theme_mod, keyed by the menu item's ID.
 *
 * Icon picker: AAAPOS_Icon_Picker_Control renders the curated icon set
 * (aaapos_nav_icon_choices() in class-aaapos-nav-walker.php) as an
 * actual clickable SVG grid instead of a text <select>. It's a single
 * plain setting (icon key, or 'custom') using core's standard
 * data-customize-setting-link binding - no fragile multi-setting
 * wiring. Selecting the "custom" swatch reveals a native
 * WP_Customize_Media_Control (nav_item_icon_custom_{ID}, stores an
 * attachment ID) for uploading/picking an image or GIF from the media
 * library - reusing WordPress's own tested upload UI instead of a
 * hand-rolled one.
 *
 * Each top-level dropdown also gets a "Columns" control (1-5, default
 * 3) stored as nav_menu_columns_{top_level_item_id}, letting you set
 * how many grid columns that specific dropdown uses.
 *
 * Style switch: each top-level dropdown also gets a "Style" control
 * (nav_menu_style_{top_level_item_id}, 'icon' default or 'image').
 * 'icon' is the icon-picker behavior above. 'image' hides the icon
 * picker/custom-icon controls per item and shows a single larger
 * "Menu Item Image" upload instead (nav_item_image_{ID}, its own
 * attachment ID, independent of nav_item_icon_custom_{ID}). Which
 * control set is visible for a given item is toggled client-side in
 * aaapos_icon_picker_customizer_assets(), using the item-ID map built
 * by aaapos_nav_style_groups() while this file's controls register.
 *
 * Rendering is handled by AAAPOS_Nav_Walker
 * (inc/class-aaapos-nav-walker.php).
 *
 * @package AAAPOS_Prime
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('aaapos_nav_column_choices')) {
    function aaapos_nav_column_choices() {
        return array(
            '1' => __('1 Column', 'aaapos-prime'),
            '2' => __('2 Columns', 'aaapos-prime'),
            '3' => __('3 Columns', 'aaapos-prime'),
            '4' => __('4 Columns', 'aaapos-prime'),
            '5' => __('5 Columns', 'aaapos-prime'),
        );
    }
}

if (!function_exists('aaapos_sanitize_nav_columns')) {
    function aaapos_sanitize_nav_columns($value) {
        $value = (int) $value;
        if ($value < 1) {
            $value = 1;
        }
        if ($value > 5) {
            $value = 5;
        }
        return (string) $value;
    }
}

if (!function_exists('aaapos_sanitize_nav_icon_key')) {
    function aaapos_sanitize_nav_icon_key($value) {
        $value = sanitize_key($value);
        if ('' === $value || 'custom' === $value || isset(aaapos_nav_icon_choices()[$value])) {
            return $value;
        }
        return '';
    }
}

if (!function_exists('aaapos_nav_style_choices')) {
    function aaapos_nav_style_choices() {
        return array(
            'icon'  => __('Icon Style', 'aaapos-prime'),
            'image' => __('Image Style', 'aaapos-prime'),
        );
    }
}

if (!function_exists('aaapos_sanitize_nav_style')) {
    function aaapos_sanitize_nav_style($value) {
        return isset(aaapos_nav_style_choices()[$value]) ? $value : 'icon';
    }
}

if (!function_exists('aaapos_nav_style_groups')) {
    /**
     * Tracks which menu-item IDs belong to which top-level dropdown's
     * Style control, so the Customizer JS (enqueued via
     * customize_controls_enqueue_scripts, after this file's
     * customize_register loop has already run) knows which icon vs.
     * image controls to show/hide when a dropdown's style is
     * switched. Call with both args to register an item, no args to
     * read the accumulated map back.
     */
    function aaapos_nav_style_groups($add_style_id = null, $add_item_id = null) {
        static $groups = array();

        if (null !== $add_style_id && null !== $add_item_id) {
            $groups[$add_style_id][] = $add_item_id;
        }

        return $groups;
    }
}

/**
 * Visual icon picker: renders the curated SVGs as clickable swatches.
 * A single setting (this control's own $this->setting, standard core
 * behavior - no 'settings' array override) holds the icon key or
 * 'custom'. Swatch clicks write to the standard
 * data-customize-setting-link-bound hidden input, exactly like core's
 * own JS-light controls, so there's no custom two-way-binding code to
 * get wrong.
 */
if (class_exists('WP_Customize_Control') && !class_exists('AAAPOS_Icon_Picker_Control')) {
    class AAAPOS_Icon_Picker_Control extends WP_Customize_Control {
        public $type = 'aaapos_icon_picker';

        public function render_content() {
            $icons = aaapos_nav_icon_choices();
            $value = $this->value();

            if ('' === $value) {
                $current_label = __('None', 'aaapos-prime');
                $current_svg   = '';
            } elseif ('custom' === $value) {
                $current_label = __('Custom Image / GIF', 'aaapos-prime');
                $current_svg   = '';
            } else {
                $current_label = isset($icons[$value]) ? $icons[$value]['label'] : __('None', 'aaapos-prime');
                $current_svg   = isset($icons[$value]) ? $icons[$value]['svg'] : '';
            }
            ?>
            <label>
                <?php if (!empty($this->label)) : ?>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <?php endif; ?>
                <?php if (!empty($this->description)) : ?>
                    <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
                <?php endif; ?>
                <input type="hidden" class="aaapos-icon-picker-value" <?php $this->link(); ?> value="<?php echo esc_attr($value); ?>" />
            </label>

            <button type="button" class="icon-picker-toggle" aria-expanded="false">
                <span class="icon-picker-toggle__preview<?php echo ('' === $current_svg) ? ' icon-picker-toggle__preview--none' : ''; ?>">
                    <?php echo $current_svg; // phpcs:ignore -- curated fixed SVG set, not user input ?>
                </span>
                <span class="icon-picker-toggle__label"><?php echo esc_html($current_label); ?></span>
                <span class="icon-picker-toggle__caret" aria-hidden="true">&#9662;</span>
            </button>

            <div class="aaapos-icon-picker" hidden>
                <button type="button" class="icon-swatch icon-swatch--none<?php echo ('' === $value) ? ' is-active' : ''; ?>" data-icon="" data-label="<?php esc_attr_e('None', 'aaapos-prime'); ?>" title="<?php esc_attr_e('None', 'aaapos-prime'); ?>">
                    <span class="icon-swatch__slash" aria-hidden="true"></span>
                </button>

                <?php foreach ($icons as $key => $data) : ?>
                    <button type="button" class="icon-swatch<?php echo ($value === $key) ? ' is-active' : ''; ?>" data-icon="<?php echo esc_attr($key); ?>" data-label="<?php echo esc_attr($data['label']); ?>" title="<?php echo esc_attr($data['label']); ?>">
                        <?php echo $data['svg']; // phpcs:ignore -- curated fixed SVG set, not user input ?>
                    </button>
                <?php endforeach; ?>

                <button type="button" class="icon-swatch icon-swatch--custom<?php echo ('custom' === $value) ? ' is-active' : ''; ?>" data-icon="custom" data-label="<?php esc_attr_e('Custom Image / GIF', 'aaapos-prime'); ?>" title="<?php esc_attr_e('Custom Image / GIF', 'aaapos-prime'); ?>">
                    <span class="icon-swatch__custom-label" aria-hidden="true"><?php esc_html_e('IMG', 'aaapos-prime'); ?></span>
                </button>
            </div>
            <?php
        }
    }
}

/**
 * Enqueues the icon-picker's own CSS/JS into the Customizer admin
 * screen only (inline, attached to core's always-loaded
 * 'customize-controls' handle - no separate asset files/enqueue.php
 * entry needed).
 */
if (!function_exists('aaapos_icon_picker_customizer_assets')) {
    function aaapos_icon_picker_customizer_assets() {
        $css = '
            .icon-picker-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                width: 100%;
                padding: 6px 10px;
                margin: 6px 0;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                background: #fff;
                cursor: pointer;
                text-align: left;
            }
            .icon-picker-toggle:hover {
                border-color: #2271b1;
            }
            .icon-picker-toggle[aria-expanded="true"] {
                border-color: #2271b1;
                box-shadow: 0 0 0 1px #2271b1;
            }
            .icon-picker-toggle__preview {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 22px;
                height: 22px;
                color: #50575e;
            }
            .icon-picker-toggle__preview svg {
                width: 18px;
                height: 18px;
                stroke-width: 1.75;
            }
            .icon-picker-toggle__preview--none {
                opacity: 0.4;
            }
            .icon-picker-toggle__label {
                flex: 1;
                font-size: 13px;
                color: #1d2327;
            }
            .icon-picker-toggle__caret {
                flex-shrink: 0;
                font-size: 10px;
                color: #787c82;
                transition: transform 0.15s ease;
            }
            .icon-picker-toggle[aria-expanded="true"] .icon-picker-toggle__caret {
                transform: rotate(180deg);
            }
            .aaapos-icon-picker {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(34px, 1fr));
                gap: 6px;
                margin: 0 0 10px;
                padding: 8px;
                border: 1px solid #dcdcde;
                border-top: none;
                border-radius: 0 0 4px 4px;
            }
            .aaapos-icon-picker[hidden] {
                display: none;
            }
            .aaapos-icon-picker .icon-swatch {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                padding: 0;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                background: #fff;
                cursor: pointer;
                color: #50575e;
            }
            .aaapos-icon-picker .icon-swatch:hover {
                border-color: #2271b1;
                color: #2271b1;
            }
            .aaapos-icon-picker .icon-swatch.is-active {
                border-color: #2271b1;
                background: #f0f6fc;
                color: #2271b1;
                box-shadow: 0 0 0 1px #2271b1;
            }
            .aaapos-icon-picker .icon-swatch svg {
                width: 18px;
                height: 18px;
                stroke-width: 1.75;
            }
            .aaapos-icon-picker .icon-swatch--none .icon-swatch__slash {
                width: 16px;
                height: 2px;
                background: #a7aaad;
                transform: rotate(45deg);
            }
            .aaapos-icon-picker .icon-swatch__custom-label {
                font-size: 9px;
                font-weight: 600;
                letter-spacing: 0.02em;
                color: #a7aaad;
            }
            .aaapos-icon-picker .icon-swatch--custom.is-active .icon-swatch__custom-label {
                color: #2271b1;
            }
        ';
        wp_add_inline_style('customize-controls', $css);

        // Style-group map (style_setting_id => [item IDs]) built while
        // aaapos_nav_menu_items_customizer() registered controls -
        // handed to the JS below so it knows which icon/image controls
        // belong to which dropdown's Style switch.
        $style_groups_data = 'window.aaaposNavStyleGroups = ' . wp_json_encode(aaapos_nav_style_groups()) . ';';
        wp_add_inline_script('customize-controls', $style_groups_data);

        $js = '
            (function ($) {
                $(document).on("click", ".icon-picker-toggle", function (e) {
                    e.preventDefault();
                    var $toggle = $(this);
                    var $grid   = $toggle.next(".aaapos-icon-picker");
                    var open    = $toggle.attr("aria-expanded") === "true";

                    open = !open;
                    $toggle.attr("aria-expanded", open ? "true" : "false");
                    $grid.prop("hidden", !open);
                });

                $(document).on("click", ".aaapos-icon-picker .icon-swatch", function (e) {
                    e.preventDefault();
                    var $btn    = $(this);
                    var $grid   = $btn.closest(".aaapos-icon-picker");
                    var $toggle = $grid.prev(".icon-picker-toggle");
                    var $picker = $btn.closest(".customize-control");
                    var $input  = $picker.find("input.aaapos-icon-picker-value");
                    var icon    = ($btn.data("icon") || "").toString();
                    var label   = ($btn.data("label") || "").toString();

                    $input.val(icon).trigger("change");
                    $grid.find(".icon-swatch").removeClass("is-active");
                    $btn.addClass("is-active");

                    $toggle.find(".icon-picker-toggle__preview")
                        .toggleClass("icon-picker-toggle__preview--none", !icon)
                        .html(icon ? $btn.html() : "");
                    $toggle.find(".icon-picker-toggle__label").text(label);

                    // Collapse after picking - keeps the sections short.
                    $toggle.attr("aria-expanded", "false");
                    $grid.prop("hidden", true);
                });

                wp.customize.bind("ready", function () {
                    var styleGroups = window.aaaposNavStyleGroups || {};

                    // Reverse the style-group map so, given an item ID, we
                    // can look up which dropdown Style control governs it.
                    var itemStyleMap = {};
                    $.each(styleGroups, function (styleId, itemIds) {
                        $.each(itemIds, function (i, itemId) {
                            itemStyleMap[itemId] = styleId;
                        });
                    });

                    function isImageStyle(itemId) {
                        var styleId = itemStyleMap[itemId];
                        var styleControl = styleId ? wp.customize.control(styleId) : null;
                        return !!styleControl && styleControl.setting.get() === "image";
                    }

                    // Per item: Icon Style shows the icon picker (and its
                    // "custom" sub-control only when that swatch is picked);
                    // Image Style shows the single image-upload control
                    // instead. Only one of the two control sets is ever
                    // visible for a given item.
                    function refreshItemControls(itemId) {
                        var iconControl   = wp.customize.control("nav_item_icon_" + itemId);
                        var customControl = wp.customize.control("nav_item_icon_custom_" + itemId);
                        var imageControl  = wp.customize.control("nav_item_image_" + itemId);
                        var imageStyle    = isImageStyle(itemId);

                        if (iconControl) {
                            iconControl.container.toggle(!imageStyle);
                        }
                        if (customControl) {
                            var iconVal = iconControl ? iconControl.setting.get() : "";
                            customControl.container.toggle(!imageStyle && iconVal === "custom");
                        }
                        if (imageControl) {
                            imageControl.container.toggle(imageStyle);
                        }
                    }

                    wp.customize.control.each(function (control) {
                        if (control.params.type !== "aaapos_icon_picker") {
                            return;
                        }

                        var itemId = control.id.replace("nav_item_icon_", "");
                        control.setting.bind(function () { refreshItemControls(itemId); });
                        refreshItemControls(itemId);
                    });

                    $.each(styleGroups, function (styleId, itemIds) {
                        var styleControl = wp.customize.control(styleId);
                        if (!styleControl) {
                            return;
                        }

                        styleControl.setting.bind(function () {
                            $.each(itemIds, function (i, itemId) {
                                refreshItemControls(itemId);
                            });
                        });
                    });
                });
            })(jQuery);
        ';
        wp_add_inline_script('customize-controls', $js);
    }
    add_action('customize_controls_enqueue_scripts', 'aaapos_icon_picker_customizer_assets');
}

function aaapos_nav_menu_items_customizer($wp_customize) {

    $wp_customize->add_panel('aaapos_nav_menu_items_panel', array(
        'title'    => __('Menu Options', 'aaapos-prime'),
        'priority' => 200,
    ));

    $registered_locations = get_registered_nav_menus();
    $assigned_locations    = get_nav_menu_locations();

    foreach ($registered_locations as $location_id => $location_label) {

        if (empty($assigned_locations[$location_id])) {
            continue; // No menu assigned to this location
        }

        $menu = wp_get_nav_menu_object($assigned_locations[$location_id]);

        if (!$menu) {
            continue;
        }

        $menu_items = wp_get_nav_menu_items($menu->term_id);

        if (empty($menu_items)) {
            continue;
        }

        // Index items by ID for parent lookups
        $items_by_id = array();
        foreach ($menu_items as $item) {
            $items_by_id[$item->ID] = $item;
        }

        // Group every sub-item (depth 1+) under its top-level ancestor,
        // regardless of how deeply nested it is.
        $groups = array(); // top_level_id => array of items

        foreach ($menu_items as $item) {
            if ((int) $item->menu_item_parent === 0) {
                continue; // top-level item itself, not a dropdown item
            }

            // Walk up the parent chain to find the top-level ancestor
            $ancestor_id = (int) $item->menu_item_parent;
            while (isset($items_by_id[$ancestor_id]) && (int) $items_by_id[$ancestor_id]->menu_item_parent !== 0) {
                $ancestor_id = (int) $items_by_id[$ancestor_id]->menu_item_parent;
            }

            $groups[$ancestor_id][] = $item;
        }

        if (empty($groups)) {
            continue; // No dropdown items in this menu - nothing to configure
        }

        $section_priority = 10;

        foreach ($groups as $top_level_id => $items) {
            $top_level_title = isset($items_by_id[$top_level_id])
                ? $items_by_id[$top_level_id]->title
                : $location_label;

            $section_id = 'aaapos_nav_group_' . sanitize_key($location_id) . '_' . $top_level_id;

            $wp_customize->add_section($section_id, array(
                'title'    => $top_level_title,
                'panel'    => 'aaapos_nav_menu_items_panel',
                'priority' => $section_priority,
            ));

            // Style switch - Icon Style (default, curated SVG/custom icon
            // picker) vs Image Style (single larger image upload per item).
            // Sits above Columns so it reads as the top-level choice for
            // the whole dropdown.
            $style_setting_id = "nav_menu_style_{$top_level_id}";

            $wp_customize->add_setting($style_setting_id, array(
                'default'           => 'icon',
                'sanitize_callback' => 'aaapos_sanitize_nav_style',
                'transport'         => 'refresh',
            ));

            $wp_customize->add_control($style_setting_id, array(
                'label'       => __('Style', 'aaapos-prime'),
                'description' => __('Icon Style shows a small icon per item. Image Style shows a single larger uploaded image per item instead.', 'aaapos-prime'),
                'section'     => $section_id,
                'type'        => 'select',
                'choices'     => aaapos_nav_style_choices(),
                'priority'    => 0,
            ));

            // Column count control - sits above the per-item settings.
            $columns_setting_id = "nav_menu_columns_{$top_level_id}";

            $wp_customize->add_setting($columns_setting_id, array(
                'default'           => '3',
                'sanitize_callback' => 'aaapos_sanitize_nav_columns',
                'transport'         => 'refresh',
            ));

            $wp_customize->add_control($columns_setting_id, array(
                'label'       => __('Columns', 'aaapos-prime'),
                'description' => __('Number of grid columns for this dropdown.', 'aaapos-prime'),
                'section'     => $section_id,
                'type'        => 'select',
                'choices'     => aaapos_nav_column_choices(),
                'priority'    => 1,
            ));

            $priority = 10;

            foreach ($items as $item) {
                $icon_setting_id   = "nav_item_icon_{$item->ID}";
                $custom_setting_id = "nav_item_icon_custom_{$item->ID}";
                $image_setting_id  = "nav_item_image_{$item->ID}";
                $desc_setting_id   = "nav_item_desc_{$item->ID}";

                // Registers this item under its dropdown's Style control so
                // the Customizer JS (inc/customizer/nav-menu-items.php
                // aaapos_icon_picker_customizer_assets) can toggle the
                // icon-picker vs. image-upload controls below.
                aaapos_nav_style_groups($style_setting_id, $item->ID);

                $wp_customize->add_setting($icon_setting_id, array(
                    'default'           => '',
                    'sanitize_callback' => 'aaapos_sanitize_nav_icon_key',
                    'transport'         => 'refresh',
                ));

                $wp_customize->add_control(new AAAPOS_Icon_Picker_Control($wp_customize, $icon_setting_id, array(
                    'label'    => $item->title,
                    'section'  => $section_id,
                    'priority' => $priority,
                )));

                // Custom image/GIF upload - attachment ID, stored separately
                // from the icon key above. Hidden by JS unless the icon
                // picker's "custom" swatch is selected.
                $wp_customize->add_setting($custom_setting_id, array(
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                    'transport'         => 'refresh',
                ));

                if (class_exists('WP_Customize_Media_Control')) {
                    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $custom_setting_id, array(
                        'label'     => __('Custom Icon Image / GIF', 'aaapos-prime'),
                        'section'   => $section_id,
                        'mime_type' => 'image',
                        'priority'  => $priority + 1,
                    )));
                }

                // Image Style upload - separate attachment ID from the
                // custom icon above, so switching a dropdown between Icon
                // Style and Image Style never clobbers the other style's
                // uploaded image. Shown/hidden by the Style control (JS in
                // aaapos_icon_picker_customizer_assets), same slot as the
                // custom icon control since the two are mutually exclusive.
                $wp_customize->add_setting($image_setting_id, array(
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                    'transport'         => 'refresh',
                ));

                if (class_exists('WP_Customize_Media_Control')) {
                    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $image_setting_id, array(
                        'label'     => __('Menu Item Image', 'aaapos-prime'),
                        'description' => __('Displayed larger than an icon.', 'aaapos-prime'),
                        'section'   => $section_id,
                        'mime_type' => 'image',
                        'priority'  => $priority + 1,
                    )));
                }

                $wp_customize->add_setting($desc_setting_id, array(
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                    'transport'         => 'refresh',
                ));

                $wp_customize->add_control($desc_setting_id, array(
                    'label'    => __('Description', 'aaapos-prime'),
                    'section'  => $section_id,
                    'type'     => 'text',
                    'priority' => $priority + 2,
                ));

                $priority += 10;
            }

            $section_priority += 10;
        }
    }
}
add_action('customize_register', 'aaapos_nav_menu_items_customizer');