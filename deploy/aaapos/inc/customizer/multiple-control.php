<?php
/**
 * Multiple Checkbox Customizer Control
 * Allows selection of multiple categories with "Select All" option
 * 
 * @package AAAPOS_Prime
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('WP_Customize_Control')) {
    
    /**
     * Multiple Checkbox Control Class
     */
    class AAAPOS_Checkbox_Multiple_Control extends WP_Customize_Control {
        
        /**
         * Control type
         */
        public $type = 'checkbox-multiple';
        
        /**
         * Render the control content
         */
        public function render_content() {
            if (empty($this->choices)) {
                return;
            }
            ?>
            
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            
            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>
            
            <div class="checkbox-multiple-wrapper" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-top: 8px; background: #fff;">
                
                <!-- Select All Checkbox -->
                <label style="display: block; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 2px solid #0073aa; font-weight: 600;">
                    <input type="checkbox" class="select-all-categories" style="margin-right: 8px;">
                    <?php esc_html_e('Select All Categories', 'aaapos-prime'); ?>
                </label>
                
                <?php
                $saved_values = $this->value();
                if (!is_array($saved_values)) {
                    $saved_values = explode(',', $saved_values);
                }
                
                foreach ($this->choices as $value => $label) :
                    $checked = in_array($value, $saved_values) ? 'checked="checked"' : '';
                ?>
                    <label style="display: block; margin-bottom: 6px;">
                        <input 
                            type="checkbox" 
                            value="<?php echo esc_attr($value); ?>" 
                            class="category-checkbox" 
                            <?php echo $checked; ?>
                            style="margin-right: 8px;"
                        />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
                
            </div>
            
            <input 
                type="hidden" 
                <?php $this->link(); ?> 
                value="<?php echo esc_attr(implode(',', $saved_values)); ?>"
            />
            
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                var control = $('#customize-control-<?php echo esc_js($this->id); ?>');
                var hiddenInput = control.find('input[type="hidden"]');
                var checkboxes = control.find('.category-checkbox');
                var selectAll = control.find('.select-all-categories');
                
                // Update hidden input when checkboxes change
                function updateHiddenInput() {
                    var values = [];
                    checkboxes.filter(':checked').each(function() {
                        values.push($(this).val());
                    });
                    hiddenInput.val(values.join(',')).trigger('change');
                }
                
                // Update Select All state
                function updateSelectAllState() {
                    var totalCheckboxes = checkboxes.length;
                    var checkedCheckboxes = checkboxes.filter(':checked').length;
                    selectAll.prop('checked', totalCheckboxes === checkedCheckboxes);
                }
                
                // Handle individual checkbox change
                checkboxes.on('change', function() {
                    updateHiddenInput();
                    updateSelectAllState();
                });
                
                // Handle Select All
                selectAll.on('change', function() {
                    var isChecked = $(this).is(':checked');
                    checkboxes.prop('checked', isChecked);
                    updateHiddenInput();
                });
                
                // Initialize Select All state
                updateSelectAllState();
            });
            </script>
            
            <?php
        }
    }
}