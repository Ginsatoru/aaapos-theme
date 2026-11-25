<?php
/**
 * Top Progress Bar
 *
 * Thin loading bar at the very top of every front-end page (page load,
 * refresh and navigating away). CSS + JS are inlined at the top of <head>
 * so the bar shows before any other asset has loaded.
 *
 * Customizer: Appearance > Customize > Top Loading Bar
 *   - top_progress_enable (on/off, default on)
 *   - top_progress_color  (empty = light shade of the brand colour)
 *
 * Source files:
 *   assets/css/components/top-progress.css
 *   assets/js/core/top-progress.js
 *
 * @package aaapos-prime
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customizer settings
 */
function aaapos_top_progress_customizer($wp_customize)
{
    $wp_customize->add_section('aaapos_top_progress', array(
        'title'    => __('Top Loading Bar', 'aaapos-prime'),
        'priority' => 45,
    ));

    $wp_customize->add_setting('top_progress_enable', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('top_progress_enable', array(
        'label'   => __('Show Top Loading Bar', 'aaapos-prime'),
        'section' => 'aaapos_top_progress',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('top_progress_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'top_progress_color', array(
        'label'           => __('Bar Colour', 'aaapos-prime'),
        'section'         => 'aaapos_top_progress',
        'active_callback' => function () {
            return (bool) get_theme_mod('top_progress_enable', true);
        },
    )));
}
add_action('customize_register', 'aaapos_top_progress_customizer');

/**
 * Output the bar (inline, top of <head>)
 */
function aaapos_output_top_progress_bar()
{
    if (!get_theme_mod('top_progress_enable', true)) {
        return;
    }

    $css_file = get_template_directory() . '/assets/css/components/top-progress.css';
    $js_file  = get_template_directory() . '/assets/js/core/top-progress.js';

    if (file_exists($css_file)) {
        $css   = file_get_contents($css_file);
        $color = sanitize_hex_color(get_theme_mod('top_progress_color', ''));
        if ($color) {
            // Custom colour from the Customizer overrides the default
            $css .= "\n:root { --top-progress-color: " . $color . "; }";
        }
        echo "<style id=\"aaapos-top-progress-css\">\n" . $css . "\n</style>\n";
    }

    if (file_exists($js_file)) {
        echo "<script id=\"aaapos-top-progress-js\">\n" . file_get_contents($js_file) . "\n</script>\n";
    }
}
add_action('wp_head', 'aaapos_output_top_progress_bar', 1);
