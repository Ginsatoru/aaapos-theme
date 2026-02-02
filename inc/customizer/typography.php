<?php
/**
 * Typography Customizer Settings
 * 
 * Includes:
 * - Font family selector (Google Fonts + Web-safe)
 * - Custom font uploader (.woff, .woff2, .ttf)
 * - Font size controls
 * - Font weight scale
 * 
 * @package AAAPOS
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Typography Settings
 */
function aaapos_typography_customizer($wp_customize) {
    
    // =========================================================================
    // TYPOGRAPHY SECTION
    // =========================================================================
    $wp_customize->add_section('aaapos_typography', array(
        'title' => __('Typography & Fonts', 'aaapos'),
        'priority' => 25,
        'description' => __('Choose from popular fonts or upload your own custom fonts', 'aaapos'),
    ));

    // =========================================================================
    // HEADING FONT
    // =========================================================================
    $wp_customize->add_setting('heading_font_family', array(
        'default' => 'Montserrat',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('heading_font_family', array(
        'label' => __('Heading Font Family', 'aaapos'),
        'description' => __('Font used for all headings (H1-H6)', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'select',
        'choices' => aaapos_get_font_choices(),
        'priority' => 10,
    ));

    // Custom Heading Font Upload
    $wp_customize->add_setting('heading_font_custom_woff', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'heading_font_custom_woff', array(
        'label' => __('Custom Heading Font (WOFF/WOFF2)', 'aaapos'),
        'description' => __('Upload .woff or .woff2 file. This will override the font family above.', 'aaapos'),
        'section' => 'aaapos_typography',
        'mime_type' => 'application/font-woff',
        'priority' => 15,
    )));

    // Custom font family name for uploaded font
    $wp_customize->add_setting('heading_font_custom_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('heading_font_custom_name', array(
        'label' => __('Custom Heading Font Name', 'aaapos'),
        'description' => __('Enter the font family name (e.g., "My Custom Font")', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'text',
        'priority' => 16,
    ));

    // =========================================================================
    // BODY FONT
    // =========================================================================
    $wp_customize->add_setting('body_font_family', array(
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('body_font_family', array(
        'label' => __('Body Font Family', 'aaapos'),
        'description' => __('Font used for body text and paragraphs', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'select',
        'choices' => aaapos_get_font_choices(),
        'priority' => 20,
    ));

    // Custom Body Font Upload
    $wp_customize->add_setting('body_font_custom_woff', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'body_font_custom_woff', array(
        'label' => __('Custom Body Font (WOFF/WOFF2)', 'aaapos'),
        'description' => __('Upload .woff or .woff2 file. This will override the font family above.', 'aaapos'),
        'section' => 'aaapos_typography',
        'mime_type' => 'application/font-woff',
        'priority' => 25,
    )));

    $wp_customize->add_setting('body_font_custom_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('body_font_custom_name', array(
        'label' => __('Custom Body Font Name', 'aaapos'),
        'description' => __('Enter the font family name (e.g., "My Custom Font")', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'text',
        'priority' => 26,
    ));

    // =========================================================================
    // ACCENT FONT (Optional decorative font)
    // =========================================================================
    $wp_customize->add_setting('accent_font_family', array(
        'default' => 'Playfair Display',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('accent_font_family', array(
        'label' => __('Accent Font Family', 'aaapos'),
        'description' => __('Optional decorative font for special elements', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'select',
        'choices' => aaapos_get_font_choices(),
        'priority' => 30,
    ));

    // =========================================================================
    // FONT WEIGHT SCALE
    // =========================================================================
    $wp_customize->add_setting('font_weight_scale', array(
        'default' => 'normal',
        'sanitize_callback' => 'sanitize_key',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('font_weight_scale', array(
        'label' => __('Font Weight Scale', 'aaapos'),
        'description' => __('Overall font weight preference', 'aaapos'),
        'section' => 'aaapos_typography',
        'type' => 'select',
        'choices' => array(
            'light' => __('Light (300-500)', 'aaapos'),
            'normal' => __('Normal (400-600)', 'aaapos'),
            'bold' => __('Bold (500-700)', 'aaapos'),
        ),
        'priority' => 50,
    ));
}
add_action('customize_register', 'aaapos_typography_customizer');

/**
 * Get Available Font Choices
 * 
 * @return array Font choices for select dropdown
 */
function aaapos_get_font_choices() {
    return array(
        // System Fonts (Fast, no external requests)
        'system' => __('— System Fonts —', 'aaapos'),
        '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif' => __('System Default', 'aaapos'),
        'Arial, sans-serif' => 'Arial',
        'Helvetica, sans-serif' => 'Helvetica',
        'Georgia, serif' => 'Georgia',
        'Times New Roman, serif' => 'Times New Roman',
        'Courier New, monospace' => 'Courier New',
        'Verdana, sans-serif' => 'Verdana',
        'Trebuchet MS, sans-serif' => 'Trebuchet MS',
        
        // Google Fonts - Sans Serif
        'google_sans' => __('— Google Fonts (Sans Serif) —', 'aaapos'),
        'Inter' => 'Inter',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Lato' => 'Lato',
        'Montserrat' => 'Montserrat',
        'Poppins' => 'Poppins',
        'Raleway' => 'Raleway',
        'Nunito' => 'Nunito',
        'Work Sans' => 'Work Sans',
        'Ubuntu' => 'Ubuntu',
        'PT Sans' => 'PT Sans',
        'Source Sans Pro' => 'Source Sans Pro',
        'Noto Sans' => 'Noto Sans',
        
        // Google Fonts - Serif
        'google_serif' => __('— Google Fonts (Serif) —', 'aaapos'),
        'Playfair Display' => 'Playfair Display',
        'Merriweather' => 'Merriweather',
        'Lora' => 'Lora',
        'PT Serif' => 'PT Serif',
        'Crimson Text' => 'Crimson Text',
        'EB Garamond' => 'EB Garamond',
        'Libre Baskerville' => 'Libre Baskerville',
        'Bitter' => 'Bitter',
        
        // Google Fonts - Display
        'google_display' => __('— Google Fonts (Display) —', 'aaapos'),
        'Bebas Neue' => 'Bebas Neue',
        'Oswald' => 'Oswald',
        'Abril Fatface' => 'Abril Fatface',
        'Pacifico' => 'Pacifico',
        'Comfortaa' => 'Comfortaa',
        'Righteous' => 'Righteous',
    );
}

/**
 * Sanitize Decimal Values
 */
function aaapos_sanitize_decimal($value) {
    return floatval($value);
}

/**
 * Output Typography CSS
 */
function aaapos_typography_css_output() {
    // Get font selections
    $heading_font = get_theme_mod('heading_font_family', 'Montserrat');
    $body_font = get_theme_mod('body_font_family', 'Inter');
    $accent_font = get_theme_mod('accent_font_family', 'Playfair Display');
    
    // Custom uploaded fonts
    $heading_custom_id = get_theme_mod('heading_font_custom_woff', '');
    $heading_custom_name = get_theme_mod('heading_font_custom_name', '');
    $body_custom_id = get_theme_mod('body_font_custom_woff', '');
    $body_custom_name = get_theme_mod('body_font_custom_name', '');
    
    // Font settings
    $base_size = get_theme_mod('base_font_size', 16);
    $weight_scale = get_theme_mod('font_weight_scale', 'normal');
    $line_height = get_theme_mod('body_line_height', 1.6);
    $letter_spacing = get_theme_mod('heading_letter_spacing', 0);
    
    // Font weight mappings
    $weights = array(
        'light' => array('normal' => 300, 'medium' => 400, 'bold' => 500),
        'normal' => array('normal' => 400, 'medium' => 500, 'bold' => 600),
        'bold' => array('normal' => 500, 'medium' => 600, 'bold' => 700),
    );
    $current_weights = $weights[$weight_scale];
    
    ?>
    <style id="aaapos-typography-css" type="text/css">
        <?php
        // Load custom heading font
        if ($heading_custom_id && $heading_custom_name) {
            $font_url = wp_get_attachment_url($heading_custom_id);
            if ($font_url) {
                $font_format = aaapos_get_font_format($font_url);
                echo "@font-face {\n";
                echo "  font-family: '" . esc_attr($heading_custom_name) . "';\n";
                echo "  src: url('" . esc_url($font_url) . "') format('" . esc_attr($font_format) . "');\n";
                echo "  font-weight: 300 900;\n";
                echo "  font-display: swap;\n";
                echo "}\n\n";
                $heading_font = $heading_custom_name;
            }
        }
        
        // Load custom body font
        if ($body_custom_id && $body_custom_name) {
            $font_url = wp_get_attachment_url($body_custom_id);
            if ($font_url) {
                $font_format = aaapos_get_font_format($font_url);
                echo "@font-face {\n";
                echo "  font-family: '" . esc_attr($body_custom_name) . "';\n";
                echo "  src: url('" . esc_url($font_url) . "') format('" . esc_attr($font_format) . "');\n";
                echo "  font-weight: 300 900;\n";
                echo "  font-display: swap;\n";
                echo "}\n\n";
                $body_font = $body_custom_name;
            }
        }
        ?>
        
        :root {
            /* Font Families */
            --font-heading: <?php echo aaapos_get_font_stack($heading_font); ?>;
            --font-body: <?php echo aaapos_get_font_stack($body_font); ?>;
            --font-accent: <?php echo aaapos_get_font_stack($accent_font); ?>;
            
            /* Font Sizes */
            --text-base: <?php echo esc_attr($base_size); ?>px;
            
            /* Font Weights */
            --font-normal: <?php echo esc_attr($current_weights['normal']); ?>;
            --font-medium: <?php echo esc_attr($current_weights['medium']); ?>;
            --font-bold: <?php echo esc_attr($current_weights['bold']); ?>;
            
            /* Line Height */
            --body-line-height: <?php echo esc_attr($line_height); ?>;
            
            /* Letter Spacing */
            --heading-letter-spacing: <?php echo esc_attr($letter_spacing); ?>em;
        }
        
        /* Apply Typography */
        body {
            font-family: var(--font-body);
            font-size: var(--text-base);
            line-height: var(--body-line-height);
            font-weight: var(--font-normal);
        }
        
        h1, h2, h3, h4, h5, h6,
        .site-title,
        .entry-title,
        .widget-title {
            font-family: var(--font-heading);
            letter-spacing: var(--heading-letter-spacing);
        }
        
        .accent-font,
        .site-tagline,
        .hero-subtitle,
        .testimonial-quote {
            font-family: var(--font-accent);
        }
    </style>
    <?php
}
add_action('wp_head', 'aaapos_typography_css_output', 101);

/**
 * Get Font Stack with Fallbacks
 */
function aaapos_get_font_stack($font) {
    // If it already contains commas, it's a full stack
    if (strpos($font, ',') !== false) {
        return $font;
    }
    
    // System fonts
    if ($font === '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif') {
        return $font;
    }
    
    // Add appropriate fallbacks
    $serif_fonts = array('Georgia', 'Times New Roman', 'Playfair Display', 'Merriweather', 'Lora', 'PT Serif', 'Crimson Text', 'EB Garamond', 'Libre Baskerville', 'Bitter');
    $mono_fonts = array('Courier New');
    
    if (in_array($font, $serif_fonts)) {
        return "'{$font}', Georgia, serif";
    } elseif (in_array($font, $mono_fonts)) {
        return "'{$font}', monospace";
    } else {
        return "'{$font}', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
    }
}

/**
 * Determine Font Format from File Extension
 */
function aaapos_get_font_format($url) {
    $extension = pathinfo($url, PATHINFO_EXTENSION);
    
    switch (strtolower($extension)) {
        case 'woff2':
            return 'woff2';
        case 'woff':
            return 'woff';
        case 'ttf':
            return 'truetype';
        case 'otf':
            return 'opentype';
        case 'eot':
            return 'embedded-opentype';
        default:
            return 'woff2';
    }
}