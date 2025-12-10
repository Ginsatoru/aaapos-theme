/**
 * WooCommerce Cart Notifications - FIXED
 * 
 * Handles beautiful toast notifications for cart actions
 * Auto-dismiss after 5 seconds with progress bar
 * EXCLUDES My Account pages
 * 
 * @package AAAPOS_Prime
 * @version 1.0.2
 */

(function($) {
    'use strict';

    /**
     * Check if we're on My Account page
     */
    function isMyAccountPage() {
        return $('body').hasClass('woocommerce-account') || 
               $('body').hasClass('woocommerce-edit-address') ||
               $('body').hasClass('woocommerce-edit-account') ||
               $('.woocommerce-MyAccount-navigation').length > 0;
    }

    /**
     * Initialize Cart Notifications
     */
    function initCartNotifications() {
        // Don't initialize on My Account pages
        if (isMyAccountPage()) {
            console.log('Cart notifications disabled on My Account page');
            return;
        }
        
        // Process existing notifications on page load
        processExistingNotifications();
        
        // Listen for AJAX add to cart events
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, button) {
            showSuccessNotification('Product added to cart!', button);
        });
        
        // Handle manual close buttons
        $(document).on('click', '.close-notice', function(e) {
            e.preventDefault();
            dismissNotification($(this).closest('.woocommerce-message, .woocommerce-info, .woocommerce-error'));
        });
    }

    /**
     * Process Existing Notifications
     * Enhances notifications that are already in the DOM
     */
    function processExistingNotifications() {
        // Don't process on My Account pages
        if (isMyAccountPage()) {
            return;
        }
        
        $('.woocommerce-message, .woocommerce-info, .woocommerce-error').each(function() {
            const $notice = $(this);
            
            // Skip if already processed
            if ($notice.hasClass('processed')) {
                return;
            }
            
            // Skip if inside My Account content
            if ($notice.closest('.woocommerce-MyAccount-content').length > 0) {
                return;
            }
            
            // Mark as processed
            $notice.addClass('processed');
            
            // Store the button first if it exists
            const $button = $notice.find('a.button, .button').first();
            const buttonHtml = $button.length ? $button.prop('outerHTML') : '';
            
            // Get all text content (excluding button)
            $button.remove();
            const textContent = $notice.html().trim();
            
            // Restructure the notification
            $notice.html('');
            
            // Add close button
            $notice.append('<button class="close-notice" aria-label="Close notification">&times;</button>');
            
            // Create message content wrapper
            const $messageContent = $('<div class="message-content"></div>');
            
            // Clean and parse the text content
            const cleanText = textContent.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            
            // Check if there's a product name pattern (usually "ProductName has been added")
            const hasBeenAddedMatch = cleanText.match(/^(.+?)\s+has been added/i);
            
            if (hasBeenAddedMatch) {
                // Create structured content with product name
                const productName = hasBeenAddedMatch[1].trim();
                $messageContent.append('<strong>' + escapeHtml(productName) + '</strong>');
                $messageContent.append(' has been added to your cart.');
            } else {
                // Use the original text
                $messageContent.html(textContent);
            }
            
            // Add button if exists
            if (buttonHtml) {
                $messageContent.append(buttonHtml);
            }
            
            $notice.append($messageContent);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                dismissNotification($notice);
            }, 5000);
        });
    }

    /**
     * Show Success Notification
     * Creates and displays a success toast notification
     */
    function showSuccessNotification(message, button) {
        // Don't show on My Account pages
        if (isMyAccountPage()) {
            return;
        }
        
        // Get product name if available
        let productName = 'Product';
        
        if (button && button.length) {
            const $productCard = button.closest('.product, li.product, .product-card');
            if ($productCard.length) {
                const $title = $productCard.find('.woocommerce-loop-product__title, h2, h3, .product-title');
                if ($title.length) {
                    productName = $title.text().trim();
                }
            }
        }
        
        // Create notification HTML with proper structure
        const notification = $('<div>', {
            'class': 'woocommerce-message processed',
            'role': 'alert'
        });
        
        // Add close button
        const closeButton = $('<button>', {
            'class': 'close-notice',
            'aria-label': 'Close notification',
            'html': '&times;'
        });
        
        // Create message content
        const messageContent = $('<div>', {
            'class': 'message-content'
        });
        
        // Add product name
        messageContent.append($('<strong>').text(productName));
        messageContent.append(' has been added to your cart.');
        
        // Add view cart button
        const viewCartButton = $('<a>', {
            'href': getCartUrl(),
            'class': 'button wc-forward',
            'text': 'View cart'
        });
        messageContent.append(viewCartButton);
        
        // Assemble notification
        notification.append(closeButton);
        notification.append(messageContent);
        
        // Remove existing notifications
        $('.woocommerce-message').not('.woocommerce-MyAccount-content .woocommerce-message').remove();
        
        // Append to body
        $('body').append(notification);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            dismissNotification(notification);
        }, 5000);
    }

    /**
     * Dismiss Notification
     * Animates and removes a notification
     */
    function dismissNotification($notice) {
        if (!$notice || !$notice.length) {
            return;
        }
        
        // Don't dismiss My Account page notifications
        if ($notice.closest('.woocommerce-MyAccount-content').length > 0) {
            return;
        }
        
        // Add fade out class
        $notice.addClass('fade-out');
        
        // Remove after animation completes
        setTimeout(function() {
            $notice.remove();
        }, 300);
    }

    /**
     * Get Cart URL
     * Returns the WooCommerce cart page URL
     */
    function getCartUrl() {
        // Try to get from WooCommerce params
        if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.cart_url) {
            return wc_add_to_cart_params.cart_url;
        }
        
        // Fallback to current site URL + /cart
        return window.location.origin + '/cart';
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Handle WooCommerce Fragments Refresh
     * Re-process notifications after AJAX updates
     */
    $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function() {
        if (!isMyAccountPage()) {
            processExistingNotifications();
        }
    });

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        initCartNotifications();
    });

    /**
     * Re-initialize after AJAX complete
     */
    $(document).ajaxComplete(function() {
        // Don't process on My Account pages
        if (isMyAccountPage()) {
            return;
        }
        
        // Small delay to ensure DOM is updated
        setTimeout(function() {
            processExistingNotifications();
        }, 100);
    });

})(jQuery);