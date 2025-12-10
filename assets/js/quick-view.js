/**
 * Quick View Functionality - FIXED VERSION
 * Displays product details in a modal popup
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

        // Fetch product data via AJAX - FIXED!
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
                    
                    // Reinitialize WooCommerce scripts for variations
                    if (typeof $.fn.wc_variation_form !== 'undefined') {
                        $content.find('.variations_form').wc_variation_form();
                    }
                    
                    // Trigger quantity selector
                    $(document.body).trigger('quick_view_loaded');
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
            console.log('Quick View clicked for product:', productId); // Debug
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