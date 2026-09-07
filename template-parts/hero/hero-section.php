<?php
/**
 * Hero Section - dispatcher
 *
 * Renders one of two selectable designs, based on the "Hero Design"
 * toggle in Customizer > Hero Section:
 *   - modern  : template-parts/hero/hero-modern-section.php (default)
 *   - classic : template-parts/hero/hero-classic-section.php
 *
 * This file only holds what both designs share: the master "Show Hero
 * Section" check, the two small helper functions, and the require that
 * pulls in whichever design's markup is active (same pattern as
 * hero.php + hero-modern.php + hero-classic.php in inc/customizer/).
 */

// Master on/off switch for the whole hero section (both designs).
if (!get_theme_mod('show_hero', true)) {
    return;
}

// Which design to render.
$hero_layout_style = get_theme_mod('hero_layout_style', 'modern');

// Resolves an attachment ID to a URL, falling back safely if the ID is
// empty OR if wp_get_attachment_image_url() fails (e.g. deleted media).
if (!function_exists('aaapos_resolve_media_url')) {
    function aaapos_resolve_media_url($attachment_id, $fallback_url) {
        if ($attachment_id) {
            $url = wp_get_attachment_image_url($attachment_id, 'full');
            if ($url) {
                return $url;
            }
        }
        return $fallback_url;
    }
}

// Word-by-word ascend reveal helper (used by the Modern design).
if (!function_exists('aaapos_ascend_words')) {
    function aaapos_ascend_words($text, $base_delay = 0, $step = 0.045) {
        $words = preg_split('/\s+/', trim($text));
        $out = array();
        foreach ($words as $i => $word) {
            $delay = $base_delay + ($i * $step);
            $out[] = '<span class="ascend-word" style="--ascend-delay: ' . esc_attr(number_format($delay, 3)) . 's;"><span class="ascend-inner">' . esc_html($word) . '</span></span>';
        }
        return implode(' ', $out);
    }
}

if ($hero_layout_style === 'classic') {
    require get_template_directory() . '/template-parts/hero/hero-classic-section.php';
} else {
    require get_template_directory() . '/template-parts/hero/hero-modern-section.php';
}