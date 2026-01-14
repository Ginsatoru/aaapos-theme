/**
 * Quick View Functionality - FIXED VERSION WITH VARIATION SUPPORT
 * Displays product details in a modal popup
 * Now properly initializes WooCommerce variation forms and custom swatches
 */

(function($) {
    'use strict';

    // Create Quick View Modal HTML
    function createQuickViewModal() {
        if ($('#quick-view-modal').length) return;

        const modalHTML = `
            <div id="quick-view-modal" class="quick-view-modal" style="display: none;">
                <div class="quick-view-overlay"></div>
                <div class="quick-view-container">
                    <button class="quick-view-close" aria-label="Close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <div class="quick-view-content">
                        <div class="quick-view-loading">
                            <div class="spinner"></div>
                            <p>Loading product...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHTML);
    }

    // Open Quick View
    function openQuickView(productId) {
        const $modal = $('#quick-view-modal');
        const $content = $modal.find('.quick-view-content');

        // Show modal with loading state
        $modal.fadeIn(300);
        $('body').addClass('quick-view-open');
        $content.html(`
            <div class="quick-view-loading">
                <div class="spinner"></div>
                <p>Loading product...</p>
            </div>
        `);

        // Fetch product data via AJAX
        $.ajax({
            url: aaaposQuickView.ajax_url,
            type: 'POST',
            data: {
                action: 'get_quick_view_product',
                product_id: productId,
                security: aaaposQuickView.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $content.html(response.data.html);
                    
                    // CRITICAL: Initialize WooCommerce variation form
                    initializeVariationForm($content);
                    
                    // Initialize custom variation swatches (color/size buttons)
                    initializeCustomSwatches($content);
                    
                    // Trigger quantity selector initialization
                    $(document.body).trigger('quick_view_loaded');
                    
                    console.log('Quick view loaded successfully');
                } else {
                    $content.html('<div class="quick-view-error"><p>Error loading product. Please try again.</p></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Quick View Error:', error);
                $content.html('<div class="quick-view-error"><p>Error loading product. Please try again.</p></div>');
            }
        });
    }

    /**
     * Initialize WooCommerce variation form
     * This is CRITICAL for variable products to work properly
     */
    function initializeVariationForm($container) {
        const $form = $container.find('.variations_form');
        
        if ($form.length && typeof $.fn.wc_variation_form !== 'undefined') {
            // Initialize WooCommerce's built-in variation handler
            $form.wc_variation_form();
            
            // Also initialize variations manually
            $form.trigger('check_variations');
            $form.trigger('wc_variation_form');
            
            console.log('Variation form initialized');
        }
    }

    /**
     * Initialize custom variation swatches (colors & sizes)
     * This converts select dropdowns to visual swatches/buttons
     */
    function initializeCustomSwatches($container) {
        // Wait a bit for WooCommerce to finish its initialization
        setTimeout(function() {
            // Convert color variations
            convertColorVariations($container);
            
            // Convert size variations
            convertSizeVariations($container);
            
            console.log('Custom swatches initialized');
        }, 100);
    }

    /**
     * Convert Color Dropdowns to Circular Swatches
     */
    function convertColorVariations($container) {
        $container.find('.variations select').each(function() {
            const $select = $(this);
            const attributeName = $select.attr('name');
            
            // Check if this is a color attribute
            if (!attributeName || !attributeName.toLowerCase().includes('color')) {
                return;
            }

            // Don't convert if already converted
            if ($select.next('.color-swatches').length) {
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
            
            // Set initial selection if exists
            const initialValue = $select.val();
            if (initialValue && initialValue !== '') {
                $swatchContainer.find(`[data-value="${initialValue}"]`).addClass('selected');
            }
        });
    }

    /**
     * Convert Size Dropdowns to Modern Buttons
     */
    function convertSizeVariations($container) {
        $container.find('.variations select').each(function() {
            const $select = $(this);
            const attributeName = $select.attr('name');
            
            // Check if this is a size attribute
            if (!attributeName || !attributeName.toLowerCase().includes('size')) {
                return;
            }

            // Don't convert if already converted
            if ($select.next('.size-buttons').length) {
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
            
            // Set initial selection if exists
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

    // Close Quick View
    function closeQuickView() {
        const $modal = $('#quick-view-modal');
        $modal.fadeOut(300);
        $('body').removeClass('quick-view-open');
        
        // Clear content after animation
        setTimeout(function() {
            $modal.find('.quick-view-content').html('');
        }, 300);
    }

    // Initialize on document ready
    $(document).ready(function() {
        // Create modal
        createQuickViewModal();

        // Quick View button click
        $(document).on('click', '.quick-view-button', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            console.log('Quick View clicked for product:', productId);
            openQuickView(productId);
        });

        // Close button click
        $(document).on('click', '.quick-view-close, .quick-view-overlay', function(e) {
            e.preventDefault();
            closeQuickView();
        });

        // Close on ESC key
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape' && $('#quick-view-modal').is(':visible')) {
                closeQuickView();
            }
        });

        // Prevent closing when clicking inside modal content
        $(document).on('click', '.quick-view-container', function(e) {
            e.stopPropagation();
        });
    });

})(jQuery);