<?php
/**
 * Multiple Checkbox Customizer Control
 * With drag-and-drop reordering support
 * 
 * @package AAAPOS_Prime
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('WP_Customize_Control')) {
    
    /**
     * Multiple Checkbox Control Class with Drag & Drop Ordering
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

            // Get saved values (comma-separated IDs in saved order)
            $saved_values = $this->value();
            if (!is_array($saved_values)) {
                $saved_values = !empty($saved_values) ? explode(',', $saved_values) : [];
            }
            $saved_values = array_filter(array_map('trim', $saved_values));

            // Build ordered list: saved items first (in saved order), then unsaved items
            $ordered_choices = [];
            foreach ($saved_values as $id) {
                if (isset($this->choices[$id])) {
                    $ordered_choices[$id] = $this->choices[$id];
                }
            }
            foreach ($this->choices as $id => $label) {
                if (!isset($ordered_choices[$id])) {
                    $ordered_choices[$id] = $label;
                }
            }

            $control_id = esc_js($this->id);
            ?>

            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>

            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>

            <div class="aaapos-dnd-wrapper" style="margin-top: 8px;">

                <!-- Select All -->
                <label style="display:flex; align-items:center; gap:8px; padding: 8px 10px; margin-bottom:8px; background:#f0f6fc; border:1px solid #c3d4e4; border-radius:4px; font-weight:600; cursor:pointer;">
                    <input type="checkbox" class="aaapos-select-all">
                    <?php esc_html_e('Select All Categories', 'aaapos-prime'); ?>
                </label>

                <!-- Drag hint -->
                <p style="font-size:11px; color:#888; margin: 0 0 8px; display:flex; align-items:center; gap:4px;">
                    <span style="font-size:14px;">⠿</span>
                    <?php esc_html_e('Drag items to reorder. Checked items will show in the filter.', 'aaapos-prime'); ?>
                </p>

                <!-- Sortable list -->
                <ul class="aaapos-sortable-list" style="list-style:none; margin:0; padding:0; border:1px solid #ddd; border-radius:4px; background:#fff; max-height:320px; overflow-y:auto;">
                    <?php foreach ($ordered_choices as $value => $label) :
                        $checked = in_array((string) $value, array_map('strval', $saved_values));
                    ?>
                        <li class="aaapos-sortable-item"
                            data-value="<?php echo esc_attr($value); ?>"
                            style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-bottom:1px solid #f0f0f0; cursor:grab; user-select:none; background:#fff; transition: background 0.15s ease;">

                            <!-- Drag handle -->
                            <span class="aaapos-drag-handle" style="color:#bbb; font-size:16px; flex-shrink:0; cursor:grab;" title="Drag to reorder">⠿</span>

                            <!-- Checkbox -->
                            <input type="checkbox"
                                   class="aaapos-category-checkbox"
                                   value="<?php echo esc_attr($value); ?>"
                                   <?php checked($checked, true); ?>
                                   style="flex-shrink:0; cursor:pointer;">

                            <!-- Label -->
                            <span style="font-size:13px; line-height:1.4;"><?php echo esc_html($label); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>

            <!-- Hidden input that WP Customizer reads -->
            <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr(implode(',', $saved_values)); ?>">

            <script type="text/javascript">
            (function($) {
                $(document).ready(function() {

                    var controlWrap  = $('#customize-control-<?php echo $control_id; ?>');
                    var hiddenInput  = controlWrap.find('input[type="hidden"]');
                    var list         = controlWrap.find('.aaapos-sortable-list');
                    var selectAll    = controlWrap.find('.aaapos-select-all');

                    // ── Update hidden input from current DOM order + checked state ──
                    function syncValue() {
                        var values = [];
                        list.find('.aaapos-sortable-item').each(function() {
                            var cb = $(this).find('.aaapos-category-checkbox');
                            if (cb.is(':checked')) {
                                values.push($(this).data('value').toString());
                            }
                        });
                        hiddenInput.val(values.join(',')).trigger('change');
                    }

                    // ── Update "Select All" indeterminate/checked state ──
                    function syncSelectAll() {
                        var total   = list.find('.aaapos-category-checkbox').length;
                        var checked = list.find('.aaapos-category-checkbox:checked').length;
                        selectAll.prop('indeterminate', checked > 0 && checked < total);
                        selectAll.prop('checked', checked === total);
                    }

                    // ── Checkbox change ──
                    list.on('change', '.aaapos-category-checkbox', function() {
                        syncValue();
                        syncSelectAll();
                    });

                    // ── Select All toggle ──
                    selectAll.on('change', function() {
                        list.find('.aaapos-category-checkbox').prop('checked', $(this).is(':checked'));
                        syncValue();
                        syncSelectAll();
                    });

                    // ── Initialise state ──
                    syncSelectAll();

                    // ── Drag & Drop reorder ──
                    var draggingItem = null;
                    var placeholder  = null;

                    list.on('dragstart', '.aaapos-sortable-item', function(e) {
                        draggingItem = this;
                        $(this).css('opacity', '0.4');
                        e.originalEvent.dataTransfer.effectAllowed = 'move';
                        e.originalEvent.dataTransfer.setData('text/plain', '');

                        // Create placeholder
                        placeholder = $('<li>')
                            .addClass('aaapos-drag-placeholder')
                            .css({
                                height:       $(this).outerHeight() + 'px',
                                background:   '#e8f3fb',
                                border:       '2px dashed #0073aa',
                                borderRadius: '3px',
                                margin:       '0',
                                listStyle:    'none'
                            });
                    });

                    list.on('dragend', '.aaapos-sortable-item', function() {
                        $(this).css('opacity', '1');
                        if (placeholder) {
                            placeholder.remove();
                            placeholder = null;
                        }
                        draggingItem = null;
                        syncValue();
                        syncSelectAll();
                    });

                    list.on('dragover', '.aaapos-sortable-item', function(e) {
                        e.preventDefault();
                        e.originalEvent.dataTransfer.dropEffect = 'move';

                        if (!draggingItem || this === draggingItem) return;

                        var rect     = this.getBoundingClientRect();
                        var midpoint = rect.top + rect.height / 2;

                        if (placeholder) placeholder.detach();

                        if (e.originalEvent.clientY < midpoint) {
                            $(this).before(placeholder);
                            $(this).before($(draggingItem).detach());
                        } else {
                            $(this).after(placeholder);
                            $(this).after($(draggingItem).detach());
                        }
                    });

                    // Make items draggable
                    list.find('.aaapos-sortable-item').attr('draggable', 'true');

                    // ── Visual hover feedback ──
                    list.on('mouseenter', '.aaapos-sortable-item', function() {
                        $(this).css('background', '#f7fbff');
                    });
                    list.on('mouseleave', '.aaapos-sortable-item', function() {
                        $(this).css('background', '#fff');
                    });

                });
            })(jQuery);
            </script>

            <?php
        }
    }
}