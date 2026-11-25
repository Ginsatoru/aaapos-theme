<?php
/**
 * Custom widgets
 */

// Register custom widgets
function mr_register_widgets() {
    register_widget('MR_Featured_Products_Widget');
    register_widget('MR_Testimonials_Widget');
    register_widget('MR_Newsletter_Widget');
}
add_action('widgets_init', 'mr_register_widgets');

/**
 * Featured Products Widget
 */
class MR_Featured_Products_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'mr_featured_products',
            __('Featured Products', 'macedon-ranges'),
            array('description' => __('Display featured products', 'macedon-ranges'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Products', 'macedon-ranges');
        $number = !empty($instance['number']) ? absint($instance['number']) : 4;

        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => $number,
            'visibility' => 'visible',
            'meta_key' => '_featured',
            'meta_value' => 'yes'
        ));

        if ($products) {
            echo '<div class="widget-products">';
            foreach ($products as $product) {
                echo '<div class="widget-product">';
                echo '<a href="' . esc_url($product->get_permalink()) . '">';
                echo $product->get_image('thumbnail');
                echo '<span class="product-title">' . esc_html($product->get_name()) . '</span>';
                echo '<span class="product-price">' . $product->get_price_html() . '</span>';
                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No featured products found.', 'macedon-ranges') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? $instance['number'] : 4;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'macedon-ranges'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of products:', 'macedon-ranges'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="12" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 4;
        return $instance;
    }
}

/**
 * Testimonials Widget
 */
class MR_Testimonials_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'mr_testimonials',
            __('Testimonials', 'macedon-ranges'),
            array('description' => __('Display customer testimonials', 'macedon-ranges'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('Testimonials', 'macedon-ranges');

        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        // This would typically come from a custom setting or post type
        echo '<div class="widget-testimonials">';
        echo '<blockquote class="testimonial">';
        echo '<p>"' . esc_html__('Great products and excellent service! Highly recommended.', 'macedon-ranges') . '"</p>';
        echo '<cite>- ' . esc_html__('Happy Customer', 'macedon-ranges') . '</cite>';
        echo '</blockquote>';
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'macedon-ranges'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Newsletter Widget
 */
class MR_Newsletter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'mr_newsletter',
            __('Newsletter Signup', 'macedon-ranges'),
            array('description' => __('Display newsletter signup form', 'macedon-ranges'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('Newsletter', 'macedon-ranges');
        $description = !empty($instance['description']) ? $instance['description'] : __('Subscribe to our newsletter for updates.', 'macedon-ranges');

        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        if ($description) {
            echo '<p class="newsletter-description">' . esc_html($description) . '</p>';
        }
        ?>
        <form class="newsletter-form widget-newsletter" method="post">
            <div class="form-group">
                <input type="email" name="email" placeholder="<?php esc_attr_e('Your email address', 'macedon-ranges'); ?>" required>
                <button type="submit"><?php esc_html_e('Subscribe', 'macedon-ranges'); ?></button>
            </div>
            <?php if (!empty($instance['show_gdpr'])) : ?>
                <div class="form-checkbox">
                    <label>
                        <input type="checkbox" name="gdpr" required>
                        <span><?php esc_html_e('I agree to receive marketing emails.', 'macedon-ranges'); ?></span>
                    </label>
                </div>
            <?php endif; ?>
        </form>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $show_gdpr = !empty($instance['show_gdpr']);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'macedon-ranges'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description:', 'macedon-ranges'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_gdpr); ?> id="<?php echo esc_attr($this->get_field_id('show_gdpr')); ?>" name="<?php echo esc_attr($this->get_field_name('show_gdpr')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_gdpr')); ?>"><?php esc_html_e('Show GDPR checkbox', 'macedon-ranges'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? sanitize_textarea_field($new_instance['description']) : '';
        $instance['show_gdpr'] = !empty($new_instance['show_gdpr']);
        return $instance;
    }
}