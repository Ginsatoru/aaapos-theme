/**
 * Setup Wizard JavaScript - iPhone Style
 * 
 * @package AAAPOS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const SetupWizard = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initBrandingPreview();
            this.initAnimations();
        },
        
        /**
         * Bind Events
         */
        bindEvents: function() {
            // Create pages button
            $('#create-pages-btn').on('click', this.createPages);
            
            // Save branding button
            $('#save-branding-btn').on('click', this.saveBranding);
            
            // Complete setup button
            $('#complete-setup-btn').on('click', this.completeSetup);
            
            // Color picker sync
            $('#brand_color').on('input', this.syncColorPicker);
            $('#brand_color_text').on('input', this.syncColorText);
            $('#brand_color_text').on('blur', this.validateColor);
            
            // Site title preview
            $('#site_title').on('input', this.updateSiteTitlePreview);
        },
        
        /**
         * Initialize Animations
         */
        initAnimations: function() {
            // Stagger animation for feature items
            $('.feature-item, .page-card, .next-step-item').each(function(index) {
                $(this).css({
                    'animation': 'slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                    'animation-delay': (index * 0.08) + 's',
                    'opacity': '0'
                });
            });
            
            // Add keyframes if not exists
            if (!document.getElementById('wizard-animations')) {
                const style = document.createElement('style');
                style.id = 'wizard-animations';
                style.textContent = `
                    @keyframes slideUp {
                        from {
                            opacity: 0;
                            transform: translateY(12px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        },
        
        /**
         * Create Pages
         */
        createPages: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            
            // Disable button and show loader
            $btn.prop('disabled', true);
            
            // Add haptic-like feedback
            SetupWizard.addButtonFeedback($btn);
            
            $.ajax({
                url: aaaposSetup.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aaapos_setup_configure_pages',
                    nonce: aaaposSetup.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI to show pages created
                        SetupWizard.updatePagesList();
                        
                        // Show success notification
                        SetupWizard.showNotification('success', response.data.message);
                        
                        // Redirect after animation completes
                        setTimeout(function() {
                            window.location.href = aaaposSetup.adminUrl + 'admin.php?page=aaapos-setup&step=branding';
                        }, 1800);
                    } else {
                        SetupWizard.showNotification('error', response.data.message || 'An error occurred');
                        $btn.prop('disabled', false);
                    }
                },
                error: function() {
                    SetupWizard.showNotification('error', 'An error occurred. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        },
        
        /**
         * Update Pages List UI
         */
        updatePagesList: function() {
            const $pageCards = $('.page-card');
            let delay = 0;
            
            $pageCards.each(function(index) {
                const $card = $(this);
                
                setTimeout(function() {
                    // Add check mark animation
                    $card.css({
                        'transform': 'scale(1.02)',
                        'transition': 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)'
                    });
                    
                    setTimeout(function() {
                        $card.css('transform', 'scale(1)');
                    }, 300);
                    
                    // Update badge
                    $card.find('.page-badge')
                        .removeClass('pending')
                        .addClass('created')
                        .text('Created');
                    
                }, delay);
                
                delay += 150;
            });
        },
        
        /**
         * Save Branding
         */
        saveBranding: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const siteTitle = $('#site_title').val();
            const brandColor = $('#brand_color').val();
            
            // Validate inputs
            if (!siteTitle.trim()) {
                SetupWizard.showNotification('error', 'Please enter a store name');
                $('#site_title').focus();
                return;
            }
            
            // Disable button
            $btn.prop('disabled', true);
            
            // Add button feedback
            SetupWizard.addButtonFeedback($btn);
            
            $.ajax({
                url: aaaposSetup.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aaapos_setup_save_branding',
                    nonce: aaaposSetup.nonce,
                    site_title: siteTitle,
                    brand_color: brandColor
                },
                success: function(response) {
                    if (response.success) {
                        SetupWizard.showNotification('success', response.data.message);
                        
                        // Update CSS variable globally
                        document.documentElement.style.setProperty('--brand-color', brandColor);
                        
                        // Redirect after delay
                        setTimeout(function() {
                            window.location.href = aaaposSetup.adminUrl + 'admin.php?page=aaapos-setup&step=ready';
                        }, 1200);
                    } else {
                        SetupWizard.showNotification('error', response.data.message || 'An error occurred');
                        $btn.prop('disabled', false);
                    }
                },
                error: function() {
                    SetupWizard.showNotification('error', 'An error occurred. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        },
        
        /**
         * Complete Setup
         */
        completeSetup: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            $btn.prop('disabled', true);
            
            SetupWizard.addButtonFeedback($btn);
            
            $.ajax({
                url: aaaposSetup.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aaapos_setup_complete',
                    nonce: aaaposSetup.nonce
                },
                success: function(response) {
                    if (response.success && response.data.redirect) {
                        // Show success message
                        SetupWizard.showNotification('success', 'Launching your store...');
                        
                        // Redirect after animation
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 800);
                    }
                },
                error: function() {
                    SetupWizard.showNotification('error', 'An error occurred. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        },
        
        /**
         * Initialize Branding Preview
         */
        initBrandingPreview: function() {
            // Set initial color
            const initialColor = $('#brand_color').val() || '#0f8abe';
            this.updateBrandColor(initialColor);
            
            // Sync text field
            $('#brand_color_text').val(initialColor.toUpperCase());
        },
        
        /**
         * Sync Color Picker
         */
        syncColorPicker: function() {
            const color = $(this).val();
            $('#brand_color_text').val(color.toUpperCase());
            SetupWizard.updateBrandColor(color);
        },
        
        /**
         * Sync Color Text
         */
        syncColorText: function() {
            let color = $(this).val().trim();
            
            // Auto-add # if missing
            if (color && !color.startsWith('#')) {
                color = '#' + color;
                $(this).val(color);
            }
            
            if (SetupWizard.isValidHex(color)) {
                $('#brand_color').val(color);
                SetupWizard.updateBrandColor(color);
            }
        },
        
        /**
         * Validate Color
         */
        validateColor: function() {
            let color = $(this).val().trim();
            
            // Auto-add # if missing
            if (color && !color.startsWith('#')) {
                color = '#' + color;
            }
            
            if (!SetupWizard.isValidHex(color)) {
                const defaultColor = '#0f8abe';
                $(this).val(defaultColor.toUpperCase());
                $('#brand_color').val(defaultColor);
                SetupWizard.updateBrandColor(defaultColor);
                SetupWizard.showNotification('error', 'Invalid color format. Using default color.');
            } else {
                $(this).val(color.toUpperCase());
            }
        },
        
        /**
         * Is Valid Hex Color
         */
        isValidHex: function(color) {
            return /^#[0-9A-F]{6}$/i.test(color);
        },
        
        /**
         * Update Brand Color Preview
         */
        updateBrandColor: function(color) {
            // Update CSS variable
            document.documentElement.style.setProperty('--brand-color', color);
            
            // Calculate darker shade for hover
            const darkerColor = SetupWizard.adjustColor(color, -20);
            document.documentElement.style.setProperty('--brand-color-dark', darkerColor);
            
            // Calculate lighter shade for backgrounds
            const lighterColor = SetupWizard.adjustColor(color, 40, true);
            document.documentElement.style.setProperty('--brand-color-light', lighterColor);
            
            // Update preview elements with smooth transition
            $('#preview-button, #preview-link').css('transition', 'all 0.3s ease');
            $('#preview-button').css('background-color', color);
            $('#preview-link').css('color', color);
        },
        
        /**
         * Adjust Color Brightness
         */
        adjustColor: function(color, percent, lighten = false) {
            const num = parseInt(color.replace('#', ''), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) + (lighten ? amt : -amt);
            const G = (num >> 8 & 0x00FF) + (lighten ? amt : -amt);
            const B = (num & 0x0000FF) + (lighten ? amt : -amt);
            
            return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
                (B < 255 ? B < 1 ? 0 : B : 255))
                .toString(16).slice(1);
        },
        
        /**
         * Update Site Title Preview
         */
        updateSiteTitlePreview: function() {
            const title = $(this).val().trim();
            $('#preview-name').text(title || 'Your Store');
        },
        
        /**
         * Add Button Feedback
         */
        addButtonFeedback: function($btn) {
            $btn.css({
                'transform': 'scale(0.97)',
                'transition': 'transform 0.1s ease'
            });
            
            setTimeout(function() {
                $btn.css('transform', 'scale(1)');
            }, 100);
        },
        
        /**
         * Show Notification
         */
        showNotification: function(type, message) {
            // Remove any existing notifications
            $('.setup-notification').remove();
            
            // Create notification element
            const icon = type === 'success' 
                ? '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="flex-shrink: 0;"><circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="2"/><path d="M6 10L9 13L14 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                : '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="flex-shrink: 0;"><circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="2"/><path d="M10 6v5M10 14h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
            
            const $notification = $('<div>')
                .addClass('setup-notification notification-' + type)
                .html('<div style="display: flex; align-items: center; gap: 0.75rem;">' + icon + '<span>' + message + '</span></div>')
                .appendTo('body');
            
            // Auto-remove after duration
            const duration = type === 'success' ? 3500 : 4500;
            setTimeout(function() {
                $notification.css({
                    'opacity': '0',
                    'transform': 'translateY(-12px) scale(0.95)',
                    'transition': 'all 0.3s cubic-bezier(0.4, 0, 1, 1)'
                });
                
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, duration);
        },
        
        /**
         * Add Input Focus Effects
         */
        addInputEffects: function() {
            $('.input-field').on('focus', function() {
                $(this).parent().addClass('input-focused');
            }).on('blur', function() {
                $(this).parent().removeClass('input-focused');
            });
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        SetupWizard.init();
        
        // Add smooth scroll behavior
        $('html').css('scroll-behavior', 'smooth');
        
        // Prevent accidental page unload during setup
        let setupInProgress = false;
        
        $('#create-pages-btn, #save-branding-btn, #complete-setup-btn').on('click', function() {
            setupInProgress = true;
        });
        
        $(window).on('beforeunload', function(e) {
            if (setupInProgress) {
                const message = 'Setup is in progress. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            }
        });
    });
    
})(jQuery);