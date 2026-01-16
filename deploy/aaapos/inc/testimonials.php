<?php
/**
 * Testimonials Custom Post Type
 *
 * @package Macedon_Ranges
 */

// Exit if accessed directly
if (!defined("ABSPATH")) {
    exit();
}

/**
 * Register Testimonials Custom Post Type
 */
function register_testimonials_post_type()
{
    $labels = [
        "name" => _x("Testimonials", "Post Type General Name", "macedon-ranges"),
        "singular_name" => _x(
            "Testimonial",
            "Post Type Singular Name",
            "macedon-ranges"
        ),
        "menu_name" => __("Testimonials", "macedon-ranges"),
        "name_admin_bar" => __("Testimonial", "macedon-ranges"),
        "archives" => __("Testimonial Archives", "macedon-ranges"),
        "attributes" => __("Testimonial Attributes", "macedon-ranges"),
        "parent_item_colon" => __("Parent Testimonial:", "macedon-ranges"),
        "all_items" => __("All Testimonials", "macedon-ranges"),
        "add_new_item" => __("Add New Testimonial", "macedon-ranges"),
        "add_new" => __("Add New", "macedon-ranges"),
        "new_item" => __("New Testimonial", "macedon-ranges"),
        "edit_item" => __("Edit Testimonial", "macedon-ranges"),
        "update_item" => __("Update Testimonial", "macedon-ranges"),
        "view_item" => __("View Testimonial", "macedon-ranges"),
        "view_items" => __("View Testimonials", "macedon-ranges"),
        "search_items" => __("Search Testimonial", "macedon-ranges"),
        "not_found" => __("Not found", "macedon-ranges"),
        "not_found_in_trash" => __("Not found in Trash", "macedon-ranges"),
        "featured_image" => __("Customer Photo", "macedon-ranges"),
        "set_featured_image" => __("Set customer photo", "macedon-ranges"),
        "remove_featured_image" => __("Remove customer photo", "macedon-ranges"),
        "use_featured_image" => __("Use as customer photo", "macedon-ranges"),
        "insert_into_item" => __("Insert into testimonial", "macedon-ranges"),
        "uploaded_to_this_item" => __(
            "Uploaded to this testimonial",
            "macedon-ranges"
        ),
        "items_list" => __("Testimonials list", "macedon-ranges"),
        "items_list_navigation" => __(
            "Testimonials list navigation",
            "macedon-ranges"
        ),
        "filter_items_list" => __("Filter testimonials list", "macedon-ranges"),
    ];

    $args = [
        "label" => __("Testimonial", "macedon-ranges"),
        "description" => __("Customer testimonials and reviews", "macedon-ranges"),
        "labels" => $labels,
        "supports" => ["title", "editor", "thumbnail"],
        "hierarchical" => false,
        "public" => false,
        "show_ui" => true,
        "show_in_menu" => true,
        "menu_position" => 20,
        "menu_icon" => "dashicons-star-filled",
        "show_in_admin_bar" => true,
        "show_in_nav_menus" => false,
        "can_export" => true,
        "has_archive" => false,
        "exclude_from_search" => true,
        "publicly_queryable" => false,
        "capability_type" => "post",
        "show_in_rest" => false,
    ];

    register_post_type("testimonial", $args);
}
add_action("init", "register_testimonials_post_type", 0);

/**
 * Add Meta Box for Testimonial Details
 */
function add_testimonial_meta_boxes()
{
    add_meta_box(
        "testimonial_details",
        __("Testimonial Details", "macedon-ranges"),
        "render_testimonial_meta_box",
        "testimonial",
        "normal",
        "high"
    );
}
add_action("add_meta_boxes", "add_testimonial_meta_boxes");

/**
 * Render the Meta Box content
 */
function render_testimonial_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field(
        "testimonial_meta_box_nonce",
        "testimonial_meta_box_nonce_field"
    );

    // Get existing values
    $customer_name = get_post_meta(
        $post->ID,
        "_testimonial_customer_name",
        true
    );
    $customer_role = get_post_meta(
        $post->ID,
        "_testimonial_customer_role",
        true
    );
    $star_rating = get_post_meta($post->ID, "_testimonial_star_rating", true);

    // Set default rating if empty
    if (empty($star_rating)) {
        $star_rating = 5;
    }
    ?>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="testimonial_customer_name"><?php _e(
                    "Customer Name",
                    "macedon-ranges"
                ); ?></label>
            </th>
            <td>
                <input 
                    type="text" 
                    id="testimonial_customer_name" 
                    name="testimonial_customer_name" 
                    value="<?php echo esc_attr($customer_name); ?>" 
                    class="regular-text"
                    placeholder="e.g., Smith Wilson"
                />
                <p class="description"><?php _e(
                    'Enter the customer\'s full name',
                    "macedon-ranges"
                ); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="testimonial_customer_role"><?php _e(
                    "Location / Job Title",
                    "macedon-ranges"
                ); ?></label>
            </th>
            <td>
                <input 
                    type="text" 
                    id="testimonial_customer_role" 
                    name="testimonial_customer_role" 
                    value="<?php echo esc_attr($customer_role); ?>" 
                    class="regular-text"
                    placeholder="e.g., Woodend"
                />
                <p class="description"><?php _e(
                    'Enter the customer\'s location or job title',
                    "macedon-ranges"
                ); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="testimonial_star_rating"><?php _e(
                    "Star Rating",
                    "macedon-ranges"
                ); ?></label>
            </th>
            <td>
                <select id="testimonial_star_rating" name="testimonial_star_rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected(
    $star_rating,
    $i
); ?>>
                            <?php echo str_repeat("★", $i) .
                                str_repeat(
                                    "☆",
                                    5 - $i
                                ); ?> (<?php echo $i; ?> <?php echo $i === 1
     ? "star"
     : "stars"; ?>)
                        </option>
                    <?php endfor; ?>
                </select>
                <p class="description"><?php _e(
                    "Select the rating (1-5 stars)",
                    "macedon-ranges"
                ); ?></p>
            </td>
        </tr>
    </table>
    
    <style>
        #testimonial_details .form-table th {
            width: 200px;
            padding-left: 0;
        }
        #testimonial_details .form-table td {
            padding-right: 0;
        }
    </style>
    
    <?php
}

/**
 * Save Meta Box Data
 */
function save_testimonial_meta_box($post_id)
{
    // Check if nonce is set
    if (!isset($_POST["testimonial_meta_box_nonce_field"])) {
        return;
    }

    // Verify nonce
    if (
        !wp_verify_nonce(
            $_POST["testimonial_meta_box_nonce_field"],
            "testimonial_meta_box_nonce"
        )
    ) {
        return;
    }

    // Check if this is an autosave
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can("edit_post", $post_id)) {
        return;
    }

    // Save Customer Name
    if (isset($_POST["testimonial_customer_name"])) {
        update_post_meta(
            $post_id,
            "_testimonial_customer_name",
            sanitize_text_field($_POST["testimonial_customer_name"])
        );
    }

    // Save Customer Role
    if (isset($_POST["testimonial_customer_role"])) {
        update_post_meta(
            $post_id,
            "_testimonial_customer_role",
            sanitize_text_field($_POST["testimonial_customer_role"])
        );
    }

    // Save Star Rating
    if (isset($_POST["testimonial_star_rating"])) {
        $rating = intval($_POST["testimonial_star_rating"]);
        if ($rating >= 1 && $rating <= 5) {
            update_post_meta($post_id, "_testimonial_star_rating", $rating);
        }
    }
}
add_action("save_post_testimonial", "save_testimonial_meta_box");

/**
 * Customize admin columns for Testimonials
 */
function testimonial_custom_columns($columns)
{
    $new_columns = [];
    $new_columns["cb"] = $columns["cb"];
    $new_columns["featured_image"] = __("Photo", "macedon-ranges");
    $new_columns["title"] = $columns["title"];
    $new_columns["customer_name"] = __("Customer Name", "macedon-ranges");
    $new_columns["customer_role"] = __("Location", "macedon-ranges");
    $new_columns["rating"] = __("Rating", "macedon-ranges");
    $new_columns["date"] = $columns["date"];

    return $new_columns;
}
add_filter("manage_testimonial_posts_columns", "testimonial_custom_columns");

/**
 * Populate custom columns
 */
function testimonial_custom_column_content($column, $post_id)
{
    switch ($column) {
        case "featured_image":
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [50, 50]);
            } else {
                echo "—";
            }
            break;

        case "customer_name":
            $name = get_post_meta($post_id, "_testimonial_customer_name", true);
            echo $name ? esc_html($name) : "—";
            break;

        case "customer_role":
            $role = get_post_meta($post_id, "_testimonial_customer_role", true);
            echo $role ? esc_html($role) : "—";
            break;

        case "rating":
            $rating = get_post_meta($post_id, "_testimonial_star_rating", true);
            if ($rating) {
                echo str_repeat("★", intval($rating)) .
                    str_repeat("☆", 5 - intval($rating));
            } else {
                echo "—";
            }
            break;
    }
}
add_action(
    "manage_testimonial_posts_custom_column",
    "testimonial_custom_column_content",
    10,
    2
);

/**
 * Make custom columns sortable
 */
function testimonial_sortable_columns($columns)
{
    $columns["customer_name"] = "customer_name";
    $columns["rating"] = "rating";
    return $columns;
}
add_filter(
    "manage_edit-testimonial_sortable_columns",
    "testimonial_sortable_columns"
);

/**
 * Add Testimonials Customizer Settings
 */
function testimonials_customizer_settings($wp_customize)
{
    // Add Testimonials Section
    $wp_customize->add_section('testimonials_section', array(
        'title'    => __('Testimonials Section', 'macedon-ranges'),
        'priority' => 130,
    ));

    // Title Setting
    $wp_customize->add_setting('testimonials_title', array(
        'default'           => 'What Our Customers Say',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_title', array(
        'label'    => __('Section Title', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    // Subtitle Setting
    $wp_customize->add_setting('testimonials_subtitle', array(
        'default'           => 'Don\'t just take our word for it - hear from our satisfied customers across Macedon Ranges',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_subtitle', array(
        'label'    => __('Section Subtitle', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'textarea',
    ));

    // Statistics Settings
    // Stat 1 - Happy Customers
    $wp_customize->add_setting('testimonials_stat_customers', array(
        'default'           => '500+',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_customers', array(
        'label'    => __('Stat 1 Value', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('testimonials_stat_customers_label', array(
        'default'           => 'Happy Customers',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_customers_label', array(
        'label'    => __('Stat 1 Label', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    // Stat 2 - Local Produce
    $wp_customize->add_setting('testimonials_stat_local', array(
        'default'           => '100%',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_local', array(
        'label'    => __('Stat 2 Value', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('testimonials_stat_local_label', array(
        'default'           => 'Local Produce',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_local_label', array(
        'label'    => __('Stat 2 Label', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    // Stat 3 - Support
    $wp_customize->add_setting('testimonials_stat_support', array(
        'default'           => '24/7',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_support', array(
        'label'    => __('Stat 3 Value', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('testimonials_stat_support_label', array(
        'default'           => 'Support',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_support_label', array(
        'label'    => __('Stat 3 Label', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    // Stat 4 - Rating
    $wp_customize->add_setting('testimonials_stat_rating', array(
        'default'           => '4.9★',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_rating', array(
        'label'    => __('Stat 4 Value', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('testimonials_stat_rating_label', array(
        'default'           => 'Average Rating',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('testimonials_stat_rating_label', array(
        'label'    => __('Stat 4 Label', 'macedon-ranges'),
        'section'  => 'testimonials_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'testimonials_customizer_settings');