/**
 * Custom Variation Swatches for WooCommerce
 * Converts color and size dropdowns into modern UI elements
 * FIXED: Size buttons reset on page refresh (no persistent selection)
 * 
 * @package AAAPOS_Prime
 * Location: assets/js/variation-swatches.js
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // CRITICAL FIX: Reset all variation selects on page load
        // This prevents WooCommerce from restoring previous selections
        resetVariationsOnLoad();
        
        // Then initialize swatches
        initVariationSwatches();
    });

    /**
     * Reset all variation selections on page load
     * This ensures no variations appear selected unless explicitly in URL
     */
    function resetVariationsOnLoad() {
        // Check if there are variation parameters in the URL
        const urlParams = new URLSearchParams(window.location.search);
        let hasVariationInUrl = false;
        
        // Check for any attribute_ parameters
        for (let param of urlParams.keys()) {
            if (param.startsWith('attribute_')) {
                hasVariationInUrl = true;
                break;
            }
        }
        
        // If no variations in URL, reset all selects to empty
        if (!hasVariationInUrl) {
            $('.variations select').each(function() {
                $(this).val('').trigger('change');
            });
        }
    }

    function initVariationSwatches() {
        // Convert Color Variations to Swatches
        convertColorVariations();
        
        // Convert Size Variations to Buttons
        convertSizeVariations();
    }

    /**
     * Convert Color Dropdowns to Circular Swatches
     */
    function convertColorVariations() {
        $('.variations select').each(function() {
            const $select = $(this);
            const attributeName = $select.attr('name');
            
            // Check if this is a color attribute
            if (!attributeName || !attributeName.toLowerCase().includes('color')) {
                return;
            }

            // Create swatches container
            const $swatchContainer = $('<div class="color-swatches"></div>');
            
            // Get all options
            $select.find('option').each(function() {
                const $option = $(this);
                const value = $option.val();
                const label = $option.text();
                
                // Skip "Choose an option" text
                if (!value) return;
                
                // Create color swatch
                const colorCode = getColorCode(value);
                const $swatch = $('<div class="color-swatch"></div>');
                $swatch.attr('data-value', value);
                $swatch.attr('title', label);
                $swatch.css('background-color', colorCode);
                
                // Add border for white/light colors
                if (isLightColor(colorCode)) {
                    $swatch.css('border', '2px solid #e5e7eb');
                }
                
                // Click handler
                $swatch.on('click', function() {
                    $swatchContainer.find('.color-swatch').removeClass('selected');
                    $(this).addClass('selected');
                    $select.val(value).trigger('change');
                });
                
                $swatchContainer.append($swatch);
            });
            
            // Insert swatches and hide select
            $select.hide().after($swatchContainer);
            
            // Only set initial selection if value exists and is valid
            const initialValue = $select.val();
            if (initialValue && initialValue !== '') {
                $swatchContainer.find(`[data-value="${initialValue}"]`).addClass('selected');
            }
        });
    }

    /**
     * Convert Size Dropdowns to Modern Buttons
     */
    function convertSizeVariations() {
        $('.variations select').each(function() {
            const $select = $(this);
            const attributeName = $select.attr('name');
            
            // Check if this is a size attribute
            if (!attributeName || !attributeName.toLowerCase().includes('size')) {
                return;
            }

            // Create button container
            const $buttonContainer = $('<div class="size-buttons"></div>');
            
            // Get all options
            $select.find('option').each(function() {
                const $option = $(this);
                const value = $option.val();
                const label = $option.text();
                
                // Skip "Choose an option" text
                if (!value) return;
                
                // Create size button
                const $button = $('<button type="button" class="size-button"></button>');
                $button.attr('data-value', value);
                $button.text(label);
                
                // Check if option is disabled (out of stock)
                if ($option.is(':disabled')) {
                    $button.prop('disabled', true);
                }
                
                // Click handler
                $button.on('click', function(e) {
                    e.preventDefault();
                    if (!$(this).prop('disabled')) {
                        $buttonContainer.find('.size-button').removeClass('selected');
                        $(this).addClass('selected');
                        $select.val(value).trigger('change');
                    }
                });
                
                $buttonContainer.append($button);
            });
            
            // Insert buttons and hide select
            $select.hide().after($buttonContainer);
            
            // Only set initial selection if value exists and is valid
            const initialValue = $select.val();
            if (initialValue && initialValue !== '') {
                $buttonContainer.find(`[data-value="${initialValue}"]`).addClass('selected');
            }
        });
    }

    /**
     * Get color code from color name
     */
    function getColorCode(colorName) {
        const colorMap = {
            // Common colors
            'black': '#000000',
            'white': '#ffffff',
            'red': '#ef4444',
            'blue': '#3b82f6',
            'green': '#10b981',
            'yellow': '#fbbf24',
            'purple': '#7c3aed',
            'pink': '#ec4899',
            'orange': '#f97316',
            'gray': '#6b7280',
            'grey': '#6b7280',
            'brown': '#92400e',
            'navy': '#1e3a8a',
            'teal': '#14b8a6',
            'cyan': '#06b6d4',
            'lime': '#84cc16',
            'indigo': '#6366f1',
            'violet': '#8b5cf6',
            'fuchsia': '#d946ef',
            'rose': '#f43f5e',
            'amber': '#f59e0b',
            'emerald': '#059669',
            'sky': '#0ea5e9',
            'slate': '#64748b',
        };
        
        const normalizedName = colorName.toLowerCase().trim();
        
        // Check if it's a hex color
        if (normalizedName.startsWith('#')) {
            return normalizedName;
        }
        
        // Check color map
        return colorMap[normalizedName] || '#7c3aed'; // Default to purple
    }

    /**
     * Check if color is light (for border decision)
     */
    function isLightColor(hexColor) {
        // Remove # if present
        const hex = hexColor.replace('#', '');
        
        // Convert to RGB
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        
        // Calculate brightness
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        
        return brightness > 200;
    }

    /**
     * Update variation swatches when WooCommerce updates variations
     */
    $(document).on('woocommerce_update_variation_values', function() {
        // Update disabled states for size buttons
        $('.size-buttons .size-button').each(function() {
            const $button = $(this);
            const value = $button.data('value');
            const $select = $button.parent().prev('select');
            const $option = $select.find(`option[value="${value}"]`);
            
            if ($option.is(':disabled')) {
                $button.prop('disabled', true);
            } else {
                $button.prop('disabled', false);
            }
        });
    });

})(jQuery);