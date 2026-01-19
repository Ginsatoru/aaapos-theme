<?php
/**
 * Custom Customizer Control: Sortable Category Selector
 * Allows selecting and reordering product categories via drag-and-drop
 * 
 * File: inc/customizer/category-order-control.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the control only when WP_Customize_Control is available
 * This prevents fatal errors when customizer isn't loaded
 */
function aaapos_register_category_order_control() {
    
    // Only load in customizer context
    if (!class_exists('WP_Customize_Control')) {
        return;
    }

/**
 * Category Order Control Class
 * Extends WP_Customize_Control to add drag-and-drop functionality
 */
class AAAPOS_Category_Order_Control extends WP_Customize_Control {
    
    /**
     * Control type
     */
    public $type = 'category-order';
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue() {
        wp_enqueue_script('jquery-ui-sortable');
        
        // Inline script for the control
        wp_add_inline_script('customize-controls', "
            (function($) {
                'use strict';
                
                wp.customize.controlConstructor['category-order'] = wp.customize.Control.extend({
                    ready: function() {
                        var control = this;
                        var wrapper = control.container.find('.category-order-wrapper');
                        var input = control.container.find('.category-order-input');
                        
                        // Initialize sortable
                        wrapper.sortable({
                            items: '.category-order-item',
                            handle: '.category-drag-handle',
                            axis: 'y',
                            opacity: 0.7,
                            cursor: 'move',
                            placeholder: 'category-order-placeholder',
                            update: function() {
                                control.updateValue();
                            }
                        });
                        
                        // Handle checkbox changes
                        wrapper.on('change', '.category-checkbox', function() {
                            control.updateValue();
                        });
                        
                        // Update hidden input value
                        control.updateValue = function() {
                            var selected = [];
                            wrapper.find('.category-checkbox:checked').each(function() {
                                selected.push($(this).val());
                            });
                            input.val(selected.join(',')).trigger('change');
                        };
                    }
                });
                
            })(jQuery);
        ");
        
        // Inline styles for the control
        wp_add_inline_style('customize-controls', "
            .category-order-wrapper {
                margin-top: 10px;
                max-height: 400px;
                overflow-y: auto;
                border: 1px solid #ddd;
                border-radius: 4px;
                background: #fff;
            }
            
            .category-order-item {
                display: flex;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #f0f0f0;
                background: #fff;
                transition: background 0.2s;
            }
            
            .category-order-item:last-child {
                border-bottom: none;
            }
            
            .category-order-item:hover {
                background: #f9f9f9;
            }
            
            .category-order-item.ui-sortable-helper {
                background: #f0f0f0;
                border: 1px solid #0073aa;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .category-order-placeholder {
                background: #e8f5fa;
                border: 2px dashed #0073aa;
                height: 50px;
                margin: 0;
                visibility: visible !important;
            }
            
            .category-drag-handle {
                cursor: move;
                padding: 5px;
                margin-right: 10px;
                color: #999;
                flex-shrink: 0;
            }
            
            .category-drag-handle:hover {
                color: #0073aa;
            }
            
            .category-checkbox {
                margin: 0 10px 0 0;
                flex-shrink: 0;
            }
            
            .category-info {
                flex: 1;
            }
            
            .category-name {
                font-weight: 500;
                color: #333;
                margin: 0 0 3px 0;
            }
            
            .category-count {
                font-size: 12px;
                color: #666;
                margin: 0;
            }
            
            .category-order-description {
                margin: 8px 0;
                font-style: italic;
                color: #666;
            }
        ");
    }
    
    /**
     * Render the control
     */
    public function render_content() {
        // Get all product categories
        $categories = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));
        
        if (empty($categories) || is_wp_error($categories)) {
            echo '<p>' . esc_html__('No categories found.', 'macedon-ranges') . '</p>';
            return;
        }
        
        // Get current selected categories
        $selected = $this->value();
        $selected_ids = !empty($selected) ? array_map('intval', explode(',', $selected)) : array();
        
        // Separate selected and unselected categories
        $selected_cats = array();
        $unselected_cats = array();
        
        // First, add selected categories in order
        if (!empty($selected_ids)) {
            foreach ($selected_ids as $cat_id) {
                $term = get_term($cat_id, 'product_cat');
                if ($term && !is_wp_error($term)) {
                    $selected_cats[] = $term;
                }
            }
        }
        
        // Then add unselected categories
        foreach ($categories as $category) {
            if (!in_array($category->term_id, $selected_ids)) {
                $unselected_cats[] = $category;
            }
        }
        
        // Merge arrays - selected first, then unselected
        $all_categories = array_merge($selected_cats, $unselected_cats);
        ?>
        
        <label class="customize-control-title">
            <?php echo esc_html($this->label); ?>
        </label>
        
        <?php if (!empty($this->description)) : ?>
            <span class="description customize-control-description category-order-description">
                <?php echo esc_html($this->description); ?>
            </span>
        <?php endif; ?>
        
        <input type="hidden" 
               class="category-order-input" 
               <?php $this->link(); ?> 
               value="<?php echo esc_attr($this->value()); ?>" />
        
        <div class="category-order-wrapper">
            <?php foreach ($all_categories as $category) : 
                $is_checked = in_array($category->term_id, $selected_ids);
            ?>
                <div class="category-order-item">
                    <span class="category-drag-handle">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <rect x="4" y="3" width="2" height="2"/>
                            <rect x="10" y="3" width="2" height="2"/>
                            <rect x="4" y="7" width="2" height="2"/>
                            <rect x="10" y="7" width="2" height="2"/>
                            <rect x="4" y="11" width="2" height="2"/>
                            <rect x="10" y="11" width="2" height="2"/>
                        </svg>
                    </span>
                    
                    <input type="checkbox" 
                           class="category-checkbox" 
                           value="<?php echo esc_attr($category->term_id); ?>"
                           <?php checked($is_checked); ?> />
                    
                    <div class="category-info">
                        <p class="category-name">
                            <?php echo esc_html($category->name); ?>
                        </p>
                        <p class="category-count">
                            <?php 
                            printf(
                                _n('%s product', '%s products', $category->count, 'macedon-ranges'),
                                number_format_i18n($category->count)
                            ); 
                            ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
    }
}

} // End of aaapos_register_category_order_control()

// Hook to customize_register to ensure WP_Customize_Control is available
add_action('customize_register', 'aaapos_register_category_order_control', 1);

/**
 * Sanitize category order input
 * Ensures only valid category IDs in proper format
 */
function aaapos_sanitize_category_order($input) {
    if (empty($input)) {
        return '';
    }
    
    // Convert to array
    $values = is_array($input) ? $input : explode(',', $input);
    
    // Sanitize as integers
    $sanitized = array_map('absint', $values);
    
    // Remove zeros and duplicates
    $sanitized = array_filter($sanitized);
    $sanitized = array_unique($sanitized);
    
    // Verify valid category IDs
    $valid_categories = array();
    foreach ($sanitized as $cat_id) {
        $term = get_term($cat_id, 'product_cat');
        if ($term && !is_wp_error($term)) {
            $valid_categories[] = $cat_id;
        }
    }
    
    // Return as comma-separated string
    return implode(',', $valid_categories);
}