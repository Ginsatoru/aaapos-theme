<?php
/**
 * Hero Section - Customizer settings (shared toggles + wiring)
 *
 * Two selectable hero designs, controlled by the "Hero Design" toggle:
 *   - modern  : controls defined in inc/customizer/hero-modern.php
 *   - classic : controls defined in inc/customizer/hero-classic.php
 *
 * This file only holds what both designs share: the master "Show Hero
 * Section" / "Hero Design" toggles, the active-callback/sanitize helper
 * functions, and the require_once calls that pull in each design's own
 * controls file (same pattern as hero.css + hero-classic.css).
 */

require_once get_template_directory() . '/inc/customizer/hero-modern.php';
require_once get_template_directory() . '/inc/customizer/hero-classic.php';

// Custom sanitization function for float values (overlay opacity slider).
if (!function_exists('mr_sanitize_float')) {
    function mr_sanitize_float($value) {
        return floatval($value);
    }
}

function mr_hero_customizer($wp_customize) {
    // Hero Section
    $wp_customize->add_section('mr_hero', array(
        'title' => __('Hero Section', 'macedon-ranges'),
        'priority' => 40,
    ));

    // =========================================================================
    // MASTER TOGGLES (apply to both designs)
    // =========================================================================

    // Show Hero Section
    $wp_customize->add_setting('show_hero', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('show_hero', array(
        'label' => __('Show Hero Section', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'checkbox',
        'priority' => 1,
    ));

    // Hero Design (which layout to render)
    $wp_customize->add_setting('hero_layout_style', array(
        'default' => 'modern',
        'sanitize_callback' => 'mr_hero_sanitize_layout_style',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('hero_layout_style', array(
        'label' => __('Hero Design', 'macedon-ranges'),
        'section' => 'mr_hero',
        'type' => 'select',
        'choices' => array(
            'modern' => __('Modern', 'macedon-ranges'),
            'classic' => __('Classic', 'macedon-ranges'),
        ),
        'priority' => 2,
    ));

    // Each design registers its own controls into the same 'mr_hero'
    // section - see inc/customizer/hero-modern.php and hero-classic.php.
    mr_hero_modern_controls($wp_customize);
    mr_hero_classic_controls($wp_customize);
}
add_action('customize_register', 'mr_hero_customizer');

/**
 * Active-callback / sanitize helpers for the Hero Design toggle.
 * Shared by both hero-modern.php and hero-classic.php.
 */
if (!function_exists('mr_hero_is_modern')) {
    function mr_hero_is_modern() {
        return get_theme_mod('hero_layout_style', 'modern') === 'modern';
    }
}

if (!function_exists('mr_hero_is_classic')) {
    function mr_hero_is_classic() {
        return get_theme_mod('hero_layout_style', 'modern') === 'classic';
    }
}

if (!function_exists('mr_hero_is_classic_video')) {
    function mr_hero_is_classic_video() {
        return mr_hero_is_classic() && get_theme_mod('hero_media_type', 'image') === 'video';
    }
}

if (!function_exists('mr_hero_is_classic_image')) {
    function mr_hero_is_classic_image() {
        return mr_hero_is_classic() && get_theme_mod('hero_media_type', 'image') !== 'video';
    }
}

if (!function_exists('mr_hero_sanitize_layout_style')) {
    function mr_hero_sanitize_layout_style($input) {
        return in_array($input, array('modern', 'classic'), true) ? $input : 'modern';
    }
}

if (!function_exists('mr_hero_sanitize_media_type')) {
    function mr_hero_sanitize_media_type($input) {
        return in_array($input, array('image', 'video'), true) ? $input : 'image';
    }
}