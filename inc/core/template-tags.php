<?php
/**
 * Custom template tags for this theme (template-tags.php)
 */

if (!function_exists("mr_posted_on")):
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function mr_posted_on()
    {
        $time_string =
            '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time("U") !== get_the_modified_time("U")) {
            $time_string =
                '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date()),
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x("Posted on %s", "post date", "macedon-ranges"),
            '<a href="' .
                esc_url(get_permalink()) .
                '" rel="bookmark">' .
                $time_string .
                "</a>",
        );

        echo '<span class="posted-on">' . $posted_on . "</span>";
    }
endif;

if (!function_exists("mr_posted_by")):
    /**
     * Prints HTML with meta information for the current author.
     */
    function mr_posted_by()
    {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x("by %s", "post author", "macedon-ranges"),
            '<span class="author vcard"><a class="url fn n" href="' .
                esc_url(get_author_posts_url(get_the_author_meta("ID"))) .
                '">' .
                esc_html(get_the_author()) .
                "</a></span>",
        );

        echo '<span class="byline"> ' . $byline . "</span>";
    }
endif;

if (!function_exists("mr_entry_footer")):
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function mr_entry_footer()
    {
        // Hide category and tag text for pages.
        if ("post" === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(
                esc_html__(", ", "macedon-ranges"),
            );
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf(
                    '<span class="cat-links">' .
                        esc_html__('Posted in %1$s', "macedon-ranges") .
                        "</span>",
                    $categories_list,
                );
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list(
                "",
                esc_html_x(", ", "list item separator", "macedon-ranges"),
            );
            if ($tags_list) {
                /* translators: 1: list of tags. */
                printf(
                    '<span class="tags-links">' .
                        esc_html__('Tagged %1$s', "macedon-ranges") .
                        "</span>",
                    $tags_list,
                );
            }
        }

        if (
            !is_single() &&
            !post_password_required() &&
            (comments_open() || get_comments_number())
        ) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __(
                            'Leave a Comment<span class="screen-reader-text"> on %s</span>',
                            "macedon-ranges",
                        ),
                        [
                            "span" => [
                                "class" => [],
                            ],
                        ],
                    ),
                    wp_kses_post(get_the_title()),
                ),
            );
            echo "</span>";
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __(
                        'Edit <span class="screen-reader-text">%s</span>',
                        "macedon-ranges",
                    ),
                    [
                        "span" => [
                            "class" => [],
                        ],
                    ],
                ),
                wp_kses_post(get_the_title()),
            ),
            '<span class="edit-link">',
            "</span>",
        );
    }
endif;

if (!function_exists("mr_post_thumbnail")):
    /**
     * Displays an optional post thumbnail.
     */
    function mr_post_thumbnail($size = "post-thumbnail")
    {
        if (
            post_password_required() ||
            is_attachment() ||
            !has_post_thumbnail()
        ) {
            return;
        }

        if (is_singular()): ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail($size); ?>
            </div>
        <?php else: ?>
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail($size, [
                    "alt" => the_title_attribute([
                        "echo" => false,
                    ]),
                ]); ?>
            </a>
        <?php endif;
    }
endif;

if (!function_exists("mr_the_posts_navigation")):
    /**
     * Custom posts navigation
     */
    function mr_the_posts_navigation()
    {
        the_posts_pagination([
            "mid_size" => 2,
            "prev_text" => sprintf(
                '%s <span class="nav-prev-text">%s</span>',
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 010 .708L5.707 8l5.647 5.646a.5.5 0 01-.708.708l-6-6a.5.5 0 010-.708l6-6a.5.5 0 01.708 0z" clip-rule="evenodd"/></svg>',
                esc_html__("Newer", "macedon-ranges"),
            ),
            "next_text" => sprintf(
                '<span class="nav-next-text">%s</span> %s',
                esc_html__("Older", "macedon-ranges"),
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z" clip-rule="evenodd"/></svg>',
            ),
        ]);
    }
endif;

/**
 * Get social links
 */
function mr_get_social_links()
{
    $social_platforms = [
        "facebook" => [
            "name" => "Facebook",
            "icon" =>
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/></svg>',
        ],
        "instagram" => [
            "name" => "Instagram",
            "icon" =>
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.416.198-.51.333-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.174-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/></svg>',
        ],
        "twitter" => [
            "name" => "Twitter",
            "icon" =>
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0016 3.542a6.658 6.658 0 01-1.889.518 3.301 3.301 0 001.447-1.817 6.533 6.533 0 01-2.087.793A3.286 3.286 0 007.875 6.03a9.325 9.325 0 01-6.767-3.429 3.289 3.289 0 001.018 4.382A3.323 3.323 0 01.64 6.575v.045a3.288 3.288 0 002.632 3.218 3.203 3.203 0 01-.865.115 3.23 3.23 0 01-.614-.057 3.283 3.283 0 003.067 2.277A6.588 6.588 0 01.78 13.58a6.32 6.32 0 01-.78-.045A9.344 9.344 0 005.026 15z"/></svg>',
        ],
    ];

    $output = '<div class="social-links">';

    foreach ($social_platforms as $platform => $data) {
        $url = get_theme_mod($platform . "_url");
        if ($url) {
            $output .= sprintf(
                '<a href="%s" class="social-link" target="_blank" rel="noopener" aria-label="%s">%s</a>',
                esc_url($url),
                esc_attr($data["name"]),
                $data["icon"],
            );
        }
    }

    $output .= "</div>";

    return $output;
}

/**
 * Get copyright text
 */
function mr_get_copyright_text()
{
    $copyright = get_theme_mod(
        "copyright_text",
        "&copy; {year} {sitename}. All rights reserved.",
    );

    $copyright = str_replace(
        ["{year}", "{sitename}"],
        [date("Y"), get_bloginfo("name")],
        $copyright,
    );

    return wp_kses_post($copyright);
}

/**
 * Display breadcrumbs
 */
function mr_breadcrumbs()
{
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumb" aria-label="Breadcrumb">';
    echo '<a href="' .
        esc_url(home_url("/")) .
        '">' .
        __("Home", "macedon-ranges") .
        "</a>";
    echo '<span class="breadcrumb-separator">/</span>';

    if (is_category() || is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            echo '<a href="' .
                esc_url(get_category_link($categories[0]->term_id)) .
                '">' .
                esc_html($categories[0]->name) .
                "</a>";
            echo '<span class="breadcrumb-separator">/</span>';
        }

        if (is_single()) {
            the_title('<span class="breadcrumb-current">', "</span>");
        }
    } elseif (is_page()) {
        the_title('<span class="breadcrumb-current">', "</span>");
    } elseif (is_search()) {
        echo '<span class="breadcrumb-current">' .
            __("Search Results", "macedon-ranges") .
            "</span>";
    }

    echo "</nav>";
}
