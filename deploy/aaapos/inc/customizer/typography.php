<?php
/**
 * Forms customizer settings
 */
function mr_typography_customizer($wp_customize) {
    // Typography Section
    $wp_customize->add_section('mr_typography', array(
        'title' => __('Typography', 'macedon-ranges'),
        'priority' => 25,
    ));

    // Heading Font
    $wp_customize->add_setting('heading_font', array(
        'default' => 'Montserrat',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('heading_font', array(
        'label' => __('Heading Font', 'macedon-ranges'),
        'section' => 'mr_typography',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat',
            'Inter' => 'Inter',
            'Playfair Display' => 'Playfair Display',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
        ),
        'priority' => 10,
    ));

    // Body Font
    $wp_customize->add_setting('body_font', array(
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('body_font', array(
        'label' => __('Body Font', 'macedon-ranges'),
        'section' => 'mr_typography',
        'type' => 'select',
        'choices' => array(
            'Inter' => 'Inter',
            'Montserrat' => 'Montserrat',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
        ),
        'priority' => 20,
    ));

    // Base Font Size
    $wp_customize->add_setting('base_font_size', array(
        'default' => 16,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('base_font_size', array(
        'label' => __('Base Font Size (px)', 'macedon-ranges'),
        'section' => 'mr_typography',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 14,
            'max' => 20,
            'step' => 1,
        ),
        'priority' => 30,
    ));

    // Font Weight Scale
    $wp_customize->add_setting('font_weight_scale', array(
        'default' => 'normal',
        'sanitize_callback' => 'sanitize_key',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('font_weight_scale', array(
        'label' => __('Font Weight Scale', 'macedon-ranges'),
        'section' => 'mr_typography',
        'type' => 'select',
        'choices' => array(
            'light' => __('Light', 'macedon-ranges'),
            'normal' => __('Normal', 'macedon-ranges'),
            'bold' => __('Bold', 'macedon-ranges'),
        ),
        'priority' => 40,
    ));
}