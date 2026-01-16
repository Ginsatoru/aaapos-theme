/**
 * Customizer Live Preview - Simplified Brand Color System
 * 
 * Single brand color updates all accent elements in real-time.
 * 
 * @package aaapos-prime
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // =========================================================================
    // HELPER FUNCTIONS
    // =========================================================================

    /**
     * Update a CSS custom property
     */
    function updateCSSVariable(variable, value) {
        document.documentElement.style.setProperty('--' + variable, value);
    }

    /**
     * Convert hex to RGB string
     */
    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        return r + ', ' + g + ', ' + b;
    }

    /**
     * Adjust color brightness
     */
    function adjustBrightness(hex, percent) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        let r = parseInt(hex.substr(0, 2), 16);
        let g = parseInt(hex.substr(2, 2), 16);
        let b = parseInt(hex.substr(4, 2), 16);

        r = Math.max(0, Math.min(255, r + (r * percent / 100)));
        g = Math.max(0, Math.min(255, g + (g * percent / 100)));
        b = Math.max(0, Math.min(255, b + (b * percent / 100)));

        return '#' + 
            Math.round(r).toString(16).padStart(2, '0') +
            Math.round(g).toString(16).padStart(2, '0') +
            Math.round(b).toString(16).padStart(2, '0');
    }

    // =========================================================================
    // BRAND COLOR - The ONE color that controls everything
    // =========================================================================

    wp.customize('brand_color', function(value) {
        value.bind(function(brandColor) {
            // Calculate variants
            var brandHover = adjustBrightness(brandColor, -15);
            var brandDark = adjustBrightness(brandColor, -25);
            var brandLight = adjustBrightness(brandColor, 40);
            var brandRgb = hexToRgb(brandColor);

            // Update brand color variables
            updateCSSVariable('brand-color', brandColor);
            updateCSSVariable('brand-color-hover', brandHover);
            updateCSSVariable('brand-color-dark', brandDark);
            updateCSSVariable('brand-color-light', brandLight);
            updateCSSVariable('brand-color-rgb', brandRgb);

            // Update all mapped variables
            updateCSSVariable('color-primary', brandColor);
            updateCSSVariable('color-primary-hover', brandHover);
            updateCSSVariable('color-primary-dark', brandDark);
            updateCSSVariable('color-primary-light', brandLight);
            updateCSSVariable('color-primary-rgb', brandRgb);

            updateCSSVariable('color-secondary', brandColor);
            updateCSSVariable('color-secondary-hover', brandHover);
            updateCSSVariable('color-secondary-rgb', brandRgb);

            updateCSSVariable('color-accent', brandColor);
            updateCSSVariable('color-accent-hover', brandHover);
            updateCSSVariable('color-accent-rgb', brandRgb);

            updateCSSVariable('color-link', brandColor);
            updateCSSVariable('color-link-hover', brandHover);

            updateCSSVariable('color-button-primary-bg', brandColor);
            updateCSSVariable('color-button-primary-hover-bg', brandHover);
            updateCSSVariable('color-button-secondary-bg', brandColor);

            updateCSSVariable('color-focus-ring', brandColor);

            updateCSSVariable('color-topbar-bg', brandColor);
            updateCSSVariable('color-topbar-bg-end', brandHover);
            updateCSSVariable('color-cart-badge', brandColor);

            updateCSSVariable('color-woo-price', brandColor);
            updateCSSVariable('color-woo-add-to-cart-bg', brandColor);
            updateCSSVariable('color-woo-add-to-cart-hover', brandHover);

            // Update gradients
            updateCSSVariable('gradient-primary', 'linear-gradient(90deg, ' + brandDark + ', ' + brandColor + ', ' + brandHover + ')');
            updateCSSVariable('gradient-topbar', 'linear-gradient(90deg, ' + brandColor + ', ' + brandHover + ')');
            updateCSSVariable('gradient-button', 'linear-gradient(135deg, ' + brandColor + ', ' + brandHover + ')');

            // Direct DOM updates for instant feedback
            $('.top-bar').css('background', 'linear-gradient(90deg, ' + brandColor + ', ' + brandHover + ')');
            $('.cart-count').css('background-color', brandColor);
            $('.hero-badge').css('background-color', brandColor);
            $('.hero-btn-primary').css('background-color', brandColor);
            $('.btn--primary, .btn-primary').css('background-color', brandColor);
            $('.add_to_cart_button, .single_add_to_cart_button').css('background-color', brandColor);
            $('.price, .woocommerce-Price-amount').not('.price del .woocommerce-Price-amount').css('color', brandColor);
        });
    });

    // =========================================================================
    // NAVIGATION ACCENT COLOR (Separate control)
    // =========================================================================

    wp.customize('nav_accent_color', function(value) {
        value.bind(function(navColor) {
            updateCSSVariable('color-nav-hover', navColor);
            updateCSSVariable('color-nav-active', navColor);
            updateCSSVariable('color-nav-underline', navColor);

            // Update nav underline pseudo-element
            var styleId = 'customizer-nav-underline';
            $('#' + styleId).remove();
            $('head').append(
                '<style id="' + styleId + '">' +
                '.nav-menu > li > a::after { background: ' + navColor + ' !important; }' +
                '.nav-menu > li > a:hover, .nav-menu .current-menu-item > a { color: ' + navColor + ' !important; }' +
                '</style>'
            );
        });
    });

    // =========================================================================
    // EXISTING CONTROLS (Keep for other customizer features)
    // =========================================================================

    // Footer Description
    wp.customize('footer_description', function(value) {
        value.bind(function(newval) {
            $('.footer-description').text(newval);
        });
    });

    // Footer Address
    wp.customize('footer_address', function(value) {
        value.bind(function(newval) {
            $('.contact-address span').text(newval);
        });
    });

    // Footer Phone
    wp.customize('footer_phone', function(value) {
        value.bind(function(newval) {
            $('.contact-phone a').text(newval);
        });
    });

    // Footer Email
    wp.customize('footer_email', function(value) {
        value.bind(function(newval) {
            $('.contact-email a').text(newval);
        });
    });

    // Footer Copyright
    wp.customize('footer_copyright_text', function(value) {
        value.bind(function(newval) {
            var currentYear = new Date().getFullYear();
            var siteName = $('title').text().split('|')[0].trim();
            var text = newval.replace('{year}', currentYear).replace('{sitename}', siteName);
            $('.footer-copyright p').html(text);
        });
    });

    // Show/Hide controls
    wp.customize('footer_show_logo', function(value) {
        value.bind(function(newval) {
            newval ? $('.footer-logo').fadeIn(300) : $('.footer-logo').fadeOut(300);
        });
    });

    wp.customize('footer_show_social', function(value) {
        value.bind(function(newval) {
            newval ? $('.footer-social-links').fadeIn(300) : $('.footer-social-links').fadeOut(300);
        });
    });

    wp.customize('footer_show_menu', function(value) {
        value.bind(function(newval) {
            newval ? $('.footer-bottom-nav').fadeIn(300) : $('.footer-bottom-nav').fadeOut(300);
        });
    });

    wp.customize('footer_show_back_to_top', function(value) {
        value.bind(function(newval) {
            newval ? $('.back-to-top').addClass('visible') : $('.back-to-top').removeClass('visible');
        });
    });

    wp.customize('footer_back_to_top_position', function(value) {
        value.bind(function(newval) {
            $('.back-to-top').removeClass('back-to-top-left back-to-top-right').addClass('back-to-top-' + newval);
        });
    });

    wp.customize('footer_width', function(value) {
        value.bind(function(newval) {
            $('.site-footer').removeClass('footer-width-boxed footer-width-full-width').addClass('footer-width-' + newval);
        });
    });

    wp.customize('social_icon_style', function(value) {
        value.bind(function(newval) {
            $('.social-links').removeClass('social-style-circle social-style-rounded social-style-square social-style-minimal').addClass('social-style-' + newval);
        });
    });

    // Social URLs
    ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'pinterest', 'tiktok', 'whatsapp'].forEach(function(platform) {
        wp.customize('social_' + platform, function(value) {
            value.bind(function(newval) {
                var $link = $('.social-' + platform);
                newval ? $link.attr('href', newval).show() : $link.hide();
            });
        });
    });

    // Section visibility
    wp.customize('show_top_bar', function(value) {
        value.bind(function(newval) {
            newval ? $('.top-bar').slideDown(300) : $('.top-bar').slideUp(300);
        });
    });

    wp.customize('show_search_bar', function(value) {
        value.bind(function(newval) {
            if (newval) {
                $('.header-search-bar, .mobile-search-wrapper').fadeIn(300);
            } else {
                $('.header-search-bar, .mobile-search-wrapper').fadeOut(300);
            }
        });
    });

    // Topbar content
    wp.customize('topbar_phone', function(value) {
        value.bind(function(newval) {
            $('.top-bar-left a[href^="tel:"]').find('span').text(newval);
        });
    });

    wp.customize('topbar_email', function(value) {
        value.bind(function(newval) {
            $('.top-bar-left a[href^="mailto:"]').find('span').text(newval);
        });
    });

    wp.customize('topbar_promo_text', function(value) {
        value.bind(function(newval) {
            $('.promo-text span').text(newval);
        });
    });

    // Hero section
    wp.customize('show_hero', function(value) {
        value.bind(function(newval) {
            newval ? $('.hero-section').slideDown(300) : $('.hero-section').slideUp(300);
        });
    });

    wp.customize('hero_badge_text', function(value) {
        value.bind(function(newval) {
            $('.hero-badge').text(newval);
        });
    });

    wp.customize('hero_title', function(value) {
        value.bind(function(newval) {
            $('.hero-title').contents().filter(function() {
                return this.nodeType === 3;
            }).first().replaceWith(newval + ' ');
        });
    });

    wp.customize('hero_title_highlight', function(value) {
        value.bind(function(newval) {
            $('.hero-title-highlight').text(newval);
        });
    });

    wp.customize('hero_subtitle', function(value) {
        value.bind(function(newval) {
            $('.hero-subtitle').text(newval);
        });
    });

    wp.customize('hero_primary_button_text', function(value) {
        value.bind(function(newval) {
            $('.hero-btn-primary').contents().filter(function() {
                return this.nodeType === 3;
            }).first().replaceWith(newval + ' ');
        });
    });

    wp.customize('hero_primary_button_link', function(value) {
        value.bind(function(newval) {
            $('.hero-btn-primary').attr('href', newval);
        });
    });

    wp.customize('hero_secondary_button_text', function(value) {
        value.bind(function(newval) {
            $('.hero-btn-secondary').contents().filter(function() {
                return this.nodeType === 3;
            }).first().replaceWith(newval + ' ');
        });
    });

    wp.customize('hero_secondary_button_link', function(value) {
        value.bind(function(newval) {
            $('.hero-btn-secondary').attr('href', newval);
        });
    });

    // Trust indicators
    ['1', '2', '3'].forEach(function(num) {
        wp.customize('hero_trust_' + num + '_number', function(value) {
            value.bind(function(newval) {
                $('.hero-trust-indicators .trust-item:nth-child(' + num + ') .trust-number').text(newval);
            });
        });
        wp.customize('hero_trust_' + num + '_label', function(value) {
            value.bind(function(newval) {
                $('.hero-trust-indicators .trust-item:nth-child(' + num + ') .trust-label').text(newval);
            });
        });
    });

    // Homepage sections
    wp.customize('featured_products_title', function(value) {
        value.bind(function(newval) {
            $('.featured-products .section-title').text(newval);
        });
    });

    wp.customize('show_featured_products', function(value) {
        value.bind(function(newval) {
            newval ? $('.featured-products').show() : $('.featured-products').hide();
        });
    });

    wp.customize('categories_title', function(value) {
        value.bind(function(newval) {
            $('.product-categories .section-title').text(newval);
        });
    });

    wp.customize('show_categories', function(value) {
        value.bind(function(newval) {
            newval ? $('.product-categories').show() : $('.product-categories').hide();
        });
    });

    wp.customize('deals_title', function(value) {
        value.bind(function(newval) {
            $('.deals-offers .section-title').text(newval);
        });
    });

    wp.customize('show_deals', function(value) {
        value.bind(function(newval) {
            newval ? $('.deals-offers').show() : $('.deals-offers').hide();
        });
    });

    wp.customize('testimonials_title', function(value) {
        value.bind(function(newval) {
            $('.testimonials .section-title').text(newval);
        });
    });

    wp.customize('show_testimonials', function(value) {
        value.bind(function(newval) {
            newval ? $('.testimonials').show() : $('.testimonials').hide();
        });
    });

    wp.customize('blog_title', function(value) {
        value.bind(function(newval) {
            $('.blog-preview .section-title').text(newval);
        });
    });

    wp.customize('show_blog', function(value) {
        value.bind(function(newval) {
            newval ? $('.blog-preview').show() : $('.blog-preview').hide();
        });
    });

    wp.customize('newsletter_title', function(value) {
        value.bind(function(newval) {
            $('.newsletter .section-title').text(newval);
        });
    });

    wp.customize('newsletter_description', function(value) {
        value.bind(function(newval) {
            $('.newsletter .section-description').text(newval);
        });
    });

    wp.customize('show_newsletter', function(value) {
        value.bind(function(newval) {
            newval ? $('.newsletter').show() : $('.newsletter').hide();
        });
    });

    // Controls requiring refresh
    ['featured_products_count', 'categories_count', 'deal_product_id', 'deal_end_date', 'blog_posts_count'].forEach(function(control) {
        wp.customize(control, function(value) {
            value.bind(function() {
                wp.customize.previewer.refresh();
            });
        });
    });

})(jQuery);