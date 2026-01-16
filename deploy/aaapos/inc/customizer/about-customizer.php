<?php
/**
 * About Page Customizer Settings
 * Complete customization options with image uploads and show/hide controls
 *
 * @package AAAPOS
 */

function aaapos_about_page_customizer($wp_customize)
{
    // ===================================
    // ABOUT PAGE SECTION
    // ===================================
    $wp_customize->add_section("aaapos_about_page", [
        "title" => __("About Page", "aaapos-prime"),
        "priority" => 151,
    ]);

    // ===================================
    // HERO SECTION
    // ===================================

    // Show/Hide Hero Section
    $wp_customize->add_setting("about_show_hero", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_hero", [
        "label" => __("Show Hero Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 5,
    ]);

    // Hero Image Upload
    $wp_customize->add_setting("about_hero_image", [
        "default" => "",
        "sanitize_callback" => "esc_url_raw",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control(
        new WP_Customize_Image_Control($wp_customize, "about_hero_image", [
            "label" => __("Hero Image", "aaapos-prime"),
            "description" => __(
                "Upload a custom hero image (recommended: 800x600px)",
                "aaapos-prime",
            ),
            "section" => "aaapos_about_page",
            "priority" => 8,
        ]),
    );

    // Hero Title
    $wp_customize->add_setting("about_hero_title", [
        "default" => "Built on Trust, Driven by Quality",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_hero_title", [
        "label" => __("Hero Title", "aaapos-prime"),
        "description" => __(
            'Use comma to split title for styling (e.g., "Part One, Part Two")',
            "aaapos-prime",
        ),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 20,
    ]);

    // Hero Intro
    $wp_customize->add_setting("about_hero_intro", [
        "default" =>
            'For over a decade, we\'ve been more than just a supplier—we\'re your partner in providing the very best for your animals.',
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_hero_intro", [
        "label" => __("Hero Introduction", "aaapos-prime"),
        "description" => __("Leave empty to hide intro text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "textarea",
        "priority" => 30,
    ]);

    // Show/Hide Hero Meta
    $wp_customize->add_setting("about_show_hero_meta", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_hero_meta", [
        "label" => __("Show Hero Meta Information", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 35,
    ]);

    // Since Year
    $wp_customize->add_setting("about_since_year", [
        "default" => "2013",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_since_year", [
        "label" => __("Since Year", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 40,
    ]);

    // Meta Item 1 Title
    $wp_customize->add_setting("about_meta1_title", [
        "default" => "Family Owned",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_meta1_title", [
        "label" => __("Meta Item 1 - Title", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 45,
    ]);

    // Meta Item 1 Text
    $wp_customize->add_setting("about_meta1_text", [
        "default" => "Local & trusted",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_meta1_text", [
        "label" => __("Meta Item 1 - Text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 46,
    ]);

    // ===================================
    // STORY SECTION
    // ===================================

    // Show/Hide Story Section
    $wp_customize->add_setting("about_show_story", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_story", [
        "label" => __("Show Story Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 55,
    ]);

    // Story Section Heading
    $wp_customize->add_setting("about_story_heading", [
        "default" => "Our Story",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_story_heading", [
        "label" => __("Story Section Heading", "aaapos-prime"),
        "description" => __("Leave empty to hide heading", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 60,
    ]);

    // Show/Hide Quote
    $wp_customize->add_setting("about_show_quote", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_quote", [
        "label" => __("Show Featured Quote", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 65,
    ]);

    // Quote Text
    $wp_customize->add_setting("about_quote", [
        "default" =>
            "We started with a simple belief: every animal deserves the best. That belief still guides us today.",
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_quote", [
        "label" => __("Featured Quote Text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "textarea",
        "priority" => 70,
    ]);

    // ===================================
    // VALUES SECTION
    // ===================================

    // Show/Hide Values Section
    $wp_customize->add_setting("about_show_values", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_values", [
        "label" => __("Show Values Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 75,
    ]);

    // Values Section Title
    $wp_customize->add_setting("about_values_title", [
        "default" => "What Drives Us",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_values_title", [
        "label" => __("Values Section Title", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 80,
    ]);

    // Values Section Subtitle
    $wp_customize->add_setting("about_values_subtitle", [
        "default" => "The principles that guide everything we do",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_values_subtitle", [
        "label" => __("Values Section Subtitle", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 90,
    ]);

    // Value Cards (1-4)
    $value_defaults = [
        1 => [
            "title" => "Quality First",
            "text" =>
                "We source only the finest products and maintain the highest standards in everything we deliver. No compromises.",
        ],
        2 => [
            "title" => "Customer Focus",
            "text" =>
                "Your satisfaction drives us. We listen, adapt, and continuously improve based on your needs.",
        ],
        3 => [
            "title" => "Sustainability",
            "text" =>
                "Committed to environmental responsibility and supporting local communities for a better tomorrow.",
        ],
        4 => [
            "title" => "Reliability",
            "text" =>
                "Count on us for consistent service, timely delivery, and dependable support every single time.",
        ],
    ];

    for ($i = 1; $i <= 4; $i++) {
        $priority = 100 + $i * 10;

        // Show/Hide Value
        $wp_customize->add_setting("about_show_value{$i}", [
            "default" => true,
            "sanitize_callback" => "wp_validate_boolean",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_show_value{$i}", [
            "label" => sprintf(__("Show Value %d", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "checkbox",
            "priority" => $priority,
        ]);

        // Value Title
        $wp_customize->add_setting("about_value{$i}_title", [
            "default" => $value_defaults[$i]["title"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_value{$i}_title", [
            "label" => sprintf(__("Value %d - Title", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 1,
        ]);

        // Value Text
        $wp_customize->add_setting("about_value{$i}_text", [
            "default" => $value_defaults[$i]["text"],
            "sanitize_callback" => "sanitize_textarea_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_value{$i}_text", [
            "label" => sprintf(
                __("Value %d - Description", "aaapos-prime"),
                $i,
            ),
            "section" => "aaapos_about_page",
            "type" => "textarea",
            "priority" => $priority + 2,
        ]);
    }

    // ===================================
    // TIMELINE SECTION
    // ===================================

    // Show/Hide Timeline Section
    $wp_customize->add_setting("about_show_timeline", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_timeline", [
        "label" => __("Show Timeline Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 200,
    ]);

    // Timeline Section Title
    $wp_customize->add_setting("about_timeline_title", [
        "default" => "Our Journey",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_timeline_title", [
        "label" => __("Timeline Section Title", "aaapos-prime"),
        "description" => __("Leave empty to hide title", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 201,
    ]);

    // Timeline Items (1-4)
    $timeline_defaults = [
        1 => [
            "year" => "2013",
            "title" => "The Beginning",
            "text" =>
                "Started as a small family operation with a vision to provide quality animal supplies locally.",
        ],
        2 => [
            "year" => "2016",
            "title" => "Expansion",
            "text" =>
                "Moved to a larger facility and expanded our product range to serve more animals.",
        ],
        3 => [
            "year" => "2019",
            "title" => "1,000 Customers",
            "text" =>
                "Celebrated serving over 1,000 satisfied customers across the region.",
        ],
        4 => [
            "year" => "2023",
            "title" => "Going Digital",
            "text" =>
                "Launched our online store to serve you better, anytime, anywhere.",
        ],
    ];

    for ($i = 1; $i <= 4; $i++) {
        $priority = 210 + $i * 10;

        // Show/Hide Timeline Item
        $wp_customize->add_setting("about_show_timeline{$i}", [
            "default" => true,
            "sanitize_callback" => "wp_validate_boolean",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_show_timeline{$i}", [
            "label" => sprintf(__("Show Timeline Item %d", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "checkbox",
            "priority" => $priority,
        ]);

        // Timeline Year
        $wp_customize->add_setting("about_timeline{$i}_year", [
            "default" => $timeline_defaults[$i]["year"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_timeline{$i}_year", [
            "label" => sprintf(__("Timeline %d - Year", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 1,
        ]);

        // Timeline Title
        $wp_customize->add_setting("about_timeline{$i}_title", [
            "default" => $timeline_defaults[$i]["title"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_timeline{$i}_title", [
            "label" => sprintf(__("Timeline %d - Title", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 2,
        ]);

        // Timeline Text
        $wp_customize->add_setting("about_timeline{$i}_text", [
            "default" => $timeline_defaults[$i]["text"],
            "sanitize_callback" => "sanitize_textarea_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_timeline{$i}_text", [
            "label" => sprintf(
                __("Timeline %d - Description", "aaapos-prime"),
                $i,
            ),
            "section" => "aaapos_about_page",
            "type" => "textarea",
            "priority" => $priority + 3,
        ]);
    }

    // ===================================
    // TEAM SECTION
    // ===================================

    // Show/Hide Team Section
    $wp_customize->add_setting("about_show_team", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_team", [
        "label" => __("Show Team Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 300,
    ]);

    // Team Section Title
    $wp_customize->add_setting("about_team_title", [
        "default" => "Meet the People Behind the Promise",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_team_title", [
        "label" => __("Team Section Title", "aaapos-prime"),
        "description" => __("Leave empty to hide title", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 301,
    ]);

    // Team Section Subtitle
    $wp_customize->add_setting("about_team_subtitle", [
        "default" =>
            "The passionate team dedicated to serving you and your animals",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_team_subtitle", [
        "label" => __("Team Section Subtitle", "aaapos-prime"),
        "description" => __("Leave empty to hide subtitle", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 302,
    ]);

    // Team Members (1-3)
    $team_defaults = [
        1 => [
            "name" => "John Smith",
            "role" => "Founder & CEO",
            "quote" => "Every animal deserves the very best care.",
        ],
        2 => [
            "name" => "Sarah Johnson",
            "role" => "Operations Manager",
            "quote" => "Smooth operations, happy customers.",
        ],
        3 => [
            "name" => "Michael Chen",
            "role" => "Head of Quality",
            "quote" => 'Quality isn\'t negotiable, it\'s essential.',
        ],
    ];

    for ($i = 1; $i <= 3; $i++) {
        $priority = 310 + $i * 10;

        // Show/Hide Team Member
        $wp_customize->add_setting("about_show_team{$i}", [
            "default" => true,
            "sanitize_callback" => "wp_validate_boolean",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_show_team{$i}", [
            "label" => sprintf(__("Show Team Member %d", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "checkbox",
            "priority" => $priority,
        ]);

        // Team Member Image
        $wp_customize->add_setting("about_team{$i}_image", [
            "default" => "",
            "sanitize_callback" => "esc_url_raw",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                "about_team{$i}_image",
                [
                    "label" => sprintf(
                        __("Team Member %d - Photo", "aaapos-prime"),
                        $i,
                    ),
                    "description" => __(
                        "Upload team member photo (recommended: 400x400px)",
                        "aaapos-prime",
                    ),
                    "section" => "aaapos_about_page",
                    "priority" => $priority + 1,
                ],
            ),
        );

        // Team Member Name
        $wp_customize->add_setting("about_team{$i}_name", [
            "default" => $team_defaults[$i]["name"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_team{$i}_name", [
            "label" => sprintf(__("Team Member %d - Name", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 2,
        ]);

        // Team Member Role
        $wp_customize->add_setting("about_team{$i}_role", [
            "default" => $team_defaults[$i]["role"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_team{$i}_role", [
            "label" => sprintf(__("Team Member %d - Role", "aaapos-prime"), $i),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 3,
        ]);

        // Team Member Quote
        $wp_customize->add_setting("about_team{$i}_quote", [
            "default" => $team_defaults[$i]["quote"],
            "sanitize_callback" => "sanitize_text_field",
            "transport" => "refresh",
        ]);
        $wp_customize->add_control("about_team{$i}_quote", [
            "label" => sprintf(
                __("Team Member %d - Quote", "aaapos-prime"),
                $i,
            ),
            "description" => __("Leave empty to hide quote", "aaapos-prime"),
            "section" => "aaapos_about_page",
            "type" => "text",
            "priority" => $priority + 4,
        ]);
    }

    // ===================================
    // CTA SECTION
    // ===================================

    // Show/Hide CTA Section
    $wp_customize->add_setting("about_show_cta", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_cta", [
        "label" => __("Show CTA Section", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 400,
    ]);

    // CTA Background Image
$wp_customize->add_setting("about_cta_bg_image", [
    "default" => "",
    "sanitize_callback" => "esc_url_raw",
    "transport" => "refresh",
]);
$wp_customize->add_control(
    new WP_Customize_Image_Control($wp_customize, "about_cta_bg_image", [
        "label" => __("CTA Background Image", "aaapos-prime"),
        "description" => __(
            "Upload a background image for the CTA section (recommended: 1920x600px). Falls back to ctabg.jpg if not set.",
            "aaapos-prime",
        ),
        "section" => "aaapos_about_page",
        "priority" => 405,
    ]),
);

// CTA Overlay Opacity
$wp_customize->add_setting("about_cta_overlay_opacity", [
    "default" => "0.6",
    "sanitize_callback" => "sanitize_text_field",
    "transport" => "refresh",
]);
$wp_customize->add_control("about_cta_overlay_opacity", [
    "label" => __("CTA Overlay Opacity", "aaapos-prime"),
    "description" => __("Black overlay opacity (0.0 to 1.0, default: 0.6)", "aaapos-prime"),
    "section" => "aaapos_about_page",
    "type" => "number",
    "input_attrs" => [
        "min" => "0",
        "max" => "1",
        "step" => "0.05",
    ],
    "priority" => 407,
]);

    // CTA Title
    $wp_customize->add_setting("about_cta_title", [
        "default" => "Ready to Experience the Difference?",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_cta_title", [
        "label" => __("CTA Title", "aaapos-prime"),
        "description" => __("Leave empty to hide title", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 410,
    ]);

    // CTA Text
    $wp_customize->add_setting("about_cta_text", [
        "default" =>
            "Join thousands of satisfied customers who trust us for quality products and exceptional service.",
        "sanitize_callback" => "sanitize_textarea_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_cta_text", [
        "label" => __("CTA Text", "aaapos-prime"),
        "description" => __("Leave empty to hide text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "textarea",
        "priority" => 420,
    ]);

    // Show/Hide Shop Button
    $wp_customize->add_setting("about_show_cta_shop_btn", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_cta_shop_btn", [
        "label" => __("Show Shop Now Button", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 425,
    ]);

    // Shop Button Text
    $wp_customize->add_setting("about_cta_shop_text", [
        "default" => "Shop Now",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_cta_shop_text", [
        "label" => __("Shop Button Text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 430,
    ]);

    // Show/Hide Contact Button
    $wp_customize->add_setting("about_show_cta_contact_btn", [
        "default" => true,
        "sanitize_callback" => "wp_validate_boolean",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_show_cta_contact_btn", [
        "label" => __("Show Contact Button", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "checkbox",
        "priority" => 435,
    ]);

    // Contact Button Text
    $wp_customize->add_setting("about_cta_contact_text", [
        "default" => "Get in Touch",
        "sanitize_callback" => "sanitize_text_field",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_cta_contact_text", [
        "label" => __("Contact Button Text", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "text",
        "priority" => 440,
    ]);

    // Contact Button URL
    $wp_customize->add_setting("about_cta_contact_url", [
        "default" => home_url("/contact"),
        "sanitize_callback" => "esc_url_raw",
        "transport" => "refresh",
    ]);
    $wp_customize->add_control("about_cta_contact_url", [
        "label" => __("Contact Button URL", "aaapos-prime"),
        "description" => __("Link for the contact button", "aaapos-prime"),
        "section" => "aaapos_about_page",
        "type" => "url",
        "priority" => 445,
    ]);
}
add_action("customize_register", "aaapos_about_page_customizer");
