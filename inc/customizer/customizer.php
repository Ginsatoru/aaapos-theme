<?php
/**
 * Theme Customizer - Simplified Brand Color System
 * 
 * Outputs CSS variables from a single brand color.
 * Automatically calculates hover, dark, and light variants.
 * customizer.php
 * 
 * @package aaapos-prime
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output Dynamic CSS from Brand Color
 * 
 * Takes ONE brand color and generates all needed variants:
 * - brand-color (the chosen color)
 * - brand-color-hover (15% darker)
 * - brand-color-dark (25% darker)
 * - brand-color-light (40% lighter)
 * - brand-color-rgb (for rgba usage)
 */
function aaapos_customizer_css_output() {
    // Get the single brand color
    $brand_color = get_theme_mod('brand_color', '#0ea5e9');
    $nav_accent  = get_theme_mod('nav_accent_color', '#D4AF37');
    
    // Auto-calculate variants
    $brand_hover = aaapos_adjust_brightness($brand_color, -15);
    $brand_dark  = aaapos_adjust_brightness($brand_color, -25);
    $brand_light = aaapos_adjust_brightness($brand_color, 40);
    $brand_rgb   = aaapos_hex_to_rgb($brand_color);
    ?>
    <style id="aaapos-customizer-css" type="text/css">
        :root {
            /* =================================
               BRAND COLOR (Auto-generated variants)
               ================================= */
            --brand-color: <?php echo esc_attr($brand_color); ?>;
            --brand-color-hover: <?php echo esc_attr($brand_hover); ?>;
            --brand-color-dark: <?php echo esc_attr($brand_dark); ?>;
            --brand-color-light: <?php echo esc_attr($brand_light); ?>;
            --brand-color-rgb: <?php echo esc_attr($brand_rgb); ?>;

            /* =================================
               MAPPED TO EXISTING VARIABLES
               (For backwards compatibility)
               ================================= */
            
            /* Primary colors - all use brand color */
            --color-primary: var(--brand-color);
            --color-primary-hover: var(--brand-color-hover);
            --color-primary-dark: var(--brand-color-dark);
            --color-primary-light: var(--brand-color-light);
            --color-primary-rgb: var(--brand-color-rgb);
            
            /* Secondary uses brand color too */
            --color-secondary: var(--brand-color);
            --color-secondary-hover: var(--brand-color-hover);
            --color-secondary-rgb: var(--brand-color-rgb);
            
            /* Accent uses brand color */
            --color-accent: var(--brand-color);
            --color-accent-hover: var(--brand-color-hover);
            --color-accent-rgb: var(--brand-color-rgb);

            /* Links use brand color */
            --color-link: var(--brand-color);
            --color-link-hover: var(--brand-color-hover);

            /* Buttons use brand color */
            --color-button-primary-bg: var(--brand-color);
            --color-button-primary-text: #ffffff;
            --color-button-primary-hover-bg: var(--brand-color-hover);
            --color-button-secondary-bg: var(--brand-color);
            --color-button-secondary-text: #ffffff;

            /* Focus ring uses brand color */
            --color-focus-ring: var(--brand-color);

            /* Header elements using brand color */
            --color-topbar-bg: var(--brand-color);
            --color-topbar-bg-end: var(--brand-color-hover);
            --color-topbar-text: #ffffff;
            --color-cart-badge: var(--brand-color);

            /* Navigation accent (separate control) */
            --color-nav-hover: <?php echo esc_attr($nav_accent); ?>;
            --color-nav-active: <?php echo esc_attr($nav_accent); ?>;
            --color-nav-underline: <?php echo esc_attr($nav_accent); ?>;

            /* WooCommerce uses brand color */
            --color-woo-price: var(--brand-color);
            --color-woo-add-to-cart-bg: var(--brand-color);
            --color-woo-add-to-cart-text: #ffffff;
            --color-woo-add-to-cart-hover: var(--brand-color-hover);

            /* Gradients using brand color */
            --gradient-primary: linear-gradient(90deg, var(--brand-color-dark), var(--brand-color), var(--brand-color-hover));
            --gradient-topbar: linear-gradient(90deg, var(--brand-color), var(--brand-color-hover));
            --gradient-button: linear-gradient(135deg, var(--brand-color), var(--brand-color-hover));
            --gradient-hero: linear-gradient(135deg, rgba(var(--brand-color-rgb), 0.95), rgba(var(--brand-color-rgb), 0.8));

            /* =================================
               FIXED COLORS (Not affected by brand)
               ================================= */
            
            /* Text colors - always neutral */
            --color-text: #374151;
            --color-text-light: #6b7280;
            --color-text-lighter: #9ca3af;
            --color-text-dark: #1f2937;
            --color-text-inverse: #ffffff;
            
            /* Background colors - always neutral */
            --color-bg-primary: #ffffff;
            --color-bg-secondary: #f9fafb;
            --color-bg-tertiary: #f3f4f6;
            --color-bg-dark: #1f2937;
            
            /* Legacy background aliases */
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;
            
            /* Base colors */
            --color-white: #ffffff;
            --color-black: #000000;
            --color-dark: #1f2937;
            --color-light: #f9fafb;

            /* Border colors - always neutral */
            --color-border: #e5e7eb;
            --color-border-light: #f3f4f6;
            --color-border-dark: #d1d5db;
            --border-color: #e5e7eb;
            --border-color-light: #f3f4f6;
            --border-color-dark: #d1d5db;

            /* Status colors - fixed for meaning */
            --color-success: #10b981;
            --color-success-light: #d1fae5;
            --color-warning: #f59e0b;
            --color-warning-light: #fef3c7;
            --color-danger: #ef4444;
            --color-danger-light: #fee2e2;
            --color-info: #3b82f6;
            --color-info-light: #dbeafe;

            /* Header - fixed neutral */
            --color-header-bg: #ffffff;
            --color-header-text: #374151;
            --color-nav-link: #374151;

            /* Footer - fixed dark */
            --color-footer-bg: #1f2937;
            --color-footer-text: #9ca3af;
            --color-footer-heading: #ffffff;
            --color-footer-link: #9ca3af;
            --color-footer-link-hover: #ffffff;
            --color-footer-border: #374151;

            /* WooCommerce - fixed colors */
            --color-woo-sale-price: #ef4444;
            --color-woo-sale-badge: #ef4444;
            --color-woo-sale-badge-text: #ffffff;
            --color-woo-rating: #fbbf24;
            --color-woo-in-stock: #10b981;
            --color-woo-out-of-stock: #ef4444;
            --woo-sale-badge: #ef4444;
            --woo-rating-color: #fbbf24;

            /* Gray scale */
            --color-gray-50: #f9fafb;
            --color-gray-100: #f3f4f6;
            --color-gray-200: #e5e7eb;
            --color-gray-300: #d1d5db;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-gray-700: #374151;
            --color-gray-800: #1f2937;
            --color-gray-900: #111827;
        }
    </style>
    <?php
}
add_action('wp_head', 'aaapos_customizer_css_output', 100);

/**
 * Output Customizer CSS in Block Editor
 */
function aaapos_editor_customizer_css() {
    ob_start();
    aaapos_customizer_css_output();
    $css = ob_get_clean();
    $css = str_replace(['<style id="aaapos-customizer-css" type="text/css">', '</style>'], '', $css);
    wp_add_inline_style('aaapos-editor-style', $css);
}
add_action('enqueue_block_editor_assets', 'aaapos_editor_customizer_css', 100);

/**
 * Convert Hex Color to RGB String
 */
if (!function_exists('aaapos_hex_to_rgb')) {
    function aaapos_hex_to_rgb($hex) {
        $hex = str_replace('#', '', $hex);
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "{$r}, {$g}, {$b}";
    }
}

/**
 * Adjust Color Brightness
 * 
 * @param string $hex     Hex color code
 * @param int    $percent Brightness adjustment (-100 to 100)
 * @return string Adjusted hex color
 */
if (!function_exists('aaapos_adjust_brightness')) {
    function aaapos_adjust_brightness($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Adjust brightness
        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));
        
        return '#' . str_pad(dechex((int)$r), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex((int)$g), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex((int)$b), 2, '0', STR_PAD_LEFT);
    }
}

/**
 * Legacy function compatibility
 */
if (!function_exists('mr_adjust_brightness')) {
    function mr_adjust_brightness($hex, $percent) {
        return aaapos_adjust_brightness($hex, $percent);
    }
}